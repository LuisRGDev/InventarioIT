<?php

namespace App\Http\Controllers;

use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceModelController extends Controller
{
    /**
     * Muestra el catálogo principal de estándares de hardware y modelos predefinidos.
     */
    public function index(Request $request): View
    {
        $query = DeviceModel::with('category')->withCount('devices');

        // Filtrado opcional por categoría o búsqueda de texto
        if ($request->filled('category')) {
            $query->where('device_category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('variant', 'like', "%{$search}%")
                  ->orWhere('cpu', 'like', "%{$search}%");
            });
        }

        $models = $query->orderBy('brand')
            ->orderBy('model')
            ->orderBy('variant')
            ->paginate(15)
            ->withQueryString();

        $categories = DeviceCategory::orderBy('name')->get();

        return view('device-models.index', compact('models', 'categories'));
    }

    /**
     * Muestra el formulario para dar de alta una nueva plantilla o configuración de hardware.
     */
    public function create(): View
    {
        $categories = DeviceCategory::orderBy('name')->get();
        return view('device-models.create', compact('categories'));
    }

    /**
     * Almacena un nuevo modelo/estándar de hardware en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'device_category_id' => ['required', 'exists:device_categories,id'],
            'brand'              => ['required', 'string', 'max:100'],
            'model'              => ['required', 'string', 'max:150'],
            'variant'            => ['nullable', 'string', 'max:150'],
            'cpu'                => ['nullable', 'string', 'max:255'],
            'ram'                => ['nullable', 'string', 'max:100'],
            'storage'            => ['nullable', 'string', 'max:150'],
            'os'                 => ['nullable', 'string', 'max:150'],
            'notes'              => ['nullable', 'string'],
        ], [
            'device_category_id.required' => 'Debes seleccionar una categoría para este estándar de hardware.',
            'brand.required'              => 'La marca (ej. Dell, Apple, HP) es obligatoria.',
            'model.required'              => 'El nombre o número de modelo es obligatorio.',
        ]);

        DeviceModel::create($validated);

        return redirect()->route('device-models.index')
            ->with('success', '¡Modelo y estándar de hardware registrado y disponible para el inventario con éxito!');
    }

    /**
     * Muestra el formulario para editar un modelo corporativo predefinido.
     */
    public function edit(DeviceModel $deviceModel): View
    {
        $categories = DeviceCategory::orderBy('name')->get();
        return view('device-models.edit', compact('deviceModel', 'categories'));
    }

    /**
     * Actualiza un estándar corporativo existente.
     */
    public function update(Request $request, DeviceModel $deviceModel): RedirectResponse
    {
        $validated = $request->validate([
            'device_category_id' => ['required', 'exists:device_categories,id'],
            'brand'              => ['required', 'string', 'max:100'],
            'model'              => ['required', 'string', 'max:150'],
            'variant'            => ['nullable', 'string', 'max:150'],
            'cpu'                => ['nullable', 'string', 'max:255'],
            'ram'                => ['nullable', 'string', 'max:100'],
            'storage'            => ['nullable', 'string', 'max:150'],
            'os'                 => ['nullable', 'string', 'max:150'],
            'notes'              => ['nullable', 'string'],
        ], [
            'device_category_id.required' => 'Debes seleccionar una categoría para este estándar.',
            'brand.required'              => 'La marca del modelo es obligatoria.',
            'model.required'              => 'El nombre o número del modelo es obligatorio.',
        ]);

        $deviceModel->update($validated);

        return redirect()->route('device-models.index')
            ->with('success', '¡Estándar de hardware modificado y actualizado correctamente!');
    }

    /**
     * Elimina una plantilla del catálogo si no hay equipos físicos usándola actualmente.
     */
    public function destroy(DeviceModel $deviceModel): RedirectResponse
    {
        if ($deviceModel->devices()->count() > 0) {
            return back()->with('error', "No puedes eliminar este modelo porque actualmente hay {$deviceModel->devices()->count()} equipo(s) inventariado(s) utilizándolo. Edita el registro o reasigna los equipos primero.");
        }

        $deviceModel->delete();

        return redirect()->route('device-models.index')
            ->with('success', 'El modelo de hardware ha sido retirado del catálogo.');
    }
}
