<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre Device::warranty_is_active / warranty_expires_soon tras
 * deduplicar getWarrantyExpiresSoonAttribute() (Hallazgo Bajo L2 de la
 * auditoría) para que reutilice warranty_is_active en vez de repetir el
 * mismo chequeo de null/isFuture().
 */
class DeviceWarrantyAccessorsTest extends TestCase
{
    use RefreshDatabase;

    protected function aDevice(array $attributes = []): Device
    {
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();

        return Device::factory()->disponible()->create(array_merge([
            'device_category_id' => $category->id,
            'device_model_id' => null,
        ], $attributes));
    }

    public function test_warranty_is_active_is_false_without_an_expiration_date(): void
    {
        $device = $this->aDevice(['warranty_expires_at' => null]);

        $this->assertFalse($device->warranty_is_active);
        $this->assertFalse($device->warranty_expires_soon);
    }

    public function test_warranty_is_active_is_false_once_expired(): void
    {
        $device = $this->aDevice(['warranty_expires_at' => now()->subDay()]);

        $this->assertFalse($device->warranty_is_active);
        $this->assertFalse($device->warranty_expires_soon);
    }

    public function test_warranty_expires_soon_is_true_within_the_warning_window(): void
    {
        $device = $this->aDevice(['warranty_expires_at' => now()->addDays(10)]);

        $this->assertTrue($device->warranty_is_active);
        $this->assertTrue($device->warranty_expires_soon);
    }

    public function test_warranty_expires_soon_is_false_far_in_the_future(): void
    {
        $device = $this->aDevice(['warranty_expires_at' => now()->addYears(2)]);

        $this->assertTrue($device->warranty_is_active);
        $this->assertFalse($device->warranty_expires_soon);
    }
}
