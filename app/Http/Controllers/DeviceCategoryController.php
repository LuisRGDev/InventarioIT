<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceCategoryRequest;
use App\Http\Requests\UpdateDeviceCategoryRequest;
use App\Models\DeviceCategory;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreDeviceCategoryRequest $request): RedirectResponse
    {
        DeviceCategory::create($request->validated());

        return redirect()->route('device-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function update(UpdateDeviceCategoryRequest $request, DeviceCategory $deviceCategory): RedirectResponse
    {
        $deviceCategory->update($request->validated());

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
