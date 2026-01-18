/**
 * Funciones JavaScript - Tienda de Calzado
 * Validaciones, gestión de carrito y funcionalidades dinámicas
 */

// Validador de cédula ecuatoriana
function validarCedulaEcuatoriana(cedula) {
    // Verificar que tenga 10 dígitos
    if (cedula.length !== 10) {
        return false;
    }

    // Verificar que solo contenga números
    if (!/^\d+$/.test(cedula)) {
        return false;
    }

    // Verificar código de provincia (01-24)
    const provincia = parseInt(cedula.substring(0, 2));
    if (provincia < 1 || provincia > 24) {
        return false;
    }

    // Algoritmo de validación del dígito verificador
    const coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    let suma = 0;

    for (let i = 0; i < 9; i++) {
        let valor = parseInt(cedula[i]) * coeficientes[i];
        if (valor > 9) {
            valor -= 9;
        }
        suma += valor;
    }

    const digitoVerificador = (10 - (suma % 10)) % 10;

    return digitoVerificador === parseInt(cedula[9]);
}

// Validar formulario en tiempo real
function validarCampo(input) {
    const valor = input.value.trim();
    const tipo = input.getAttribute('data-validacion');
    let esValido = true;
    let mensaje = '';

    switch (tipo) {
        case 'cedula':
            esValido = validarCedulaEcuatoriana(valor);
            mensaje = esValido ? '' : 'Cédula ecuatoriana no válida';
            break;

        case 'email':
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            esValido = regexEmail.test(valor);
            mensaje = esValido ? '' : 'Email no válido';
            break;

        case 'telefono':
            esValido = /^\d{10}$/.test(valor);
            mensaje = esValido ? '' : 'Teléfono debe tener 10 dígitos';
            break;

        case 'password':
            esValido = valor.length >= 6;
            mensaje = esValido ? '' : 'Contraseña debe tener al menos 6 caracteres';
            break;

        case 'requerido':
            esValido = valor.length > 0;
            mensaje = esValido ? '' : 'Este campo es requerido';
            break;
    }

    mostrarError(input, esValido, mensaje);
    return esValido;
}

// Mostrar error de validación
function mostrarError(input, esValido, mensaje) {
    const contenedor = input.parentElement;
    let errorDiv = contenedor.querySelector('.error-mensaje');

    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-mensaje text-danger small mt-1';
        contenedor.appendChild(errorDiv);
    }

    if (esValido) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorDiv.textContent = '';
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorDiv.textContent = mensaje;
    }
}

// Gestión del carrito de compras
class CarritoCompras {
    constructor() {
        this.items = this.cargarCarrito();
    }

    cargarCarrito() {
        const carrito = localStorage.getItem('carrito');
        return carrito ? JSON.parse(carrito) : [];
    }

    guardarCarrito() {
        localStorage.setItem('carrito', JSON.stringify(this.items));
        this.actualizarContador();
    }

    agregarProducto(producto, cantidad = 1) {
        // Verificar si el usuario es administrador
        if (window.usuarioRol === 'admin') {
            this.mostrarNotificacion('Los administradores no pueden realizar compras', 'warning');
            return;
        }

        const itemExistente = this.items.find(item => item.id_producto === producto.id_producto);

        if (itemExistente) {
            itemExistente.cantidad += cantidad;
        } else {
            this.items.push({
                id_producto: producto.id_producto,
                nombre: producto.nombre,
                precio: producto.precio_final || producto.precio,
                imagen_url: producto.imagen_url,
                cantidad: cantidad
            });
        }

        this.guardarCarrito();
        this.mostrarNotificacion('Producto agregado al carrito', 'success');
    }

    eliminarProducto(idProducto) {
        this.items = this.items.filter(item => item.id_producto !== idProducto);
        this.guardarCarrito();
        this.actualizarVistaCarrito();
    }

    actualizarCantidad(idProducto, cantidad) {
        const item = this.items.find(item => item.id_producto === idProducto);
        if (item) {
            item.cantidad = parseInt(cantidad);
            if (item.cantidad <= 0) {
                this.eliminarProducto(idProducto);
            } else {
                this.guardarCarrito();
                this.actualizarVistaCarrito();
            }
        }
    }

    obtenerTotal() {
        return this.items.reduce((total, item) => total + (item.precio * item.cantidad), 0);
    }

    obtenerCantidadTotal() {
        return this.items.reduce((total, item) => total + item.cantidad, 0);
    }

    vaciarCarrito() {
        this.items = [];
        this.guardarCarrito();
        this.actualizarVistaCarrito();
    }

    actualizarContador() {
        const contador = document.getElementById('carrito-contador');
        if (contador) {
            const cantidad = this.obtenerCantidadTotal();
            contador.textContent = cantidad;
            contador.style.display = cantidad > 0 ? 'inline' : 'none';
        }
    }

    actualizarVistaCarrito() {
        // Implementar según la vista específica
        if (typeof actualizarCarritoUI === 'function') {
            actualizarCarritoUI(this.items);
        }
    }

    mostrarNotificacion(mensaje, tipo = 'info') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: tipo,
            title: mensaje
        });
    }
}

// Inicializar carrito
const carrito = new CarritoCompras();

