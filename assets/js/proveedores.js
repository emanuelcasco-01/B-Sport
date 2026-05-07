// ============================================
// SCRIPT PARA GESTIÓN DE Proveedores
// ============================================
$(document).ready(function() {
    var logobase64= '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'

    // Configuración Alertify
    if (typeof alertify !== 'undefined') {
        alertify.defaults.transition = "zoom";
        alertify.set('notifier', 'position', 'top-right');
    }

    const empresa = "B-SPORT";
    const reporteTitulo = "REPORTE DE PROVEEDORES";

    // Inicializar DataTable
    var table = $('#tblProveedores').DataTable({
        language: { 
            url: "<?php echo URL_BASE; ?>assets/dt/Spanish.json" 
        },
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rt<"d-flex justify-content-between align-items-center mt-3"lip>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'btn btn-sm',
                title: empresa + " - " + reporteTitulo,
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                className: 'btn btn-sm',
                title: "Listado de Proveedores",
                exportOptions: { columns: [0, 1, 2, 3, 4] },
                customize: function (doc) {
                    // 1. Insertar el Logo al principio del contenido
                    doc.content.splice(0, 0, {
                        margin: [0, 0, 0, 12],
                        alignment: 'center',
                        image: 'data:image/jpeg;base64,' + logobase64,
                        width: 80
                    });

                    // Estilos generales
                    doc.styles.tableHeader.fontSize = 10;
                    doc.defaultStyle.fontSize = 9;

                    // --- Configuración del Membrete (Header) ---
                    doc['header'] = (function(page, pages) {
                        return {
                            columns: [
                                {
                                    text: 'BIANCA SPORT',
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

                    // --- Configuración del Pie de Página (Footer) ---
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

                    doc.content[2].margin = [0, 30, 0, 30]; 
                }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Imprimir',
                className: 'btn btn-sm',
                title: "<h3>" + empresa + "</h3><p>" + reporteTitulo + "</p>",
                exportOptions: { columns: [0, 1, 2, 3, 4] }
            }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'asc']]
    });

    // Envío de Formulario (Guardar/Actualizar)
    $("#formProveedor").on("submit", function(e) {
        e.preventDefault();
        const id = $("#id_proveedor").val();
        const urlDestino = (id === "") 
                ? "../../modules/proveedores/proveedores_guardar.php" 
                : "../../modules/proveedores/proveedores_actualizar.php";

        // Mostrar loading en el botón
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
                    $("#modalProveedor").modal("hide");
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

    // Cargar datos para Editar
    $('#tblProveedores').on('click', '.btnEditarProveedor', function() {
        const d = $(this).data();
        $("#formProveedor")[0].reset();
        $("#id_proveedor").val(d.id_proveedor);
        $("#ruc").val(d.ruc);
        $("#razon_social").val(d.razon_social);
        $("#direccion").val(d.direccion);
        $("#telefono").val(d.telefono);
        $("#email").val(d.email);
        $("#estado").val(d.estado);
        $("#modalTitulo").html('<i class="bi bi-pencil-square me-2" style="color: var(--color-yellow);"></i>Actualizar Proveedor: ' + d.razon_social);
        $("#modalProveedor").modal("show");
    });
    
    // Limpiar modal al cerrar
    $('#modalProveedor').on('hidden.bs.modal', function() {
        $("#formProveedor")[0].reset();
        $("#id_proveedor").val("");
        
    });

    // Limpiar modal de eliminación al cerrar
    $('#modalEliminarProveedor').on('hidden.bs.modal', function() {
        $("#proveedorEliminarId").val("");
    });
});

// ============================================
// FUNCIONES GLOBALES
// ============================================
function prepararNuevo() {
    $("#formProveedor")[0].reset();
    $("#id_proveedor").val("");
    $("#modalTitulo").html('<i class="bi bi-truck" style="color: var(--color-yellow);"></i>Registrar Nuevo Proveedor');
}

function eliminarProveedor(btn) {
    // Obtener datos del botón
    const datos = $(btn).data();
    
    // Cargar datos en el modal de confirmación
    $("#proveedorEliminarId").val(datos.id_proveedor);
    $("#eliminarRuc").text(datos.ruc);
    $("#eliminarRazonSocial").text(datos.razon_social);
    $("#eliminarDireccion").text(datos.direccion || 'No especificada');
    $("#eliminarTelefono").text(datos.telefono || 'No especificado');
    $("#eliminarEmail").text(datos.email || 'No especificado');
    
    // Mostrar el modal
    $("#modalEliminarProveedor").modal("show");
}

function confirmarEliminarProveedor() {
    const id = $("#proveedorEliminarId").val();
    
    if (!id) {
        showNotification('Error: ID de proveedor no encontrado', 'error');
        return;
    }
    
    // Cerrar el modal
    $("#modalEliminarProveedor").modal("hide");
    
    // Mostrar confirmación final
    if (typeof alertify !== 'undefined') {
        alertify.confirm(
            " Confirmar Eliminación Definitiva",
            `<div style="text-align: center;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: var(--color-red);"></i>
                <p class="mt-2"><strong>¿Está completamente seguro?</strong></p>
                <p class="text-muted">Esta acción eliminará permanentemente al proveedor y no podrá ser recuperado.</p>
            </div>`,
            function() {
                ejecutarEliminacion(id);
            },
            function() {
                showNotification('Eliminación cancelada', 'warning');
            }
        ).set('labels', { ok: 'Sí, eliminar definitivamente', cancel: 'Cancelar' });
    } else {
        if (confirm('¿Está completamente seguro de eliminar este proveedor? Esta acción no se puede deshacer.')) {
            ejecutarEliminacion(id);
        }
    }
}

function ejecutarEliminacion(id) {
    // Mostrar loading
    showNotification('Eliminando proveedor...', 'info');
    
    $.ajax({
        url: "../../modules/proveedores/proveedores_eliminar.php",
        type: "POST",
        data: { id_proveedor: id },
        dataType: "json",
        success: function(res) {
            if (res.status) {
                showNotification(res.msg, 'success');
                setTimeout(() => { location.reload(); }, 1200);
            } else {
                showNotification(res.msg, 'error');
            }
        },
        error: function() {
            showNotification('Error al procesar la solicitud', 'error');
        }
    });
}
