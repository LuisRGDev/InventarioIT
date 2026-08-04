<?php

namespace App\Http\Controllers;

use App\Enums\ExtensionStatus;
use App\Models\OfficeExtension;
use App\Http\Requests\StoreOfficeExtensionRequest;
use App\Http\Requests\UpdateOfficeExtensionRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
}
