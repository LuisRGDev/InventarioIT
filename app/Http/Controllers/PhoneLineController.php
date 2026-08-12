<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoneLineRequest;
use App\Http\Requests\UpdatePhoneLineRequest;
use App\Models\PhoneLine;
use Illuminate\Http\Request;
use App\Exports\PhoneLinesExport;
use App\Exports\PhoneLinesTemplateExport;
use App\Imports\PhoneLinesImport;
use Maatwebsite\Excel\Facades\Excel;

class PhoneLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PhoneLine::with('currentAssignment.employee');

        // Búsqueda simple
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('number', 'like', "%{$search}%")
                  ->orWhere('data_plan', 'like', "%{$search}%")
                  ->orWhereHas('currentAssignment.employee', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        // Filtro por estatus
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $phoneLines = $query->latest()->paginate(15)->withQueryString();

        return view('phone-lines.index', compact('phoneLines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('phone-lines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePhoneLineRequest $request)
    {
        PhoneLine::create($request->validated());

        return redirect()->route('phone-lines.index')
            ->with('success', 'Línea telefónica registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PhoneLine $phoneLine)
    {
        $phoneLine->load(['assignments.employee', 'currentAssignment.employee']);
        
        return view('phone-lines.show', compact('phoneLine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhoneLine $phoneLine)
    {
        return view('phone-lines.edit', compact('phoneLine'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePhoneLineRequest $request, PhoneLine $phoneLine)
    {
        $phoneLine->update($request->validated());

        return redirect()->route('phone-lines.show', $phoneLine)
            ->with('success', 'Línea telefónica actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhoneLine $phoneLine)
    {
        // Verificar si tiene asignación activa
        if ($phoneLine->currentAssignment) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar la línea porque tiene una asignación activa.');
        }

        $phoneLine->delete();

        return redirect()->route('phone-lines.index')
            ->with('success', 'Línea telefónica eliminada correctamente.');
    }
    /**
     * Display the history of assignments for the specified resource.
     */
    public function history(PhoneLine $phoneLine)
    {
        $assignments = $phoneLine->assignments()->with('employee')->orderBy('assigned_at', 'desc')->get();

        return view('phone-lines.history', compact('phoneLine', 'assignments'));
    }

    /**
     * Export the phone lines to an Excel file.
     */
    public function export()
    {
        return Excel::download(new PhoneLinesExport, 'directorio_lineas_telefonicas_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PhoneLinesTemplateExport, 'plantilla_importacion_lineas.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Debes subir un archivo.',
            'file.mimes'    => 'El archivo debe ser un Excel (.xlsx, .xls o .csv).',
            'file.max'      => 'El archivo no debe pesar más de 5MB.'
        ]);

        try {
            Excel::import(new PhoneLinesImport, $request->file('file'));
            
            return redirect()->route('phone-lines.index')
                ->with('success', 'Importación de líneas telefónicas completada correctamente.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Fila {$row}: {$errors}";
            }
            return back()->with('error', 'Errores de validación en el archivo: <br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error inesperado al importar el archivo: ' . $e->getMessage());
        }
    }
}
