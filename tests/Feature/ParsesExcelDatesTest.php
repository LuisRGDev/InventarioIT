<?php

namespace Tests\Feature;

use App\Imports\DevicesImport;
use App\Models\Device;
use App\Models\DeviceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Cubre el trait App\Imports\Concerns\ParsesExcelDates, consolidado en la
 * Fase 2 (Hallazgo Alto H7) a partir de 4 copias casi idénticas. Se prueba
 * a través de DevicesImport (uno de sus 4 consumidores) para verificar el
 * comportamiento real de principio a fin, no solo la unidad aislada.
 */
class ParsesExcelDatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_parses_dd_mm_yyyy_dates(): void
    {
        DeviceCategory::where('slug', 'desktop')->firstOrFail();

        $rows = new Collection([
            [
                'categoria' => 'Desktop',
                'marca' => 'HP',
                'modelo' => 'ProDesk 400',
                'numero_de_serie' => 'SN-DATE-TEST-001',
                'fecha_compra' => '15/03/2023',
            ],
        ]);

        (new DevicesImport)->collection($rows);

        $device = Device::where('serial_number', 'SN-DATE-TEST-001')->firstOrFail();

        $this->assertSame('2023-03-15', $device->purchase_date->format('Y-m-d'));
    }

    public function test_treats_na_as_null_instead_of_a_parse_error(): void
    {
        DeviceCategory::where('slug', 'desktop')->firstOrFail();

        $rows = new Collection([
            [
                'categoria' => 'Desktop',
                'marca' => 'HP',
                'modelo' => 'ProDesk 400',
                'numero_de_serie' => 'SN-DATE-TEST-002',
                'fecha_compra' => 'N/A',
            ],
        ]);

        (new DevicesImport)->collection($rows);

        $device = Device::where('serial_number', 'SN-DATE-TEST-002')->firstOrFail();

        $this->assertNull($device->purchase_date);
    }
}
