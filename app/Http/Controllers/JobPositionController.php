<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobPositionController extends Controller
{
    public function index(Request $request): View
    {
        $query = JobPosition::withCount('employees');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('direction', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $positions = $query->orderBy('direction')
            ->orderBy('area')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('job-positions.index', compact('positions'));
    }

    public function create(): View
    {
        return view('job-positions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['required', 'string', 'max:100'],
            'area'      => ['required', 'string', 'max:100'],
            'name'      => ['required', 'string', 'max:100'],
            'notes'     => ['nullable', 'string'],
        ], [
            'direction.required' => 'La dirección es obligatoria.',
            'area.required'      => 'El área es obligatoria.',
            'name.required'      => 'El puesto es obligatorio.',
        ]);

        JobPosition::create($validated);

        return redirect()->route('job-positions.index')
            ->with('success', 'Puesto registrado con éxito.');
    }

    public function edit(JobPosition $jobPosition): View
    {
        return view('job-positions.edit', compact('jobPosition'));
    }

    public function update(Request $request, JobPosition $jobPosition): RedirectResponse
    {
        $validated = $request->validate([
            'direction' => ['required', 'string', 'max:100'],
            'area'      => ['required', 'string', 'max:100'],
            'name'      => ['required', 'string', 'max:100'],
            'notes'     => ['nullable', 'string'],
        ], [
            'direction.required' => 'La dirección es obligatoria.',
            'area.required'      => 'El área es obligatoria.',
            'name.required'      => 'El puesto es obligatorio.',
        ]);

        $jobPosition->update($validated);

        return redirect()->route('job-positions.index')
            ->with('success', 'Puesto actualizado correctamente.');
    }

    public function destroy(JobPosition $jobPosition): RedirectResponse
    {
        if ($jobPosition->employees()->count() > 0) {
            return back()->with('error', "No puedes eliminar este puesto porque actualmente hay {$jobPosition->employees()->count()} empleado(s) con este puesto. Edita el registro de los empleados primero.");
        }

        $jobPosition->delete();

        return redirect()->route('job-positions.index')
            ->with('success', 'El puesto ha sido eliminado.');
    }
}
