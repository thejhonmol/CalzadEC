# Casos de Uso - Tienda de Calzado

## Actores del Sistema

1. **Administrador**: Usuario con permisos completos para gestionar el sistema
2. **Cliente**: Usuario que puede navegar el catálogo y realizar compras

---

## Caso de Uso 1: Gestión de Marcas y Productos

**Actor:** Administrador  
**Descripción:** Gestión integral del catálogo, incluyendo creación de marcas y productos asociados.

**Flujo Principal (Marcas):**
1. El administrador accede al módulo de Marcas.
2. Visualiza el listado de marcas existentes.
3. Puede crear una nueva marca ingresando nombre y descripción.
4. El sistema valida que el nombre no esté duplicado.
5. Guarda la marca en el sistema.

**Flujo Principal (Productos):**
1. El administrador inicia sesión en el sistema
2. Accede al módulo de gestión de productos
3. Selecciona la opción deseada (Agregar, Editar, Eliminar)
4. Completa el formulario seleccionando **Marca**, Género, Tipo, Talla, etc.
5. El sistema valida la información
6. El sistema guarda los cambios y actualiza el catálogo

**Precondiciones:**
- El usuario debe estar autenticado como administrador

**Postcondiciones:**
- El catálogo se actualiza con los cambios realizados
- El stock se ajusta según corresponda
- Las marcas creadas están disponibles para asignación
 
---

## Caso de Uso 2: Gestión de Clientes

**Actor:** Administrador  
**Descripción:** El administrador puede visualizar, modificar y eliminar información de clientes registrados

**Flujo Principal:**
1. El administrador accede al módulo de clientes
2. Visualiza la lista de clientes registrados
3. Selecciona un cliente para ver detalles o modificar
4. Realiza las modificaciones necesarias
5. El sistema valida los datos (especialmente cédula ecuatoriana)
6. Guarda los cambios

**Precondiciones:**
- El administrador debe estar autenticado

**Postcondiciones:**
- Los datos del cliente se actualizan en la base de datos

---

## Caso de Uso 3: Registro y Autenticación

**Actor:** Cliente  
**Descripción:** Un nuevo usuario se registra en el sistema o un usuario existente inicia sesión

**Flujo Principal (Registro):**
1. El usuario accede a la página de registro
2. Completa el formulario con sus datos personales
3. El sistema valida la cédula ecuatoriana y requiere la selección de Provincia y Ciudad (Ecuador).
4. El sistema verifica que el email no esté registrado
5. Crea la cuenta y envía confirmación
6. Redirige al usuario al catálogo

**Flujo Alternativo (Login):**
1. El usuario accede a la página de login
2. Ingresa cédula/email y contraseña
3. El sistema valida las credenciales
4. Redirige según el rol (admin → dashboard, cliente → catálogo)

**Precondiciones:**
- Para registro: La cédula debe ser válida y no estar registrada

**Postcondiciones:**
- Se crea una sesión activa para el usuario
- El usuario accede a las funcionalidades según su rol

---

## Caso de Uso 4: Navegación de Catálogo

**Actor:** Cliente  
**Descripción:** El cliente explora el catálogo de productos con filtros

**Flujo Principal:**
1. El cliente accede al catálogo
2. Aplica filtros según preferencias (género, tipo, talla)
3. Visualiza los productos disponibles con sus detalles
4. Puede ver productos en promoción destacados
5. Selecciona un producto para ver más detalles

**Precondiciones:**
- El catálogo debe tener productos disponibles

**Postcondiciones:**
- El cliente visualiza productos según los filtros aplicados

---

## Caso de Uso 5: Realizar Compra

**Actor:** Cliente  
**Descripción:** El cliente agrega productos al carrito y completa la compra

**Flujo Principal:**
1. El cliente navega el catálogo
2. Agrega productos al carrito especificando cantidad
3. Accede al carrito de compras
4. Revisa los productos, cantidades y total
5. Confirma la compra
6. El sistema valida disponibilidad de stock
7. Procesa la venta y actualiza el stock
8. Genera la factura automáticamente
9. Muestra confirmación y factura al cliente

**Precondiciones:**
- El cliente debe estar autenticado
- Los productos deben tener stock disponible

**Postcondiciones:**
- El stock se reduce según la compra
- Se registra la venta en la base de datos
- Se genera la factura

**Flujo Alternativo:**
- Si no hay stock suficiente, se notifica al cliente y se ajusta la cantidad

---

## Caso de Uso 6: Gestión de Promociones Segmentadas

**Actor:** Administrador  
**Descripción:** El administrador crea, modifica y elimina promociones con filtros específicos

