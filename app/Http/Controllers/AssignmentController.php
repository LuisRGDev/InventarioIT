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
}
