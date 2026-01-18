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
    <title>Gestión de Productos - CalzadEC</title>
    
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
                <a class="nav-link active" href="productos.php"><i class="fas fa-box"></i> Productos</a>
                <a class="nav-link" href="marcas.php"><i class="fas fa-tags"></i> Marcas</a>
                <a class="nav-link" href="promociones.php"><i class="fas fa-percent"></i> Promociones</a>
                <a class="nav-link" href="../../index.php"><i class="fas fa-home"></i> Ver Sitio</a>
                <a class="nav-link" href="#" onclick="cerrarSesion()"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestión de Productos</h2>
            <button class="btn btn-success-custom" onclick="mostrarFormularioNuevo()">
                <i class="fas fa-plus"></i> Nuevo Producto
            </button>
        </div>

        <div class="table-custom">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Género</th>
                        <th>Tipo</th>
                        <th>Talla</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="productos-tabla">
                    <tr>
                        <td colspan="9" class="text-center">Cargando productos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalProductoTitulo">
                        <i class="fas fa-box"></i> Nuevo Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formProducto" onsubmit="guardarProducto(event)">
                    <div class="modal-body">
                        <input type="hidden" id="id_producto" name="id_producto">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código del Producto *</label>
                                <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" 
                                       placeholder="Ej: CAL-H-DEP-001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Nombre del producto" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" 
                                      placeholder="Descripción del producto"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label">Marca *</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="mostrarModalMarca()">
                                    <i class="fas fa-plus"></i> Nueva Marca
                                </button>
                            </div>
                            <select class="form-select" id="id_marca" name="id_marca" required>
                                <option value="">Seleccione una marca...</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Género *</label>
                                <select class="form-select" id="genero" name="genero" required>
                                    <option value="">Seleccione...</option>
                                    <option value="hombre">Hombre</option>
                                    <option value="mujer">Mujer</option>
                                    <option value="niño">Niño</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="deportivo">Deportivo</option>
                                    <option value="no_deportivo">No Deportivo</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Talla *</label>
                                <input type="number" class="form-control" id="talla" name="talla" 
                                       step="0.5" min="20" max="50" placeholder="Ej: 42" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio ($) *</label>
                                <input type="number" class="form-control" id="precio" name="precio" 
                                       step="0.01" min="0.01" placeholder="Ej: 59.99" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock *</label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       min="0" placeholder="Cantidad disponible" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Imagen del Producto</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="imagen_url" name="imagen_url" 
                                       placeholder="URL de la imagen o subir archivo">
                                <button class="btn btn-primary" type="button" id="btn_upload_widget">
                                    <i class="fas fa-cloud-upload-alt"></i> Subir Imagen
                                </button>
                            </div>
                            <small class="text-muted">Sube una imagen gratis a la nube o pega una URL externa</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success-custom">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Marca -->
    <div class="modal fade" id="modalMarca" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-tag"></i> Nueva Marca
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formMarca" onsubmit="guardarMarca(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Marca *</label>
                            <input type="text" class="form-control" id="nombre_marca" name="nombre_marca" 
                                   placeholder="Ej: Nike" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_marca" name="descripcion" rows="2" 
                                      placeholder="Descripción opcional de la marca"></textarea>
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
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
    
    <script>
        // CONFIGURACIÓN CLOUDINARY
        // Credenciales configuradas para CalzadEC
        const CLOUDINARY_CLOUD_NAME = 'dhdsmsdkp';
        const CLOUDINARY_UPLOAD_PRESET = 'calzadec_preset';
        
        // Widget de Cloudinary
        let myWidget = cloudinary.createUploadWidget({
            cloudName: CLOUDINARY_CLOUD_NAME, 
            uploadPreset: CLOUDINARY_UPLOAD_PRESET,
            sources: ['local', 'url', 'camera'],
            showAdvancedOptions: false,
            cropping: true,
            multiple: false,
            defaultSource: "local",
            styles: {
                palette: {
                    window: "#FFFFFF",
                    windowBorder: "#90A0B3",
                    tabIcon: "#0078FF",
                    menuIcons: "#5A616A",
                    textDark: "#000000",
                    textLight: "#FFFFFF",
                    link: "#0078FF",
                    action: "#FF620C",
                    inactiveTabIcon: "#0E2F5A",
                    error: "#F44235",
                    inProgress: "#0078FF",
                    complete: "#20B832",
                    sourceBg: "#E4EBF1"
                },
                fonts: {
                    default: null,
                    "'Fira Sans', sans-serif": {
                        url: "https://fonts.googleapis.com/css?family=Fira+Sans",
                        active: true
                    }
                }
            }
        }, (error, result) => { 
            if (!error && result && result.event === "success") { 
                console.log('Imagen subida con éxito: ', result.info); 
                document.getElementById('imagen_url').value = result.info.secure_url;
                Swal.fire({
                    icon: 'success',
                    title: 'Imagen Cargada',
                    text: 'La imagen se ha subido correctamente a la nube',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
        
        document.getElementById("btn_upload_widget").addEventListener("click", function(){
            myWidget.open();
        }, false);

        let modalProducto;
        let modalMarca;
        
        document.addEventListener('DOMContentLoaded', function() {
            modalProducto = new bootstrap.Modal(document.getElementById('modalProducto'));
            modalMarca = new bootstrap.Modal(document.getElementById('modalMarca'));
            cargarProductos();
            cargarMarcas();
        });
        
        function cargarProductos() {
            fetch('../../controlador/ProductoController.php?accion=listar')
                .then(response => response.json())
                .then(data => {
                    if (data.productos) {
                        mostrarProductos(data.productos);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        function cargarMarcas() {
            fetch('../../controlador/MarcaController.php?accion=listar')
                .then(response => response.json())
                .then(data => {
                    if (data.marcas) {
                        const selectMarca = document.getElementById('id_marca');
                        // Mantener la opción por defecto
                        selectMarca.innerHTML = '<option value="">Seleccione una marca...</option>';
                        
                        data.marcas.forEach(m => {
                            const option = document.createElement('option');
                            option.value = m.id_marca;
                            option.textContent = m.nombre_marca;
                            selectMarca.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar marcas:', error));
        }
        
        function mostrarProductos(productos) {
            const tbody = document.getElementById('productos-tabla');
            
            if (productos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center">No hay productos registrados</td></tr>';
                return;
            }
            
            let html = '';
            productos.forEach(p => {
                html += `
                    <tr>
                        <td><code>${p.codigo_producto}</code></td>
                        <td>${p.nombre}</td>
                        <td><span class="badge bg-secondary">${p.nombre_marca || 'Sin marca'}</span></td>
                        <td><span class="badge bg-info">${p.genero}</span></td>
                        <td><span class="badge bg-secondary">${p.tipo}</span></td>
                        <td>${p.talla}</td>
                        <td>$${parseFloat(p.precio).toFixed(2)}</td>
                        <td>
                            <span class="badge ${p.stock < 10 ? 'bg-danger' : 'bg-success'}">
                                ${p.stock}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editarProducto(${p.id_producto})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${p.id_producto})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        function mostrarFormularioNuevo() {
            document.getElementById('formProducto').reset();
            document.getElementById('id_producto').value = '';
            document.getElementById('modalProductoTitulo').innerHTML = '<i class="fas fa-plus"></i> Nuevo Producto';
            modalProducto.show();
        }
        
        function editarProducto(id) {
            // Cargar datos del producto
            fetch(`../../controlador/ProductoController.php?accion=obtener&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.producto) {
                        const p = data.producto;
                        document.getElementById('id_producto').value = p.id_producto;
                        document.getElementById('codigo_producto').value = p.codigo_producto;
                        document.getElementById('nombre').value = p.nombre;
                        document.getElementById('descripcion').value = p.descripcion || '';
                        document.getElementById('id_marca').value = p.id_marca || '';
                        document.getElementById('genero').value = p.genero;
                        document.getElementById('tipo').value = p.tipo;
                        document.getElementById('talla').value = p.talla;
                        document.getElementById('precio').value = p.precio;
                        document.getElementById('stock').value = p.stock;
                        document.getElementById('imagen_url').value = p.imagen_url || '';
                        
                        document.getElementById('modalProductoTitulo').innerHTML = '<i class="fas fa-edit"></i> Editar Producto';
                        modalProducto.show();
                    } else {
                        Swal.fire('Error', data.error || 'No se pudo cargar el producto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al cargar el producto', 'error');
                });
        }
        
        function guardarProducto(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('formProducto'));
            const idProducto = formData.get('id_producto');
            
            formData.append('accion', idProducto ? 'actualizar' : 'crear');
            
            fetch('../../controlador/ProductoController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: idProducto ? 'Producto Actualizado' : 'Producto Creado',
                        text: data.mensaje,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    modalProducto.hide();
                    cargarProductos();
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
                Swal.fire('Error', 'Ocurrió un error al guardar el producto', 'error');
            });
        }
        
        function eliminarProducto(id) {
            Swal.fire({
                title: '¿Eliminar producto?',
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
                    formData.append('id_producto', id);
                    
                    fetch('../../controlador/ProductoController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'Producto eliminado exitosamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            cargarProductos();
                        } else {
                            Swal.fire('Error', data.error, 'error');
                        }
                    });
                }
            });
        }
        
        function mostrarModalMarca() {
            document.getElementById('formMarca').reset();
            modalMarca.show();
        }
        
        function guardarMarca(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('formMarca'));
            formData.append('accion', 'crear');
            
            fetch('../../controlador/MarcaController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Marca Creada',
                        text: data.mensaje,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    modalMarca.hide();
                    
                    // Recargar marcas y seleccionar la nueva
                    cargarMarcas();
                    
                    // Esperar un momento para que se carguen las marcas y luego seleccionar la nueva
                    setTimeout(() => {
                        document.getElementById('id_marca').value = data.id;
                    }, 200);
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
