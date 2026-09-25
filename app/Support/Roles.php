<?php

namespace App\Support;

/**
 * Nombres de rol de spatie/laravel-permission (ver database/seeders/RoleSeeder.php)
 * y agrupaciones reutilizables para el middleware `role:` en routes/web.php.
 *
 * Mapeo de permisos (Fase 1 de la auditoría, confirmado con el usuario):
 * - Admin TI: acceso total, incluye eliminar, gestionar catálogos y ver BitLocker.
 * - Técnico: operar el inventario (crear/editar/asignar/devolver/reemplazar/
 *   importar/exportar), sin eliminar registros, sin gestionar catálogos
 *   (categorías/modelos/puestos) y sin ver llaves BitLocker.
 * - Solo lectura: únicamente ver/listar/exportar.
 */
class Roles
{
    public const ADMIN = 'Admin TI';

    public const TECNICO = 'Técnico';

    public const SOLO_LECTURA = 'Solo lectura';

    // Nota: el middleware `role:` de spatie/laravel-permission separa roles
    // múltiples con "|" (una coma se interpretaría como el segundo argumento
    // del middleware, el nombre del guard de autenticación).

    /** Cualquier rol reconocido puede ver/listar/exportar. */
    public const READ = self::ADMIN.'|'.self::TECNICO.'|'.self::SOLO_LECTURA;

    /** Crear/editar/asignar/devolver/reemplazar/importar. */
    public const WRITE = self::ADMIN.'|'.self::TECNICO;

    /** Eliminar registros y gestionar catálogos (categorías/modelos/puestos). */
    public const ADMIN_ONLY = self::ADMIN;
}
