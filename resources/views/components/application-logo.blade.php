<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-middleby-900 via-middleby-800 to-middleby-600 shadow-md border border-middleby-400/30 text-white overflow-hidden group">
        {{-- Imagen moderna de procesador / IT --}}
        <svg class="w-6 h-6 text-amber-500 drop-shadow transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
    </div>
    <div class="flex flex-col leading-none">
        <div class="flex items-center gap-1">
            <span class="text-xl font-extrabold tracking-tight text-middleby-900 font-sans">Inventario</span>
            <span class="text-xl font-medium tracking-tight text-middleby-700 font-sans ml-0.5">IT</span>
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block animate-pulse ml-1" title="Sistema Online"></span>
        </div>
        <span class="text-[10px] font-semibold text-slate-400 tracking-widest uppercase mt-0.5">Middleby</span>
    </div>
</div>