// Filtrar productos
function filtrarProductos(genero = null, tipo = null) {
    let url = 'controlador/ProductoController.php?accion=filtrar';

    if (genero) {
        url += `&genero=${genero}`;
    }

    if (tipo) {
        url += `&tipo=${tipo}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.productos) {
                renderizarProductos(data.productos);
            }
        })
        .catch(error => {
            console.error('Error al filtrar productos:', error);
        });
}

// Renderizar productos (implementar según la vista)
function renderizarProductos(productos) {
    const contenedor = document.getElementById('productos-contenedor');
    if (!contenedor) return;

    contenedor.innerHTML = '';

    if (!productos || productos.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12 text-center">
                <p class="text-muted">No se encontraron productos</p>
            </div>
        `;
        return;
    }

    productos.forEach(producto => {
        // Verificar stock
        const stock = parseInt(producto.stock) || 0;
        const agotado = stock <= 0;

        // Verificar si tiene promoción activa
        const tienePromocion = producto.tiene_promocion && producto.porcentaje_descuento > 0;

        // Calcular precio final si hay promoción
        let precioFinal = parseFloat(producto.precio);
        if (tienePromocion && producto.precio_final) {
            precioFinal = parseFloat(producto.precio_final);
        }

        const precioHTML = tienePromocion
            ? `<div class="mb-2">
                   <p class="mb-1"><del class="text-muted">$${parseFloat(producto.precio).toFixed(2)}</del></p>
                   <p class="text-success mb-0 fw-bold fs-5">$${precioFinal.toFixed(2)}</p>
                   <small class="text-success"><i class="fas fa-tag"></i> Ahorra ${parseFloat(producto.porcentaje_descuento).toFixed(0)}%</small>
               </div>`
            : `<p class="fw-bold fs-5 mb-2">$${parseFloat(producto.precio).toFixed(2)}</p>`;

        const badgeHTML = tienePromocion
            ? `<span class="position-absolute top-0 end-0 m-2 badge bg-danger">-${parseFloat(producto.porcentaje_descuento).toFixed(0)}%</span>`
            : '';

        // Overlay de agotado
        const agotadoOverlay = agotado
            ? `<div class="position-absolute top-50 start-50 translate-middle" 
                    style="z-index: 10; background: rgba(220, 53, 69, 0.9); 
                           color: white; padding: 10px 30px; 
                           border-radius: 8px; font-weight: bold; 
                           box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                   <i class="fas fa-times-circle"></i> AGOTADO
               </div>`
            : '';

        // Ruta de imagen - usar placeholder.svg si no existe
        let imagenRuta = 'img/placeholder.svg';
        if (producto.imagen_url && producto.imagen_url.trim() !== '') {
            imagenRuta = producto.imagen_url;
        }

        // Botón de agregar al carrito o agotado
        const botonHTML = agotado
            ? `<button class="btn btn-secondary w-100" disabled>
                   <i class="fas fa-ban"></i> No Disponible
               </button>`
            : window.usuarioRol === 'admin'
                ? `<button class="btn btn-secondary w-100" disabled title="Los administradores no pueden comprar">
                       <i class="fas fa-lock"></i> No disponible para admin
                   </button>`
                : `<button class="btn btn-primary w-100" onclick="carrito.agregarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')})">
                       <i class="fas fa-cart-plus"></i> Agregar al Carrito
                   </button>`;

        // Indicador de stock
        const stockHTML = agotado
            ? '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Sin stock</span>'
            : stock < 5
                ? `<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle"></i> Quedan ${stock}</span>`
                : `<span class="badge bg-success"><i class="fas fa-check-circle"></i> Disponible</span>`;

        const card = `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm hover-shadow ${agotado ? 'opacity-75' : ''}">
                    ${badgeHTML}
                    <div class="position-relative">
                        <img src="${imagenRuta}" 
                             class="card-img-top ${agotado ? 'filter-grayscale' : ''}" 
                             alt="${producto.nombre}"
                             width="100%"
                             height="250"
                             style="object-fit: cover;">
                        ${agotadoOverlay}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${producto.nombre}</h5>
                        <div class="mb-2">
                            <span class="badge bg-primary me-1">${producto.genero}</span>
                            <span class="badge bg-secondary">${producto.tipo === 'deportivo' ? 'Deportivo' : 'No Deportivo'}</span>
                            ${stockHTML}
                        </div>
                        <p class="text-muted mb-2"><i class="fas fa-ruler"></i> Talla: ${producto.talla}</p>
                        ${precioHTML}
                        ${botonHTML}
                    </div>
                </div>
            </div>
        `;

        contenedor.innerHTML += card;
    });
}

// Confirmar acción
function confirmarAccion(mensaje, callback) {
    Swal.fire({
        title: '¿Está seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// Formatear precio
function formatearPrecio(precio) {
    return new Intl.NumberFormat('es-EC', {
        style: 'currency',
        currency: 'USD'
    }).format(precio);
}

// Inicializar validaciones al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    // Actualizar contador del carrito
    carrito.actualizarContador();

    // Agregar validación en tiempo real a campos con data-validacion
    const camposValidacion = document.querySelectorAll('[data-validacion]');
    camposValidacion.forEach(campo => {
        campo.addEventListener('blur', () => validarCampo(campo));
        campo.addEventListener('input', () => {
            if (campo.classList.contains('is-invalid') || campo.classList.contains('is-valid')) {
                validarCampo(campo);
            }
        });
    });

    // Validar formularios antes de enviar
    const formularios = document.querySelectorAll('form[data-validar]');
    formularios.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const campos = form.querySelectorAll('[data-validacion]');
            let formularioValido = true;

            campos.forEach(campo => {
                if (!validarCampo(campo)) {
                    formularioValido = false;
                }
            });

            if (formularioValido) {
                form.submit();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Por favor corrija los errores en el formulario'
                });
            }
        });
    });
});
