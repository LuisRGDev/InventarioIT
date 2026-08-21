<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-middleby-800 to-middleby-700 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-wider shadow-sm hover:from-middleby-700 hover:to-middleby-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-middleby-500 focus:ring-offset-2 active:scale-95 transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
