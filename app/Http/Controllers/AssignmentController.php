<?php

namespace App\Http\Controllers;

use App\Models\DeviceAssignment;
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

    public function downloadCartaResponsiva(DeviceAssignment $assignment, \App\Services\ResponsiveLetterService $service)
    {
        $filePath = $service->generate($assignment);
        
        $employeeName = str_replace(' ', '_', $assignment->employee->name);
        $date = $assignment->assigned_at->format('Y-m-d');
        $fileName = "Carta_Responsiva_{$employeeName}_{$date}.docx";
        
        return response()->download($filePath, $fileName)
            ->deleteFileAfterSend(true);
    }
}
