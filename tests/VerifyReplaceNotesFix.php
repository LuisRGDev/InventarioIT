<?php

/**
 * Test de verificación del fix: ReplaceDevicePage pasa notes al service.
 *
 * Ejecutar: php tests/VerifyReplaceNotesFix.php
 * No requiere mbstring ni Laravel.
 */

$passed = 0;
$failed = 0;

function assert_contains(string $file, string $needle, string $description): void
{
    global $passed, $failed;
    $content = file_get_contents($file);
    if (str_contains($content, $needle)) {
        echo "  ✓ {$description}\n";
        $passed++;
    } else {
        echo "  ✗ {$description}\n";
        echo "    No se encontró: \"{$needle}\"\n";
        $failed++;
    }
}

function assert_not_contains(string $file, string $needle, string $description): void
{
    global $passed, $failed;
    $content = file_get_contents($file);
    if (!str_contains($file, $needle) || !str_contains($content, $needle)) {
        echo "  ✓ {$description}\n";
        $passed++;
    } else {
        echo "  ✗ {$description}\n";
        echo "    Se encontró: \"{$needle}\"\n";
        $failed++;
    }
}

function assert_file_not_exists(string $path, string $description): void
{
    global $passed, $failed;
    if (!file_exists($path)) {
        echo "  ✓ {$description}\n";
        $passed++;
    } else {
        echo "  ✗ {$description}\n";
        echo "    El archivo aún existe: {$path}\n";
        $failed++;
    }
}

$base = __DIR__ . '/..';
$livewire = $base . '/app/Livewire/ReplaceDevicePage.php';
$service = $base . '/app/Services/DeviceAssignmentService.php';
$blade = $base . '/resources/views/livewire/replace-device-page.blade.php';

echo "=== Test: Fix de notas en reemplazo de equipos ===\n\n";

// ── 1. ReplaceDevicePage tiene las propiedades ──
echo "[1] Propiedades en ReplaceDevicePage.php\n";
assert_contains($livewire, 'public string $returnNotes', 'Propiedad $returnNotes existe');
assert_contains($livewire, 'public string $assignNotes', 'Propiedad $assignNotes existe');

// ── 2. ReplaceDevicePage pasa las notas al service ──
echo "\n[2] ReplaceDevicePage pasa notas al service\n";
assert_contains($livewire, "'return_notes'          => \$this->returnNotes", 'return_notes se pasa correctamente');
assert_contains($livewire, "'assign_notes'          => \$this->assignNotes", 'assign_notes se pasa correctamente');

// ── 3. Service espera las claves correctas ──
echo "\n[3] DeviceAssignmentService espera las claves\n";
assert_contains($service, "\$data['return_notes']", 'Service espera return_notes');
assert_contains($service, "\$data['assign_notes']", 'Service espera assign_notes');

// ── 4. Blade tiene los textareas ──
echo "\n[4] Vista Blade tiene textareas de notas\n";
assert_contains($blade, 'wire:model="returnNotes"', 'Textarea de returnNotes en Blade');
assert_contains($blade, 'wire:model="assignNotes"', 'Textarea de assignNotes en Blade');
assert_contains($blade, 'Notas de devolución', 'Label de notas de devolución');
assert_contains($blade, 'Notas de asignación', 'Label de notas de asignación');

// ── 5. Código muerto eliminado ──
echo "\n[5] FormRequests muertos eliminados\n";
assert_file_not_exists($base . '/app/Http/Requests/AssignDeviceRequest.php', 'AssignDeviceRequest.php eliminado');
assert_file_not_exists($base . '/app/Http/Requests/ReturnDeviceRequest.php', 'ReturnDeviceRequest.php eliminado');
assert_file_not_exists($base . '/app/Http/Requests/ReplaceDeviceRequest.php', 'ReplaceDeviceRequest.php eliminado');

// ── 6. No se rompió nada existente ──
echo "\n[6] Integridad del código existente\n";
assert_contains($livewire, 'condition_on_return', 'condition_on_return preservado');
assert_contains($livewire, 'condition_on_delivery', 'condition_on_delivery preservado');
assert_contains($livewire, 'old_device_new_status', 'old_device_new_status preservado');
assert_contains($service, 'lockForUpdate', 'lockForUpdate preservado en service');

// ── Resultado ──
echo "\n" . str_repeat('─', 50) . "\n";
$total = $passed + $failed;
echo "Resultado: {$passed}/{$total} pruebas pasaron";
if ($failed > 0) {
    echo " ({$failed} fallaron)";
}
echo "\n";

exit($failed > 0 ? 1 : 0);
