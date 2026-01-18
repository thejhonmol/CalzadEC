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
    <title>Gestión de Marcas - CalzadEC</title>
    
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
                <a class="nav-link active" href="marcas.php"><i class="fas fa-tags"></i> Marcas</a>
                <a class="nav-link" href="promociones.php"><i class="fas fa-percent"></i> Promociones</a>
                <a class="nav-link" href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
                <a class="nav-link" href="../../index.php"><i class="fas fa-home"></i> Ver Sitio</a>
                <a class="nav-link" href="../../controlador/AuthController.php?accion=logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tags"></i> Gestión de Marcas</h2>
            <button class="btn btn-success-custom" onclick="mostrarFormularioNuevo()">
                <i class="fas fa-plus"></i> Nueva Marca
            </button>
        </div>

        <!-- Información de ayuda -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Información:</strong> Solo puedes eliminar marcas que no tengan productos asociados. 
            Las marcas con productos se marcarán como inactivas para preservar el historial de ventas.
        </div>

        <div class="table-custom">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Productos Asociados</th>
                        <th>Fecha Creación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="marcas-tabla">
                    <tr>
                        <td colspan="7" class="text-center">Cargando marcas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Marca -->
    <div class="modal fade" id="modalMarca" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalMarcaTitulo">
                        <i class="fas fa-tag"></i> Nueva Marca
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formMarca" onsubmit="guardarMarca(event)">
                    <div class="modal-body">
                        <input type="hidden" id="id_marca" name="id_marca">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Marca *</label>
                            <input type="text" class="form-control" id="nombre_marca" name="nombre_marca" 
                                   placeholder="Ej: Nike" required maxlength="100">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Descripción de la marca"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success-custom">
                            <i class="fas fa-save"></i> Guardar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let modalMarca;
        
        document.addEventListener('DOMContentLoaded', function() {
            modalMarca = new bootstrap.Modal(document.getElementById('modalMarca'));
            cargarMarcas();
        });
        
        function cargarMarcas() {
            fetch('../../controlador/MarcaController.php?accion=listarTodas')
                .then(response => response.json())
                .then(data => {
                    if (data.marcas) {
                        mostrarMarcas(data.marcas);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        function mostrarMarcas(marcas) {
            const tbody = document.getElementById('marcas-tabla');
            
            if (marcas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay marcas registradas</td></tr>';
                return;
            }
            
            let html = '';
            marcas.forEach(m => {
                const estadoBadge = m.estado === 'activo' 
                    ? '<span class="badge bg-success">Activo</span>' 
                    : '<span class="badge bg-secondary">Inactivo</span>';
                
                const productosInfo = m.total_productos > 0 
                    ? `<span class="badge bg-warning text-dark">${m.total_productos}</span>`
                    : '<span class="badge bg-secondary">0</span>';
                
                html += `
                    <tr>
                        <td>${m.id_marca}</td>
                        <td><strong>${m.nombre_marca}</strong></td>
                        <td>${m.descripcion || '<em class="text-muted">Sin descripción</em>'}</td>
                        <td class="text-center">${productosInfo}</td>
                        <td>${new Date(m.fecha_creacion).toLocaleDateString('es-EC')}</td>
                        <td>${estadoBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editarMarca(${m.id_marca})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${m.estado === 'activo' ? `
                                <button class="btn btn-sm btn-danger" onclick="eliminarMarca(${m.id_marca}, ${m.total_productos})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : `
                                <button class="btn btn-sm btn-success" onclick="activarMarca(${m.id_marca})" title="Activar">
                                    <i class="fas fa-check"></i>
                                </button>
                            `}
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        function mostrarFormularioNuevo() {
            document.getElementById('formMarca').reset();
            document.getElementById('id_marca').value = '';
            document.getElementById('modalMarcaTitulo').innerHTML = '<i class="fas fa-plus"></i> Nueva Marca';
            modalMarca.show();
        }
        
        function editarMarca(id) {
            fetch(`../../controlador/MarcaController.php?accion=obtener&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.marca) {
                        const m = data.marca;
                        document.getElementById('id_marca').value = m.id_marca;
                        document.getElementById('nombre_marca').value = m.nombre_marca;
                        document.getElementById('descripcion').value = m.descripcion || '';
                        
                        document.getElementById('modalMarcaTitulo').innerHTML = '<i class="fas fa-edit"></i> Editar Marca';
                        modalMarca.show();
                    } else {
                        Swal.fire('Error', data.error || 'No se pudo cargar la marca', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al cargar la marca', 'error');
                });
        }
        
        function guardarMarca(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('formMarca'));
            const idMarca = formData.get('id_marca');
            
            formData.append('accion', idMarca ? 'actualizar' : 'crear');
            
            fetch('../../controlador/MarcaController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: idMarca ? 'Marca Actualizada' : 'Marca Creada',
                        text: data.mensaje,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    modalMarca.hide();
                    cargarMarcas();
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
                Swal.fire('Error', 'Ocurrió un error al guardar la marca', 'error');
            });
        }
        
        function eliminarMarca(id, totalProductos) {
            if (totalProductos > 0) {
                Swal.fire({
                    title: '¿Desactivar marca?',
                    html: `Esta marca tiene <strong>${totalProductos}</strong> producto(s) asociado(s).<br>
                           Se marcará como inactiva para preservar el historial.<br><br>
                           <small class="text-muted">No se puede eliminar permanentemente para proteger las ventas realizadas.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Sí, desactivar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarEliminacion(id);
                    }
                });
            } else {
                Swal.fire({
                    title: '¿Eliminar marca?',
                    text: 'Esta marca no tiene productos asociados. ¿Deseas eliminarla permanentemente?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarEliminacion(id);
                    }
                });
            }
        }
        
        function ejecutarEliminacion(id) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id_marca', id);
            
            fetch('../../controlador/MarcaController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Marca desactivada',
                        text: data.mensaje,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    cargarMarcas();
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            });
        }
        
        function activarMarca(id) {
            Swal.fire({
                title: '¿Activar marca?',
                text: 'La marca volverá a estar disponible para productos',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Sí, activar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('accion', 'activar');
                    formData.append('id_marca', id);
                    
                    fetch('../../controlador/MarcaController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Marca Activada',
                                text: data.mensaje,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            cargarMarcas();
                        } else {
                            Swal.fire('Error', data.error, 'error');
                        }
                    });
                }
            });
        }
        
        // Función eliminada a favor de link directo

    </script>
</body>
</html>
