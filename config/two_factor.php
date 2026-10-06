<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 2FA obligatorio
    |--------------------------------------------------------------------------
    |
    | Con true, cualquier usuario autenticado que aún no haya activado la
    | autenticación en dos pasos es redirigido a la pantalla de configuración
    | y no puede usar el resto del sistema hasta completarla. Con false solo
    | se pide el código a quienes ya lo activaron (útil para pruebas locales).
    |
    */
    'required' => env('TWO_FACTOR_REQUIRED', true),

    // Nombre que aparece en la app autenticadora (Google/Microsoft Authenticator…).
    'issuer' => env('TWO_FACTOR_ISSUER'),

    // Minutos que el usuario tiene para teclear su código tras validar la contraseña.
    'challenge_ttl_minutes' => 5,

    // Cantidad de códigos de respaldo de un solo uso.
    'recovery_codes' => 8,

    // Ventana de tolerancia del reloj: ±N periodos de 30 s.
    'window' => 1,
];
