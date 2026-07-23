<?php

namespace App\Http\Controllers;

use App\Models\DeviceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceCategoryController extends Controller
{
    public function index(): View
    {
        $categories = DeviceCategory::withCount('devices')
            ->orderBy('name')
            ->paginate(20);

        return view('device-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:device_categories,name'],
            'slug'        => ['nullable', 'string', 'max:100', 'unique:device_categories,slug'],
            'description' => ['nullable', 'string'],
        ]);

        DeviceCategory::create($validated);

        return redirect()->route('device-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, DeviceCategory $deviceCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', "unique:device_categories,name,{$deviceCategory->id}"],
            'description' => ['nullable', 'string'],
        ]);

        $deviceCategory->update($validated);

        return redirect()->route('device-categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(DeviceCategory $deviceCategory): RedirectResponse
    {
        if ($deviceCategory->devices()->exists()) {
            return back()->with('error', 'No se puede eliminar una categoría con equipos asociados.');
        }

        $deviceCategory->delete();

        return redirect()->route('device-categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
