<?php
session_start();

// Verificar admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - CalzadEC Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <style>
        .report-card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 12px;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .report-card.active {
            border: 2px solid var(--primary-color);
            background-color: #f0f7ff;
        }
        .report-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link" href="productos.php"><i class="fas fa-box"></i> Productos</a>
                <a class="nav-link" href="marcas.php"><i class="fas fa-tags"></i> Marcas</a>
                <a class="nav-link" href="promociones.php"><i class="fas fa-percent"></i> Promociones</a>
                <a class="nav-link active" href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
                <a class="nav-link" href="../../index.php"><i class="fas fa-home"></i> Ver Sitio</a>
                <a class="nav-link" href="../../controlador/AuthController.php?accion=logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h2 class="mb-4">Centro de Reportes</h2>
        
        <!-- Selección de Reporte -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card report-card p-3 text-center active" onclick="seleccionarReporte('ventas_fecha', this)">
                    <i class="fas fa-file-invoice-dollar report-icon"></i>
                    <h5>Ventas por Fecha</h5>
                    <p class="text-muted small">Ingresos detallados por rango de fechas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card report-card p-3 text-center" onclick="seleccionarReporte('productos_top', this)">
                    <i class="fas fa-star report-icon"></i>
                    <h5>Top Productos</h5>
                    <p class="text-muted small">Ranking de los artículos más vendidos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card report-card p-3 text-center" onclick="seleccionarReporte('inventario', this)">
                    <i class="fas fa-boxes report-icon"></i>
                    <h5>Estado Inventario</h5>
                    <p class="text-muted small">Stock actual y alertas de reposición</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card report-card p-3 text-center" onclick="seleccionarReporte('clientes', this)">
                    <i class="fas fa-users report-icon"></i>
                    <h5>Resumen Clientes</h5>
                    <p class="text-muted small">Actividad y lealtad de compradores</p>
                </div>
            </div>
        </div>

        <!-- Filtros Dinámicos -->
        <div id="filtros-container" class="card p-3 mb-4 shadow-sm border-0">
            <div class="row align-items-end" id="filtros-ventas">
                <div class="col-md-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" id="fecha-inicio" class="form-control" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" id="fecha-fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="generarReporte()">
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Área de Resultados -->
        <div class="table-container d-none" id="resultados-area">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 id="titulo-reporte">Reporte de Ventas</h4>
                <button class="btn btn-danger" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf"></i> Exportar a PDF
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="tabla-reporte">
                    <thead class="table-light">
                        <tr id="cabecera-tabla"></tr>
                    </thead>
                    <tbody id="cuerpo-tabla"></tbody>
                </table>
            </div>
        </div>

        <div id="loader" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Generando reporte...</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jsPDF and AutoTable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        let reporteActual = 'ventas_fecha';
        let datosReporte = [];

        function seleccionarReporte(tipo, elemento) {
            reporteActual = tipo;
            
            // UI
            $('.report-card').removeClass('active');
            $(elemento).addClass('active');
            
            // Mostrar/Ocultar filtros de fecha solo para ventas
            if (tipo === 'ventas_fecha') {
                $('#filtros-container').show();
            } else {
                $('#filtros-container').hide();
                generarReporte(); // Cargar directo si no requiere filtros
            }
        }

        function generarReporte() {
            $('#resultados-area').addClass('d-none');
            $('#loader').removeClass('d-none');

            let url = `../../controlador/ReporteController.php?accion=${reporteActual}`;
            
            if (reporteActual === 'ventas_fecha') {
                const inicio = $('#fecha-inicio').val();
                const fin = $('#fecha-fin').val();
                url += `&inicio=${inicio}&fin=${fin}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(res => {
                    $('#loader').addClass('d-none');
                    if (res.success) {
                        datosReporte = res.data;
                        renderizarTabla(res.data);
                        $('#resultados-area').removeClass('d-none');
                    }
                })
                .catch(err => {
                    console.error(err);
                    $('#loader').addClass('d-none');
                });
        }

        function renderizarTabla(data) {
            const cabecera = $('#cabecera-tabla');
            const cuerpo = $('#cuerpo-tabla');
            cabecera.empty();
            cuerpo.empty();

            if (data.length === 0) {
                cuerpo.append('<tr><td colspan="10" class="text-center">No hay datos para mostrar</td></tr>');
                return;
            }

            switch (reporteActual) {
                case 'ventas_fecha':
                    $('#titulo-reporte').text('Reporte de Ventas por Fecha');
                    cabecera.append('<th>Fecha</th><th>Venta ID</th><th>Cliente</th><th>Email</th><th>Subtotal</th><th>Descuento</th><th>Total</th>');
                    data.forEach(v => {
                        cuerpo.append(`
                            <tr>
                                <td>${v.fecha_venta}</td>
                                <td>#${v.id_venta}</td>
                                <td>${v.cliente}</td>
                                <td>${v.email}</td>
                                <td>$${parseFloat(v.subtotal).toFixed(2)}</td>
                                <td class="text-danger">-$${parseFloat(v.descuento).toFixed(2)}</td>
                                <td class="fw-bold">$${parseFloat(v.total).toFixed(2)}</td>
                            </tr>
                        `);
                    });
                    break;
                case 'productos_top':
                    $('#titulo-reporte').text('Reporte de Productos Más Vendidos');
                    cabecera.append('<th>Producto</th><th>Código</th><th>Cant. Vendida</th><th>Total Ingresos</th>');
                    data.forEach(p => {
                        cuerpo.append(`
                            <tr>
                                <td>${p.nombre}</td>
                                <td>${p.codigo_producto}</td>
                                <td>${p.total_vendido}</td>
                                <td class="fw-bold">$${parseFloat(p.total_ingresos).toFixed(2)}</td>
                            </tr>
                        `);
                    });
                    break;
                case 'inventario':
                    $('#titulo-reporte').text('Reporte de Estado de Inventario');
                    cabecera.append('<th>Código</th><th>Producto</th><th>Talla</th><th>Stock</th><th>Precio</th><th>Estado</th>');
                    data.forEach(p => {
                        const stockClass = p.stock <= 5 ? 'table-danger' : (p.stock <= 10 ? 'table-warning' : '');
                        cuerpo.append(`
                            <tr class="${stockClass}">
                                <td>${p.codigo_producto}</td>
                                <td>${p.nombre}</td>
                                <td>${p.talla}</td>
                                <td class="fw-bold">${p.stock}</td>
                                <td>$${parseFloat(p.precio).toFixed(2)}</td>
                                <td><span class="badge ${p.estado === 'activo' ? 'bg-success' : 'bg-secondary'}">${p.estado}</span></td>
                            </tr>
                        `);
                    });
                    break;
                case 'clientes':
                    $('#titulo-reporte').text('Reporte de Resumen de Clientes');
                    cabecera.append('<th>Cliente</th><th>Email</th><th>Teléfono</th><th>Registro</th><th>Total Compras</th><th>Total Gastado</th>');
                    data.forEach(c => {
                        cuerpo.append(`
                            <tr>
                                <td>${c.nombre_completo}</td>
                                <td>${c.email}</td>
                                <td>${c.telefono}</td>
                                <td>${c.fecha_registro}</td>
                                <td>${c.total_compras}</td>
                                <td class="fw-bold">$${parseFloat(c.total_gastado).toFixed(2)}</td>
                            </tr>
                        `);
                    });
                    break;
            }
        }

        function exportarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            
            // Título
            doc.setFontSize(18);
            doc.text('CALZADEC - Reporte Administrativo', 14, 20);
            doc.setFontSize(14);
            doc.text($('#titulo-reporte').text(), 14, 30);
            
            // Fecha de generación
            doc.setFontSize(10);
            doc.text(`Generado el: ${new Date().toLocaleString()}`, 14, 38);

            // Tabla
            doc.autoTable({
                html: '#tabla-reporte',
                startY: 45,
                theme: 'striped',
                headStyles: { fillColor: [13, 110, 253] }, // Bootstrap primary color
                styles: { fontSize: 9 }
            });

            doc.save(`${reporteActual}_${new Date().toISOString().slice(0,10)}.pdf`);
        }

        // Cargar reporte inicial
        $(document).ready(() => {
            generarReporte();
        });
    </script>
</body>
</html>
