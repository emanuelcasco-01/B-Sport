// ============================================
// SCRIPT PARA GESTIÓN DE USUARIOS
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
                    // Se inserta antes de la tabla (doc.content[1])
                    doc.content.splice(0, 0, {
                        margin: [0, 0, 0, 12],
                        alignment: 'center',
                        image: 'data:image/jpeg;base64,' + logobase64, // Asegúrate de anteponer el prefijo data:image
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

                    // Ajuste de márgenes del contenido para no solapar con header/footer
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
});

// ============================================
// FUNCIONES GLOBALES
// ============================================
function prepararNuevo() {
    $("#formProveedor")[0].reset();
    $("#id_proveedor").val("");
    $("#modalTitulo").html('<i class="bi bi-truck" style="color: var(--color-yellow);"></i>Registrar Nuevo Proveedor');
}

function eliminarProveedor(id, user) {
    if (typeof alertify === 'undefined') {
        if (confirm(`¿Desea eliminar a ${user}?`)) {
            ejecutarEliminacion(id);
        }
        return;
    }
    
    alertify.confirm(
        "Confirmar Eliminación",
        `¿Está seguro de que desea eliminar al proveedor <strong>${user}</strong>?`,
        function() {
            ejecutarEliminacion(id);
        },
        function() {
            showNotification('Operación cancelada', 'warning');
        }
    ).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' });
}

function ejecutarEliminacion(id) {
    $.ajax({
        url: "../../modules/proveedores/proveedores_eliminar.php",
        type: "POST",
        data: { id_proveedor: id },
        dataType: "json",
        success: function(res) {
            if (res.status) {
                showNotification(res.msg, 'success');
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                showNotification(res.msg, 'error');
            }
        },
        error: function() {
            showNotification('Error al procesar la solicitud', 'error');
        }
    });
}