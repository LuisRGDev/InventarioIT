<?php

namespace Tests\Feature;

use App\Exports\DevicesExport;
use App\Exports\EmployeesExport;
use App\Exports\MaintenancesExport;
use App\Exports\OfficeExtensionsExport;
use App\Exports\PhoneLinesExport;
use App\Exports\Sheets\AssignedDevicesSheet;
use App\Exports\Sheets\EmployeesSheet;
use App\Exports\Sheets\GlobalEmployeesInventorySheet;
use App\Exports\Sheets\UnassignedDevicesSheet;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Excel as ExcelWriterType;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/**
 * Regresión del Hallazgo Alto H9: WithChunkReading no tenía ningún efecto
 * en exports que implementaban FromCollection, porque collection() ya
 * materializaba toda la tabla en memoria con ->get() antes de que el
 * chunking pudiera aplicar. Se migraron a FromQuery (que sí soporta
 * chunking real vía Builder::chunk()). Este test confirma dos cosas: que
 * cada clase declara FromQuery, y que el archivo exportado sigue trayendo
 * los datos y relaciones esperadas tras el cambio.
 */
class ExportUsesQueryChunkingTest extends TestCase
{
    use RefreshDatabase;

    protected function readCellValue(string $binary, int $col, int $row = 2)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_test_').'.xlsx';
        file_put_contents($tmpFile, $binary);

        $spreadsheet = IOFactory::load($tmpFile);
        $value = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();

        unlink($tmpFile);

        return $value;
    }

    public function test_all_mapped_exports_implement_from_query(): void
    {
        foreach ([
            DevicesExport::class,
            EmployeesExport::class,
            MaintenancesExport::class,
            OfficeExtensionsExport::class,
            PhoneLinesExport::class,
            AssignedDevicesSheet::class,
            EmployeesSheet::class,
            GlobalEmployeesInventorySheet::class,
            UnassignedDevicesSheet::class,
        ] as $exportClass) {
            $this->assertInstanceOf(
                FromQuery::class,
                new $exportClass,
                "{$exportClass} debe implementar FromQuery para que WithChunkReading tenga efecto real."
            );
        }
    }

    public function test_devices_export_still_includes_assigned_employee_via_eager_loaded_relation(): void
    {
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();
        $device = Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => null,
            'brand' => 'Dell',
            'model' => 'OptiPlex Query Test',
        ]);
        $employee = Employee::factory()->activo()->create(['name' => 'Empleado Query Test']);
        app(DeviceAssignmentService::class)->assign($device, $employee);

        $binary = Excel::raw(new DevicesExport, ExcelWriterType::XLSX);

        // "Empleado Asignado" es la columna 3 (1-based) en DevicesExport::headings().
        $this->assertSame('Empleado Query Test', $this->readCellValue($binary, 3));
    }
}
