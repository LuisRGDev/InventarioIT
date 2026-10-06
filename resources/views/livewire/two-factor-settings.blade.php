<div class="max-w-3xl mx-auto py-8 space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Autenticación en dos pasos</h2>
        <p class="text-gray-600 mt-1">Además de tu contraseña, para entrar se pedirá un código de 6 dígitos que genera una app en tu teléfono.</p>
    </div>

    @if (session('status'))
        <div class="p-4 bg-amber-50 text-amber-800 rounded-xl border border-amber-200 text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    {{-- Códigos de respaldo recién generados: se muestran una sola vez --}}
    @if (count($recoveryCodes))
        <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 overflow-hidden" x-data="{ copied: false }">
            <div class="p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-3 text-emerald-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <h3 class="text-lg font-semibold">{{ $enabled ? 'Códigos de respaldo' : 'Listo' }}</h3>
                </div>
                <p class="text-sm text-gray-700">
                    Guarda estos códigos en un lugar seguro (gestor de contraseñas o impresos).
                    <strong>No se volverán a mostrar.</strong> Cada uno sirve una sola vez si pierdes acceso a tu app.
                </p>
                <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-gray-50 border border-gray-200 rounded-xl p-4" id="recovery-codes">
                    @foreach ($recoveryCodes as $recoveryCode)
                        <span class="select-all">{{ $recoveryCode }}</span>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                            x-on:click="navigator.clipboard.writeText(@js(implode("\n", $recoveryCodes))).then(() => copied = true)"
                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                        <span x-show="!copied">Copiar códigos</span>
                        <span x-show="copied" x-cloak>¡Copiados!</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                        Ya los guardé, continuar
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if ($enabled)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Activado
                </span>
                <span class="text-sm text-gray-600">Te quedan <strong>{{ $remaining }}</strong> {{ $remaining === 1 ? 'código de respaldo' : 'códigos de respaldo' }}.</span>
            </div>

            @if ($remaining <= 2)
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">Te quedan pocos códigos de respaldo. Genera unos nuevos abajo.</p>
            @endif

            <form wire:submit="regenerate" class="space-y-3 max-w-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Generar códigos de respaldo nuevos</h3>
                <p class="text-xs text-gray-500">Los códigos anteriores dejan de funcionar. Confirma tu contraseña para continuar.</p>
                <div>
                    <x-input-label for="password" value="Contraseña actual" />
                    <x-text-input wire:model="password" id="password" type="password" class="block mt-1 w-full" autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <x-primary-button>Generar códigos nuevos</x-primary-button>
            </form>

            <p class="text-xs text-gray-500 border-t border-gray-100 pt-4">
                ¿Perdiste tu teléfono? Un administrador puede restablecer tu autenticación en dos pasos desde el panel de usuarios.
            </p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-8">
            @if ($required)
                <p class="text-sm text-gray-600">Es obligatorio activarla para usar el sistema.</p>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                        <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">1</span>
                        Escanea el código QR
                    </h3>
                    <p class="text-sm text-gray-600">Usa Google Authenticator, Microsoft Authenticator u otra app compatible.</p>
                    <div class="inline-block p-3 bg-white border border-gray-200 rounded-xl" aria-label="Código QR para tu app autenticadora">
                        {!! $qrSvg !!}
                    </div>
                    <div class="text-xs text-gray-500">
                        ¿No puedes escanear? Escribe esta clave manualmente:
                        <code class="block mt-1 p-2 bg-gray-50 border border-gray-200 rounded font-mono text-gray-800 break-all select-all">{{ trim(chunk_split($secret, 4, ' ')) }}</code>
                    </div>
                </div>

                <form wire:submit="confirm" class="space-y-3">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                        <span class="inline-block w-5 h-5 bg-indigo-100 text-indigo-600 rounded text-xs font-bold text-center leading-5">2</span>
                        Confirma el código
                    </h3>
                    <p class="text-sm text-gray-600">Escribe el código de 6 dígitos que muestra la app para terminar de activarlo.</p>
                    <div>
                        <x-input-label for="code" value="Código de verificación" />
                        <x-text-input wire:model="code" id="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="7" placeholder="000000"
                                      class="block mt-1 w-full font-mono text-center text-xl tracking-[0.4em]" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>
                    <x-primary-button>Activar</x-primary-button>
                </form>
            </div>
        </div>
    @endif
</div>
