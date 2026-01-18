<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Promociones - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link" href="productos.php"><i class="fas fa-box"></i> Productos</a>
                <a class="nav-link" href="marcas.php"><i class="fas fa-tags"></i> Marcas</a>
                <a class="nav-link active" href="promociones.php"><i class="fas fa-percent"></i> Promociones</a>
                <a class="nav-link" href="../../index.php"><i class="fas fa-home"></i> Ver Sitio</a>
                <a class="nav-link" href="#" onclick="cerrarSesion()"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-percent"></i> Gestión de Promociones</h2>
            <div>
                <button class="btn btn-info me-2" onclick="mostrarPlantillas()">
                    <i class="fas fa-magic"></i> Usar Plantilla
                </button>
                <button class="btn btn-success-custom" onclick="mostrarFormularioNuevo()">
                    <i class="fas fa-plus"></i> Nueva Promoción
                </button>
            </div>
        </div>

        <div class="table-custom">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Aplicada A</th>
                        <th>Productos</th>
                        <th>Descuento</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="promociones-tabla">
                    <tr>
                        <td colspan="8" class="text-center">Cargando promociones...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Promoción -->
    <div class="modal fade" id="modalPromocion" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalPromocionTitulo">
                        <i class="fas fa-percent"></i> Nueva Promoción
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formPromocion" onsubmit="guardarPromocion(event)">
                    <div class="modal-body">
                        <input type="hidden" id="id_promocion" name="id_promocion">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nombre de la Promoción *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Ej: Descuento de Año Nuevo" required maxlength="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Descuento (%) *</label>
                                <input type="number" class="form-control" id="porcentaje_descuento" name="porcentaje_descuento" 
                                       min="1" max="<?php 
                                       $rol = $_SESSION['usuario']['rol'] ?? 'empleado';
                                       $limites = ['superadmin' => 100, 'admin' => 50, 'gerente' => 30, 'empleado' => 15];
                                       echo $limites[$rol] ?? 10;
                                       ?>" step="0.01" placeholder="Ej: 15" required>
                                <small class="text-muted">
                                    Máximo permitido para su rol: <?php 
                                    echo $limites[$rol] ?? 10;
                                    ?>%
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" 
                                      placeholder="Descripción de la promoción"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-filter"></i> Aplicación de la Promoción</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Aplicar a: *</label>
                            <select class="form-select" id="tipo_aplicacion" name="tipo_aplicacion" required onchange="manejarTipoAplicacion()">
                                <option value="todos">Todos los productos</option>
                                <option value="marca">Por Marca</option>
                                <option value="genero">Por Género</option>
                                <option value="tipo">Por Tipo de Calzado</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="campo_marca" style="display: none;">
                            <label class="form-label">Marca *</label>
                            <select class="form-select" id="id_marca" name="id_marca" onchange="actualizarContadorProductos()">
                                <option value="">Seleccione una marca...</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="campo_genero" style="display: none;">
                            <label class="form-label">Género *</label>
                            <select class="form-select" id="genero" name="genero" onchange="actualizarContadorProductos()">
                                <option value="">Seleccione un género...</option>
                                <option value="hombre">Hombre</option>
                                <option value="mujer">Mujer</option>
                                <option value="niño">Niño</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="campo_tipo" style="display: none;">
                            <label class="form-label">Tipo de Calzado *</label>
                            <select class="form-select" id="tipo" name="tipo" onchange="actualizarContadorProductos()">
                                <option value="">Seleccione un tipo...</option>
                                <option value="deportivo">Deportivo</option>
                                <option value="no_deportivo">No Deportivo</option>
                            </select>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Contador en tiempo real -->
                        <div class="alert alert-info" id="contador-productos" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            Esta promoción aplicará a <strong id="total-productos-preview">0</strong> producto(s)
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activa" name="activa" value="1" checked>
                            <label class="form-check-label" for="activa">Promoción Activa</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success-custom">
                            <i class="fas fa-save"></i> Guardar Promoción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Plantillas -->
    <div class="modal fade" id="modalPlantillas" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-magic"></i> Plantillas de Promociones
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Selecciona una plantilla predefinida para crear promociones rápidamente</p>
                    <div class="row" id="plantillas-container">
                        <!-- Las plantillas se cargan dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let modalPromocion;
        let modalPlantillas;
        
        // Plantillas predefinidas
        const plantillasPromociones = [
            {
                nombre: 'Black Friday',
                icono: 'fa-tag',
                color: 'danger',
                descuento: 30,
                duracion: 3,
                descripcion: 'Venta especial de Black Friday con grandes descuentos'
            },
            {
                nombre: 'Cyber Monday',
                icono: 'fa-laptop',
                color: 'primary',
                descuento: 25,
                duracion: 1,
                descripcion: 'Ofertas exclusivas online por Cyber Monday'
            },
            {
                nombre: 'Navidad',
                icono: 'fa-gift',
                color: 'success',
                descuento: 20,
                duracion: 7,
                descripcion: 'Promoción navideña para compartir en familia'
            },
            {
                nombre: 'Año Nuevo',
                icono: 'fa-champagne-glasses',
                color: 'warning',
                descuento: 15,
                duracion: 5,
                descripcion: 'Empieza el año con descuentos increíbles'
            },
            {
                nombre: 'Día del Padre',
                icono: 'fa-user-tie',
                color: 'info',
                descuento: 15,
                duracion: 1,
                descripcion: 'Regalos especiales para papá'
            },
            {
                nombre: 'Día de la Madre',
                icono: 'fa-heart',
                color: 'danger',
                descuento: 15,
                duracion: 1,
                descripcion: 'Celebra a mamá con descuentos especiales'
            },
            {
                nombre: 'Regreso a Clases',
                icono: 'fa-backpack',
                color: 'primary',
                descuento: 10,
                duracion: 14,
                descripcion: 'Prepárate para el regreso a clases'
            },
            {
                nombre: 'San Valentín',
                icono: 'fa-heart',
                color: 'danger',
                descuento: 14,
                duracion: 3,
                descripcion: 'Celebra el amor con descuentos románticos'
            }
        ];
        
        document.addEventListener('DOMContentLoaded', function() {
            modalPromocion = new bootstrap.Modal(document.getElementById('modalPromocion'));
            modalPlantillas = new bootstrap.Modal(document.getElementById('modalPlantillas'));
            cargarPromociones();
            cargarMarcas();
            cargarPlantillas();
        });
        
        function mostrarFormularioNuevo() {
            document.getElementById('formulario-promocion').reset();
            document.getElementById('id_promocion').value = '';
            document.getElementById('modalPromocionTitulo').innerHTML = '<i class="fas fa-plus"></i> Nueva Promoción';
            
            // Resetear campos condicionales
            document.getElementById('campo_marca').style.display = 'none';
            document.getElementById('campo_genero').style.display = 'none';
            document.getElementById('campo_tipo').style.display = 'none';
            
            // Establecer tipo_aplicacion en "todos" por defecto
            document.getElementById('tipo_aplicacion').value = 'todos';
            
            // Actualizar contador inicialmente
            actualizarContadorProductos();
            
            // Mostrar modal
            modalPromocion.show();
        }
        
        function cargarPromociones() {
            fetch('../../controlador/PromocionController.php?accion=listar')
                .then(response => response.json())
                .then(data => {
                    if (data.promociones) {
                        mostrarPromociones(data.promociones);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        function mostrarPromociones(promociones) {
            const tbody = document.getElementById('promociones-tabla');
            
            if (promociones.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">No hay promociones registradas</td></tr>';
                return;
            }
            
            let html = '';
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            
            promociones.forEach(p => {
                const fechaInicio = new Date(p.fecha_inicio);
                const fechaFin = new Date(p.fecha_fin);
                
                let estadoBadge = '';
                if (!p.activa || p.activa === '0') {
                    estadoBadge = '<span class="badge bg-secondary">Inactiva</span>';
                } else if (fechaFin < hoy) {
                    estadoBadge = '<span class="badge bg-danger">Expirada</span>';
                } else if (fechaInicio > hoy) {
                    estadoBadge = '<span class="badge bg-warning text-dark">Programada</span>';
                } else {
                    estadoBadge = '<span class="badge bg-success">Vigente</span>';
                }
                
                // Determinar aplicación
                let aplicadaA = '<span class="badge bg-info">Todos</span>';
                if (p.tipo_aplicacion === 'marca') {
                    aplicadaA = `<span class="badge bg-primary"><i class="fas fa-tags"></i> Marca: ${p.nombre_marca || 'ID ' + p.id_marca}</span>`;
                } else if (p.tipo_aplicacion === 'genero') {
                    aplicadaA = `<span class="badge bg-secondary"><i class="fas fa-venus-mars"></i> ${p.genero}</span>`;
                } else if (p.tipo_aplicacion === 'tipo') {
                    aplicadaA = `<span class="badge bg-success"><i class="fas fa-shoe-prints"></i> ${p.tipo === 'deportivo' ? 'Deportivo' : 'No Deportivo'}</span>`;
                }
                
                html += `
                    <tr>
                        <td>${p.id_promocion}</td>
                        <td><strong>${p.nombre}</strong></td>
                        <td>${aplicadaA}</td>
                        <td><span class="badge ${p.total_productos > 0 ? 'bg-success' : 'bg-warning text-dark'}">${p.total_productos} producto${p.total_productos != 1 ? 's' : ''}</span></td>
                        <td><span class="badge bg-primary">${parseFloat(p.porcentaje_descuento).toFixed(2)}%</span></td>
                        <td>${new Date(p.fecha_inicio).toLocaleDateString('es-EC')}</td>
                        <td>${new Date(p.fecha_fin).toLocaleDateString('es-EC')}</td>
                        <td>${estadoBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editarPromocion(${p.id_promocion})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarPromocion(${p.id_promocion})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        function mostrarFormularioNuevo() {
            document.getElementById('formPromocion').reset();
            document.getElementById('id_promocion').value = '';
            document.getElementById('activa').checked = true;
            document.getElementById('tipo_aplicacion').value = 'todos';
            manejarTipoAplicacion(); // Ocultar campos de filtro
            document.getElementById('modalPromocionTitulo').innerHTML = '<i class="fas fa-plus"></i> Nueva Promoción';
            modalPromocion.show();
        }
        
        function editarPromocion(id) {
            fetch(`../../controlador/PromocionController.php?accion=obtener&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.promocion) {
                        const p = data.promocion;
                        document.getElementById('id_promocion').value = p.id_promocion;
                        document.getElementById('nombre').value = p.nombre;
                        document.getElementById('descripcion').value = p.descripcion || '';
                        document.getElementById('porcentaje_descuento').value = p.porcentaje_descuento;
                        document.getElementById('fecha_inicio').value = p.fecha_inicio;
                        document.getElementById('fecha_fin').value = p.fecha_fin;
                        document.getElementById('activa').checked = p.activa == 1;
                        
                        // Cargar filtros
                        document.getElementById('tipo_aplicacion').value = p.tipo_aplicacion || 'todos';
                        manejarTipoAplicacion(); // Mostrar campos correspondientes
                        
                        if (p.tipo_aplicacion === 'marca' && p.id_marca) {
                            document.getElementById('id_marca').value = p.id_marca;
                        } else if (p.tipo_aplicacion === 'genero' && p.genero) {
                            document.getElementById('genero').value = p.genero;
                        } else if (p.tipo_aplicacion === 'tipo' && p.tipo) {
                            document.getElementById('tipo').value = p.tipo;
                        }
                        
                        document.getElementById('modalPromocionTitulo').innerHTML = '<i class="fas fa-edit"></i> Editar Promoción';
                        modalPromocion.show();
                    } else {
                        Swal.fire('Error', data.error || 'No se pudo cargar la promoción', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al cargar la promoción', 'error');
                });
        }
        
        function guardarPromocion(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('formPromocion'));
            const idPromocion = formData.get('id_promocion');
            
            // Si el checkbox no está marcado, agregar valor 0
            if (!formData.has('activa')) {
                formData.append('activa', '0');
            }
            
            formData.append('accion', idPromocion ? 'actualizar' : 'crear');
            
            fetch('../../controlador/PromocionController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: idPromocion ? 'Promoción Actualizada' : 'Promoción Creada',
                        text: data.mensaje,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    modalPromocion.hide();
                    cargarPromociones();
                } else {
                    let errorMsg = data.error;
                    if (data.detalles) {
                        errorMsg += ':\n' + data.detalles.join('\n');
                    }
                    Swal.fire('Error', errorMsg, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error al guardar la promoción', 'error');
            });
        }
        
        function eliminarPromocion(id) {
            Swal.fire({
                title: '¿Eliminar promoción?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('accion', 'eliminar');
                    formData.append('id_promocion', id);
                    
                    fetch('../../controlador/PromocionController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminada',
                                text: 'Promoción eliminada exitosamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            cargarPromociones();
                        } else {
                            Swal.fire('Error', data.error, 'error');
                        }
                    });
                }
            });
        }
        
        function cargarMarcas() {
            fetch('../../controlador/MarcaController.php?accion=listar')
                .then(response => response.json())
                .then(data => {
                    if (data.marcas) {
                        const select = document.getElementById('id_marca');
                        select.innerHTML = '<option value="">Seleccione una marca...</option>';
                        data.marcas.forEach(m => {
                            const option = document.createElement('option');
                            option.value = m.id_marca;
                            option.textContent = m.nombre_marca;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        function manejarTipoAplicacion() {
            const tipoAplicacion = document.getElementById('tipo_aplicacion').value;
            const campoMarca = document.getElementById('campo_marca');
            const campoGenero = document.getElementById('campo_genero');
            const campoTipo = document.getElementById('campo_tipo');
            
            // Ocultar todos
            campoMarca.style.display = 'none';
            campoGenero.style.display = 'none';
            campoTipo.style.display = 'none';
            
            // Limpiar valores
            document.getElementById('id_marca').value = '';
            document.getElementById('genero').value = '';
            document.getElementById('tipo').value = '';
            
            // Remover required
            document.getElementById('id_marca').removeAttribute('required');
            document.getElementById('genero').removeAttribute('required');
            document.getElementById('tipo').removeAttribute('required');
            
            // Mostrar según selección
            if (tipoAplicacion === 'marca') {
                campoMarca.style.display = 'block';
                document.getElementById('id_marca').setAttribute('required', 'required');
            } else if (tipoAplicacion === 'genero') {
                campoGenero.style.display = 'block';
                document.getElementById('genero').setAttribute('required', 'required');
            } else if (tipoAplicacion === 'tipo') {
                campoTipo.style.display = 'block';
                document.getElementById('tipo').setAttribute('required', 'required');
            }
            
            // Actualizar contador de productos
            actualizarContadorProductos();
        }
        
        function cargarPlantillas() {
            const container = document.getElementById('plantillas-container');
            container.innerHTML = '';
            
            plantillasPromociones.forEach((plantilla, index) => {
                const card = `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 cursor-pointer" onclick="aplicarPlantilla(${index})" style="cursor: pointer;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas ${plantilla.icono} text-${plantilla.color}"></i>
                                    ${plantilla.nombre}
                                </h5>
                                <p class="card-text text-muted small">${plantilla.descripcion}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-${plantilla.color}">${plantilla.descuento}% descuento</span>
                                    <small class="text-muted">${plantilla.duracion} día${plantilla.duracion > 1 ? 's' : ''}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += card;
            });
        }
        
        function mostrarPlantillas() {
            modalPlantillas.show();
        }
        
        function aplicarPlantilla(index) {
            const plantilla = plantillasPromociones[index];
            
            // Calcular fechas
            const hoy = new Date();
            const fechaFin = new Date();
            fechaFin.setDate(hoy.getDate() + plantilla.duracion);
            
            // Rellenar formulario
            document.getElementById('nombre').value = plantilla.nombre;
            document.getElementById('descripcion').value = plantilla.descripcion;
            document.getElementById('porcentaje_descuento').value = plantilla.descuento;
            document.getElementById('fecha_inicio').value = hoy.toISOString().split('T')[0];
            document.getElementById('fecha_fin').value = fechaFin.toISOString().split('T')[0];
            document.getElementById('activa').checked = true;
            document.getElementById('tipo_aplicacion').value = 'todos';
            document.getElementById('id_promocion').value = '';
            
            manejarTipoAplicacion();
            
            // Cerrar modal plantillas y abrir modal promoción
            modalPlantillas.hide();
            document.getElementById('modalPromocionTitulo').innerHTML = '<i class="fas fa-magic"></i> Nueva Promoción desde Plantilla';
            modalPromocion.show();
            
            Swal.fire({
                icon: 'info',
                title: 'Plantilla Aplicada',
                text: `Se ha cargado la plantilla "${plantilla.nombre}". Puedes modificar los datos antes de guardar.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
        
        function actualizarContadorProductos() {
            const tipoAplicacion = document.getElementById('tipo_aplicacion').value;
            const idMarca = document.getElementById('id_marca').value;
            const genero = document.getElementById('genero').value;
            const tipo = document.getElementById('tipo').value;
            
            const formData = new FormData();
            formData.append('tipo_aplicacion', tipoAplicacion);
            if (idMarca) formData.append('id_marca', idMarca);
            if (genero) formData.append('genero', genero);
            if (tipo) formData.append('tipo', tipo);
            formData.append('accion', 'contarProductos');
            
            fetch('../../controlador/PromocionController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const contador = document.getElementById('contador-productos');
                const total = document.getElementById('total-productos-preview');
                
                total.textContent = data.total || 0;
                
                if (data.total > 0) {
                    contador.className = 'alert alert-success';
                    contador.style.display = 'block';
                } else {
                    contador.className = 'alert alert-warning';
                    contador.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('contador-productos').style.display = 'none';
            });
        }
        
        function cerrarSesion() {
            fetch('../../controlador/AuthController.php?accion=logout')
                .then(response => response.json())
                .then(data => {
                    window.location.href = '../../index.php';
                });
        }
    </script>
</body>
</html>