**Flujo Principal:**
1. El administrador accede al módulo de promociones
2. Crea una nueva promoción especificando:
   - Nombre y descripción
   - Porcentaje de descuento
   - Fecha de inicio y fin
   - **Filtros de Aplicación:** Por Marca, Género, Tipo o Todos
3. El sistema muestra **en tiempo real** la cantidad de productos afectados
4. El sistema valida las fechas y coherencia de datos
5. Guarda la promoción
6. Los productos afectados muestran el precio con descuento y badge visual

**Precondiciones:**
- El administrador debe estar autenticado
- Deben existir productos que cumplan los filtros seleccionados

**Postcondiciones:**
- La promoción se aplica automáticamente a las ventas y catálogo
- Se actualizan los precios visibles para el cliente

---

## Caso de Uso 7: Generación de Reportes

**Actor:** Administrador  
**Descripción:** El administrador genera reportes de ventas y estadísticas

**Flujo Principal:**
1. El administrador accede al módulo de reportes
2. Selecciona el tipo de reporte:
   - Ventas por período
   - Productos más vendidos
   - Stock bajo
   - Clientes frecuentes
3. Especifica parámetros (fechas, filtros)
4. El sistema genera el reporte dinámicamente en pantalla
5. El administrador visualiza los datos en una tabla interactiva
6. Selecciona la opción "Exportar a PDF"
7. El sistema genera un documento PDF profesional con los datos filtrados

**Precondiciones:**
- Debe haber datos registrados en el sistema

**Postcondiciones:**
- Se genera el reporte solicitado
- Se descarga un archivo PDF con la información oficial

---

## Caso de Uso 8: Gestión de Perfil de Usuario

**Actor:** Cliente  
**Descripción:** El cliente puede visualizar sus datos y editar su información de contacto (teléfono, ubicación geográfica y dirección).

**Flujo Principal:**
1. El cliente inicia sesión
2. Accede a la sección "Mi Perfil"
3. Visualiza sus datos informativos (Cédula, correo, nombre)
4. Selecciona "Editar Datos"
5. Modifica su número de celular, provincia, ciudad o dirección
6. El sistema valida el formato de los datos
7. Guarda los cambios
8. El sistema actualiza la sesión y muestra confirmación

**Precondiciones:**
- El cliente debe estar autenticado

**Postcondiciones:**
- La información de contacto se actualiza en la base de datos y en la sesión activa

---

## Caso de Uso 9: Interacción con Promociones Destacadas

**Actor:** Cliente / Administrador  
**Descripción:** Los usuarios interactúan con el sistema visual de promociones, incluyendo el carrusel de ofertas y la barra de promociones rotativa.

**Flujo Principal (Cliente):**
1. El usuario accede a la página de inicio o catálogo
2. Visualiza la barra superior con la promoción activa actual
3. La barra rota automáticamente cada 5 segundos mostrando diferentes promociones
4. Explora la sección "Ofertas Destacadas" con productos en promoción
5. Utiliza las flechas del carrusel para navegar entre productos
6. Identifica productos en oferta por sus badges (porcentaje y nombre de promoción)
7. Puede agregar productos al carrito directamente desde el carrusel

**Flujo Alternativo (Administrador):**
1. El administrador visualiza las mismas promociones
2. Observa el botón "Panel Admin" visible únicamente para su rol
3. Accede rápidamente al panel de administración desde cualquier página

**Precondiciones:**
- El sistema debe tener promociones activas configuradas

**Postcondiciones:**
- El usuario puede explorar y adquirir productos promocionados
- El administrador tiene acceso rápido al panel de gestión

---

## Diagrama de Casos de Uso

```
┌─────────────────────────────────────────────────────────┐
│                 SISTEMA TIENDA CALZADO                  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Administrador                      Cliente             │
│       │                               │                 │
│       ├─> Gestionar Productos         │                 │
│       │                               │                 │
│       ├─> Gestionar Clientes          │                 │
│       │                               │                 │
│       ├─> Gestionar Promociones       │                 │
│       │                               │                 │
│       ├─> Generar Reportes            │                 │
│       │                               │                 │
│       ├─> Ver Ventas                  │                 │
│       │                               │                 │
│       └─> Autenticarse <──────────────┤                 │
│                                       │                 │
│                                       ├─> Registrarse   │
│                                       │                 │
│                                       ├─> Navegar       │
│                                       │   Catálogo      │
│                                       │                 │
│                                       ├─> Realizar      │
│                                       │   Compra        │
│                                       │                 │
│                                       ├─> Ver Mis       │
│                                       │   Compras       │
│                                       │                 │
│                                       └─> Gestionar     │
│                                           Perfil        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```
