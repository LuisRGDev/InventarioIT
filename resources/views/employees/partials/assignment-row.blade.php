@php
    $devBadgeMap = [
        'green'  => 'bg-green-100 text-green-800',
        'blue'   => 'bg-blue-100 text-blue-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'gray'   => 'bg-gray-100 text-gray-800',
        'red'    => 'bg-red-100 text-red-800',
    ];
    $devBadge = $assignment->device ? ($devBadgeMap[$assignment->device->status->color()] ?? 'bg-gray-100 text-gray-800') : 'bg-gray-100 text-gray-800';
@endphp
<div class="px-6 py-4 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
            @if($assignment->device && $assignment->device->category->isSmartphone())
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            @else
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            @endif
        </div>
        <div>
            @if($assignment->device)
                <a href="{{ route('devices.show', $assignment->device) }}"
                   class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition">
                    {{ $assignment->device->brand }} {{ $assignment->device->model }}
                </a>
                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $assignment->device->serial_number }}</p>
            @else
                <span class="text-sm text-gray-400 italic">Equipo eliminado</span>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-3 text-right flex-shrink-0">
        <div>
            <p class="text-xs text-gray-400">Asignado el</p>
            <p class="text-xs font-medium text-gray-700">{{ $assignment->assigned_at->format('d M Y') }}</p>
        </div>
        <a href="{{ route('assignments.return', $assignment->device_id) }}"
           class="px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition">
            Retornar
        </a>
    </div>
</div>
