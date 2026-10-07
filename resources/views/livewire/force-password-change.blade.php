<div>
    <div class="mb-4 text-sm text-gray-600">
        Tu contraseña es temporal (la fijó un administrador). Elige una nueva para continuar.
    </div>

    <form wire:submit="save" class="space-y-4">
        <div>
            <x-input-label for="current_password" value="Contraseña temporal" />
            <x-text-input wire:model="current_password" id="current_password" type="password" class="block mt-1 w-full" autocomplete="current-password" required autofocus />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Nueva contraseña" />
            <x-text-input wire:model="password" id="password" type="password" class="block mt-1 w-full" autocomplete="new-password" required />
            <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres, mayúsculas, minúsculas, números y símbolos.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" required />
        </div>

        <div class="flex items-center justify-between pt-2">
            <button type="button" wire:click="logout" class="underline text-sm text-gray-500 hover:text-gray-800">Cerrar sesión</button>
            <x-primary-button>Guardar contraseña</x-primary-button>
        </div>
    </form>
</div>
