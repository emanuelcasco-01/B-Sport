// ============================================
// Gestión de Artículos
// ============================================

$(document).ready(function() {
    
    // Configuración de Alertify
    if (typeof alertify !== 'undefined') {
        alertify.defaults.transition = "zoom";
        alertify.set('notifier', 'position', 'top-right');
    }

    // Constantes
    const empresa = "B-SPORT";
    const reporteTitulo = "LISTADO DE ARTÍCULOS";
    const URL_BASE = $('meta[name="url-base"]').attr('content') || '';

    // ============================================
    // 1. INICIALIZAR DATATABLE
    // ============================================
    var table = $('#tblArticulo').DataTable({
        language: { 
            url: URL_BASE + "assets/dt/Spanish.json" 
        },
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: empresa + " - " + reporteTitulo,
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5] 
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: empresa + " - " + reporteTitulo,
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5] 
                },
                customize: function (doc) {
                    doc.styles.tableHeader.fontSize = 10;
                    doc.defaultStyle.fontSize = 9;
                    
                    doc['header'] = (function(page, pages) {
                        return {
                            columns: [
                                {
                                    text: empresa,
                                    fontSize: 14,
                                    bold: true,
                                    margin: [40, 20]
                                },
                                {
                                    alignment: 'right',
                                    text: 'Fecha: ' + new Date().toLocaleDateString() + ' ' + new Date().toLocaleTimeString(),
                                    fontSize: 9,
                                    margin: [0, 20, 40]
                                }
                            ]
                        };
                    });
                    
                    doc['footer'] = (function(page, pages) {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    text: ['Reporte generado por: ', { text: 'Sistema B-Sport' }],
                                    margin: [40, 0]
                                },
                                {
                                    alignment: 'right',
                                    text: ['Página ', { text: page.toString() }, ' de ', { text: pages.toString() }],
                                    margin: [0, 0, 40]
                                }
                            ],
                            fontSize: 8,
                            margin: [0, 0, 0, 20]
                        };
                    });
                    
                    doc.content[2].margin = [0, 35, 0, 35];
                }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Imprimir',
                className: 'btn btn-secondary btn-sm',
                title: "<h3>" + empresa + "</h3><p>" + reporteTitulo + "</p>",
                exportOptions: { 
                    columns: [0, 1, 2, 3, 4, 5] 
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 6 }
        ]
    });
    $("#formArticulo").on("submit", function(e) {
        e.preventDefault();
        
        const id = $("#id_articulo").val();

        const urlDestino = (id === "") 
                ? "../../modules/articulo/articulo_guardar.php" 
                : "../../modules/articulo/articulo_actualizar.php";

        // Mostrar loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="loading-spinner" style="width:16px;height:16px;"></span> Guardando...').prop('disabled', true);

        $.ajax({
            url: urlDestino,
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(res) {
                submitBtn.html(originalText).prop('disabled', false);
                
                if (res.status) {
                    showNotification(res.msg, 'success');
                    $("#modalMarca").modal("hide");
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                    showNotification(res.msg, 'error');
                }
            },
            error: function(jqXHR) {
                submitBtn.html(originalText).prop('disabled', false);
                console.error(jqXHR.responseText);
                showNotification('Error de conexión con el servidor', 'error');
            }
        });
    });

    // ============================================
    // 2. EVENTO EDITAR ARTÍCULO 
    // ============================================
    $('#tblArticulo').on('click', '.btnEditarArticulo', function() {
        const d = $(this).data();
        
        // Cargar datos en el formulario
        $("#id_articulo").val(d.id);
        $("#codigo_barra").val(d.codigo);
        $("#nombre_articulo").val(d.nombre);
        $("#tipo").val(d.tipo);
        $("#es_producido").val(d.producido);
        $("#id_categoria").val(d.categoria);
        $("#id_marca").val(d.marca);
        $("#id_unidad").val(d.unidad);
        $("#id_iva").val(d.iva);
        $("#precio_compras").val(d.compra);
        $("#precio_venta").val(d.venta);
        $("#estado").val(d.estado);
        
        // Cambiar título del modal
        $("#modalTituloArticulo").html('<i class="bi bi-pencil-square me-2"></i>Actualizar Artículo: ' + d.nombre);
        
        // Cambiar texto del botón
        $("#formArticulo button[type='submit']").html('<i class="bi bi-pencil-square"></i> Actualizar Artículo');
        
        // Mostrar modal
        $("#modalArticulo").modal("show");
    });

    // ============================================
    // 3. EVENTO VER VARIANTES
    // ============================================
    $('#tblArticulo').on('click', '.btnVerVariantes', function() {
        const idArticulo = $(this).data('id');
        const nombreArticulo = $(this).data('nombre');
        
        // Mostrar loading en el modal
        $('#modalVariantes .modal-content').html(`
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-4 fs-5">Cargando variantes de <strong>${nombreArticulo}</strong>...</p>
            </div>
        `);
        
        $('#modalVariantes').modal('show');
        
        // Cargar contenido real
        $('#modalVariantes .modal-content').load(
            URL_BASE + 'views/articulos/modal_variantes.php?id_articulo=' + idArticulo,
            function(response, status, xhr) {
                if (status === "error") {
                    $('#modalVariantes .modal-content').html(`
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle me-2"></i>Error
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center py-5">
                            <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #dc3545;"></i>
                            <p class="mt-4 fs-5">Error al cargar las variantes</p>
                            <p class="text-muted">Por favor, intente nuevamente</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    `);
                }
            }
        );
    });

    // ============================================
    // 4. LIMPIAR MODAL AL CERRAR
    // ============================================
    $('#modalArticulo').on('hidden.bs.modal', function() {
        $("#formArticulo")[0].reset();
        $("#id_articulo").val("");
        $("#modalTituloArticulo").html('<i class="bi bi-box-seam me-2"></i>Registrar Nuevo Artículo');
        $("#formArticulo button[type='submit']").html('<i class="bi bi-check-circle"></i> Guardar Artículo');
        
        // Restablecer valores por defecto
        $("#es_producido").val("0");
        $("#estado").val("1");
        $("#precio_compras").val("0");
        $("#precio_venta").val("0");
        
        // Limpiar validaciones visuales
        $('#codigo_barra, #nombre_articulo').removeClass('is-valid is-invalid');
    });

    // ============================================
    // 5. CÁLCULO AUTOMÁTICO DE PRECIO DE VENTA
    // ============================================
    $('#precio_compras, #id_iva').on('change keyup', function() {
        calcularPrecioVenta();
    });



    // ============================================
    // 8. TOOLTIPS DE BOOTSTRAP
    // ============================================
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // ============================================
    // 9. PREVENIR ENVÍO CON ENTER
    // ============================================
    $('#formArticulo').on('keypress', 'input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            return false;
        }
    });

});
// EVENTO ELIMINAR
    $('#tblArticulo').on('click', '.btnEliminarArticulo', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');

        if (typeof alertify === 'undefined') {
            if (confirm(`¿Estás seguro de que deseas desactivar el artículo: ${nombre}?`)) {
                ejecutarDesactivacion(id);
            }
            return;
        }

        alertify.confirm(
            "Confirmar Desactivación",
            `¿Estás seguro de que deseas <strong>desactivar</strong> el artículo:<br><br><strong style="color: var(--color-red);">${nombre}</strong>?`,
            function() {
                ejecutarDesactivacion(id);
            },
            function() {
                showNotification('Operación cancelada', 'warning');
            }
        ).set('labels', { ok: 'Sí, desactivar', cancel: 'Cancelar' });
    });
    

