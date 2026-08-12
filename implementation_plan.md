# Plan de Implementación de Mejoras y Correcciones

## User Review Required
> [!IMPORTANT]
> - Removeré el campo de `phone` (teléfono personal) de las vistas de creación/edición de empleados. Los empleados mantendrán su número pero a través de la asignación oficial de "Línea Telefónica Móvil". ¿Deseas que este campo se elimine también de la base de datos (con una migración) o basta con ocultarlo del sistema/formularios? Por ahora solo lo ocultaré en el sistema para evitar pérdida de datos históricos.
> - Cambiaré el filtro de categorías en el Dashboard/Inventario a un modelo de múltiples "Checkboxes".

## Proposed Changes

---

### Módulo de Catálogo de Equipos y Registro (Placeholders)
Actualmente, el sistema asume que los placeholders para crear "Modelos/Estándares" y registrar equipos son para computadoras (Ej: "8 GB RAM", "Windows 11"). 
- **Modificar placeholders en Plantillas**: Actualizaré `device-models/create.blade.php` para que si se elige "Smartphone", los placeholders muestren ejemplos orientados a celulares (Ej: "8 GB LPDDR5X", "256 GB UFS", "Android 14").
- **Modificar campos al registrar equipo físico**: En `devices/create.blade.php` me aseguraré de que la RAM y el Almacenamiento también puedan especificarse para Celulares si es necesario, mostrando placeholders adaptados a teléfonos (Ej. iOS 17, 128 GB ROM).

#### [MODIFY] [device-models/create.blade.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/resources/views/device-models/create.blade.php)
#### [MODIFY] [devices/create.blade.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/resources/views/devices/create.blade.php)

---

### Módulo de Empleados (Quitar número telefónico personal)
Eliminaremos la posibilidad de capturar el teléfono desde el perfil del empleado, de modo que la única vía válida sea mediante la asignación oficial de la línea.
- **Formulario de Registro**: Eliminaré el campo `phone` en el Asistente (Livewire).
- **Formulario de Edición**: Eliminaré el campo `phone` de la vista de edición estándar.
- **Validaciones**: Actualizaré las reglas de `StoreEmployeeRequest`, `UpdateEmployeeRequest` y el componente de Livewire para ignorar el campo.

#### [MODIFY] [livewire/employee-onboarding-wizard.blade.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/resources/views/livewire/employee-onboarding-wizard.blade.php)
#### [MODIFY] [EmployeeOnboardingWizard.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/app/Livewire/EmployeeOnboardingWizard.php)
#### [MODIFY] [employees/edit.blade.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/resources/views/employees/edit.blade.php)
#### [MODIFY] [StoreEmployeeRequest.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/app/Http/Requests/StoreEmployeeRequest.php)
#### [MODIFY] [UpdateEmployeeRequest.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/app/Http/Requests/UpdateEmployeeRequest.php)

---

### Módulo de Asignación de Equipos (Bug al registrar empleado)
Al seleccionar un equipo de cómputo en el alta del empleado:
- **Error visual de Livewire**: Al listar las computadoras resultantes del buscador, faltaba la directiva obligatoria `wire:key` que evita que Livewire se confunda al actualizar el DOM cuando el usuario selecciona o des-selecciona.
- **Mejora Visual**: Una vez seleccionado el equipo, en lugar de decir fríamente `Equipo ID: 5`, mostraré `Marca, Modelo y Serie`.
- **Manejo de Errores Seguros**: Se envolverá la asignación transaccional con bloque `try...catch` por si el equipo llegara a perder su disponibilidad (ej. por uso concurrente), mostrando un mensaje amigable en pantalla y no un error "500" fatal.

#### [MODIFY] [livewire/employee-onboarding-wizard.blade.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/resources/views/livewire/employee-onboarding-wizard.blade.php)
#### [MODIFY] [EmployeeOnboardingWizard.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/app/Livewire/EmployeeOnboardingWizard.php)

---

### Módulo de Dashboard y Filtros (Checkboxes Múltiples)
Se actualizará la tabla de dispositivos para permitir selección múltiple de categorías.
- **Controlador Livewire**: Cambiaré `$category_id` por un arreglo vacío `$category_ids = []` y ajustaré la consulta SQL a un `whereIn('device_category_id', $category_ids)`.
- **Vista**: Reemplazaré el `select` clásico ("Todas") por un listado en línea o menú desplegable con *Checkboxes* que permita palomear Desktop, Portátil y Smartphone al mismo tiempo.

#### [MODIFY] [livewire/device-table.blade.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/resources/views/livewire/device-table.blade.php)
#### [MODIFY] [DeviceTable.php](file:///wsl.localhost/Ubuntu/home/luis_rosales/InventarioIT/app/Livewire/DeviceTable.php)

---

## Verification Plan
1. **Automated Tests**: No requiero correr pruebas automatizadas, pero validaré el PHP estáticamente.
2. **Manual Verification**: 
    - Comprobar que, al registrar una PC, los placeholders sean de PC, y al registrar un celular, cambien dinámicamente.
    - Confirmar que ya no hay campo "teléfono" para el empleado.
    - Intentar seleccionar un dispositivo en el wizard y comprobar que su vista previa sea clara.
    - Filtrar el inventario simultáneamente por `Smartphone` y `Portatil`.
