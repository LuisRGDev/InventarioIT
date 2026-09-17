@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-middleby-500 focus:ring-middleby-500 rounded-md shadow-sm']) }}>
