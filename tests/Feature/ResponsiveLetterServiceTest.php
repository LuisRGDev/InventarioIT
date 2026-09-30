<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Models\PhoneLine;
use App\Models\User;
use App\Services\DeviceAssignmentService;
use App\Services\PhoneLineAssignmentService;
use App\Services\ResponsiveLetterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

/**
 * Cubre la generación de la carta responsiva con el formato real de
 * Middleby (membrete, fecha, lista de equipo entregado, cláusulas y
 * firma), reemplazando el texto genérico "CARTA RESPONSIVA" que tenía
 * antes el servicio.
 */
class ResponsiveLetterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function extractDocumentXmlText(string $docxPath): string
    {
        $zip = new ZipArchive;
        $zip->open($docxPath);
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $xml, $matches);

        return implode(' ', $matches[1]);
    }

    public function test_generates_letter_with_charger_line_for_a_laptop(): void
    {
        $category = DeviceCategory::where('slug', 'portatil')->firstOrFail();
        $device = Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => null,
            'brand' => 'Dell',
            'model' => 'Latitude 5420',
            'serial_number' => 'SN-TEST-123',
            'imei' => null,
        ]);
        $employee = Employee::factory()->activo()->create(['name' => 'Juan Pérez']);
        $assignedBy = User::factory()->create(['name' => 'Luis Rosales']);
        $this->actingAs($assignedBy);

        $assignment = app(DeviceAssignmentService::class)->assign($device, $employee);

        $filePath = app(ResponsiveLetterService::class)->generate($assignment->fresh());

        $this->assertFileExists($filePath);

        $text = $this->extractDocumentXmlText($filePath);

        $this->assertStringContainsString('Tlalnepantla de Baz, Estado de México,', $text);
        $this->assertStringContainsString('Dell Latitude 5420', $text);
        $this->assertStringContainsString('SN-TEST-123', $text);
        $this->assertStringContainsString('Cargador', $text);
        $this->assertStringContainsString('Juan Pérez', $text);
        $this->assertStringContainsString('Entrego: Luis Rosales', $text);

        unlink($filePath);
    }

    public function test_omits_charger_line_for_a_device_category_without_one(): void
    {
        $category = DeviceCategory::factory()->create(['slug' => 'monitor-'.uniqid(), 'name' => 'Monitor']);
        $device = Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => null,
        ]);
        $employee = Employee::factory()->activo()->create();

        $assignment = app(DeviceAssignmentService::class)->assign($device, $employee);

        $filePath = app(ResponsiveLetterService::class)->generate($assignment->fresh());
        $text = $this->extractDocumentXmlText($filePath);

        $this->assertStringNotContainsString('Cargador', $text);

        unlink($filePath);
    }

    public function test_includes_phone_number_line_when_employee_has_an_active_line(): void
    {
        $category = DeviceCategory::where('slug', 'smartphone')->firstOrFail();
        $device = Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => null,
            'imei' => '123456789012345',
        ]);
        $employee = Employee::factory()->activo()->create();

        $phoneLine = PhoneLine::factory()->disponible()->create(['number' => '5511223344']);
        app(PhoneLineAssignmentService::class)->assign($phoneLine, $employee);

        $assignment = app(DeviceAssignmentService::class)->assign($device, $employee);

        $filePath = app(ResponsiveLetterService::class)->generate($assignment->fresh());
        $text = $this->extractDocumentXmlText($filePath);

        $this->assertStringContainsString('123456789012345', $text);
        $this->assertStringContainsString('5511223344', $text);

        unlink($filePath);
    }
}
