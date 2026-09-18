<?php

namespace App\Http\Controllers;

use App\Models\DeviceAssignment;
use App\Models\OfficeExtensionAssignment;
use App\Models\PhoneLineAssignment;
use App\Services\ExtensionAssignmentService;
use App\Services\PhoneLineAssignmentService;
use App\Services\ResponsiveLetterService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = DeviceAssignment::with(['device.category', 'employee', 'assignedBy', 'returnedBy']);

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->whereNull('returned_at');
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('returned_at');
            }
        }

        $assignments = $query->orderByDesc('assigned_at')->paginate(20);

        return view('assignments.index', compact('assignments'));
    }

    public function downloadCartaResponsiva(DeviceAssignment $assignment, ResponsiveLetterService $service)
    {
        try {
            $filePath = $service->generate($assignment);
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo generar la carta responsiva. Intenta de nuevo.');
        }

        $employeeName = str_replace(' ', '_', $assignment->employee->name);
        $date = $assignment->assigned_at->format('Y-m-d');
        $fileName = "Carta_Responsiva_{$employeeName}_{$date}.docx";

        return response()->download($filePath, $fileName)
            ->deleteFileAfterSend(true);
    }

    public function returnPhoneLine(PhoneLineAssignment $assignment, Request $request, PhoneLineAssignmentService $service)
    {
        try {
            $service->returnLine($assignment, ['notes' => 'Devuelto desde perfil de empleado.']);

            return back()->with('success', 'Línea celular devuelta correctamente al inventario.');
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo devolver la línea. Intenta de nuevo.');
        }
    }

    public function returnExtension(OfficeExtensionAssignment $assignment, Request $request, ExtensionAssignmentService $service)
    {
        try {
            $service->returnExtension($assignment, ['notes' => 'Devuelto desde perfil de empleado.']);

            return back()->with('success', 'Extensión devuelta correctamente al inventario.');
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo devolver la extensión. Intenta de nuevo.');
        }
    }
}
