<?php

namespace Tests\Feature;

use App\Imports\DevicesImport;
use App\Models\Device;
use App\Models\DeviceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Regresión del Hallazgo Crítico C6: DevicesImport reutilizaba el IMEI de
 * una fila anterior en filas posteriores sin IMEI, porque la variable
 * $imei nunca se reseteaba dentro del foreach.
 */
class DevicesImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_row_without_imei_does_not_inherit_previous_rows_imei(): void
    {
        // La migración 2026_07_24_202713_cleanup_device_categories_and_rename_laptop
        // ya garantiza (vía firstOrCreate) que "Portátil"/"Desktop"/"Smartphone"
        // existan en cada base de datos migrada; se reutiliza en vez de crear
        // una nueva para no colisionar con ese slug único.
        DeviceCategory::where('slug', 'portatil')->firstOrFail();

        $rows = new Collection([
            [
                'categoria' => 'Portátil',
                'marca' => 'Dell',
                'modelo' => 'Latitude 5540',
                'numero_de_serie' => 'SN-IMEI-TEST-001',
                'imei' => '123456789012345',
            ],
            [
                'categoria' => 'Portátil',
                'marca' => 'HP',
                'modelo' => 'EliteBook 840',
                'numero_de_serie' => 'SN-IMEI-TEST-002',
                'imei' => null,
            ],
        ]);

        (new DevicesImport)->collection($rows);

        $first = Device::where('serial_number', 'SN-IMEI-TEST-001')->firstOrFail();
        $second = Device::where('serial_number', 'SN-IMEI-TEST-002')->firstOrFail();

        $this->assertSame('123456789012345', $first->imei);
        $this->assertNull($second->imei, 'La fila sin IMEI no debe heredar el IMEI de la fila anterior.');
    }
}
