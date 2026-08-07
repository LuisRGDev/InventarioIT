<?php

namespace App\Http\Controllers;

use App\Enums\ExtensionStatus;
use App\Models\OfficeExtension;
use App\Imports\OfficeExtensionsImport;
use App\Http\Requests\StoreOfficeExtensionRequest;
use App\Http\Requests\UpdateOfficeExtensionRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfficeExtensionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $extensions = OfficeExtension::with('currentAssignment.employee')
            ->when($search, function ($query, $search) {
                return $query->where('extension_number', 'like', "%{$search}%")
                             ->orWhere('direct_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('office-extensions.index', compact('extensions', 'search'));
    }

    public function create(): View
    {
        $statuses = ExtensionStatus::cases();
        return view('office-extensions.create', compact('statuses'));
    }

    public function store(StoreOfficeExtensionRequest $request): RedirectResponse
    {
        OfficeExtension::create($request->validated());

        return redirect()->route('office-extensions.index')
            ->with('success', 'Extensión telefónica creada exitosamente.');
    }

    public function edit(OfficeExtension $officeExtension): View
    {
        $statuses = ExtensionStatus::cases();
        return view('office-extensions.edit', compact('officeExtension', 'statuses'));
    }

    public function update(UpdateOfficeExtensionRequest $request, OfficeExtension $officeExtension): RedirectResponse
    {
        $officeExtension->update($request->validated());

        return redirect()->route('office-extensions.index')
            ->with('success', 'Extensión telefónica actualizada exitosamente.');
    }

    public function destroy(OfficeExtension $officeExtension): RedirectResponse
    {
        // Hard delete
        $officeExtension->assignments()->delete();
        $officeExtension->delete();

        return redirect()->route('office-extensions.index')
            ->with('success', 'Extensión telefónica eliminada correctamente.');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = ['numero_de_extension', 'numero_directo', 'estatus', 'correo'];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            
            // BOM to force UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers);
            // Example row
            fputcsv($file, ['101', '5551234567', 'asignada', 'ejemplo@empresa.com']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_extensiones.csv"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ], [
            'file.required' => 'Debe seleccionar un archivo para importar.',
            'file.mimes'    => 'El archivo debe ser un Excel o CSV válido.',
        ]);

        try {
            Excel::import(new OfficeExtensionsImport, $request->file('file'));
            return redirect()->route('office-extensions.index')->with('success', 'Extensiones importadas correctamente.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->route('office-extensions.index')->with('error', 'Error de validación:<br>' . implode('<br>', $messages));
        } catch (\Exception $e) {
            return redirect()->route('office-extensions.index')->with('error', 'Ocurrió un error al importar el archivo: ' . $e->getMessage());
        }
    }
}