// ============================================
// FUNCIONES GLOBALES
// ============================================

function prepararNuevoArticulo() {
    $("#formArticulo")[0].reset();
    $("#id_articulo").val("");
    $("#codigo_barra").val("");
    $("#nombre_articulo").val("");
    $("#tipo").val("PRODUCTO");
    $("#es_producido").val("0");
    $("#id_categoria").val("");
    $("#id_marca").val("");
    $("#id_unidad").val("");
    $("#id_iva").val("");
    $("#precio_compras").val("0");
    $("#precio_venta").val("0");
    $("#estado").val("1");
    
    $("#modalTituloArticulo").html('<i class="bi bi-box-seam me-2"></i>Registrar Nuevo Artículo');
    $("#formArticulo button[type='submit']").html('<i class="bi bi-check-circle"></i> Guardar Artículo');
    
    // Remover clases de validación
    $('#codigo_barra, #nombre_articulo').removeClass('is-valid is-invalid');
    $('.invalid-feedback').remove();
}

/**
 * Calcula el precio de venta sugerido basado en el costo y el IVA
 */
function calcularPrecioVenta() {
    const precioCompra = parseFloat($('#precio_compra').val()) || 0;
    const idIva = $('#id_iva').val();
    
    if (precioCompra > 0 && idIva) {
        // Obtener el porcentaje de IVA del select
        const ivaText = $('#id_iva option:selected').text();
        const porcentajeIva = parseFloat(ivaText) || 0;
        
        // Cálculo: Precio con IVA + 30% de margen
        const precioConIva = precioCompra * (1 + porcentajeIva / 100);
        const precioSugerido = precioConIva * 1.3; // 30% de margen
        
        $('#precio_venta').val(precioSugerido.toFixed(2));
    }
}

/**
 * Muestra una notificación usando Alertify o alert
 */
function showNotification(message, type) {
    if (typeof alertify !== 'undefined') {
        alertify[type](message);
    } else {
        console.log(`[${type.toUpperCase()}]: ${message}`);
        if (type === 'error' || type === 'success' || type === 'warning') {
            alert(message);
        }
    }
}