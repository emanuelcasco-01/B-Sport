// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // Configuración global
    const URL_BASE = '<?php echo URL_BASE; ?>';
    
    // ============================================
    // TOGGLE DEL MENÚ LATERAL
    // ============================================
    const wrapper = document.getElementById("wrapper");
    const toggleButton = document.getElementById("menu-toggle");
    
    if (toggleButton && wrapper) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            wrapper.classList.toggle("toggled");
            
            // Guardar estado en localStorage
            const isToggled = wrapper.classList.contains("toggled");
            localStorage.setItem('sidebar-toggled', isToggled);
            
            // Actualizar icono del botón
            const icon = this.querySelector('i');
            if (icon) {
                if (isToggled) {
                    icon.classList.remove('bi-list');
                    icon.classList.add('bi-list-nested');
                } else {
                    icon.classList.remove('bi-list-nested');
                    icon.classList.add('bi-list');
                }
            }
        });
        
        // Restaurar estado del sidebar
        const savedState = localStorage.getItem('sidebar-toggled');
        if (savedState === 'true') {
            wrapper.classList.add("toggled");
            const icon = toggleButton.querySelector('i');
            if (icon) {
                icon.classList.remove('bi-list');
                icon.classList.add('bi-list-nested');
            }
        }
    }
    
    // ============================================
    // FECHA Y HORA EN TIEMPO REAL
    // ============================================
    function updateDateTime() {
        const now = new Date();
        
        // Formato para fecha
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        const dateStr = now.toLocaleDateString('es-ES', dateOptions);
        
        // Formato para hora
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const timeStr = now.toLocaleTimeString('es-ES', timeOptions);
        
        // Actualizar elementos
        const dateElement = document.getElementById("currentDate");
        const timeElement = document.getElementById("currentTime");
        
        if (dateElement) {
            dateElement.textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
        }
        
        if (timeElement) {
            timeElement.textContent = timeStr;
        }
    }
    
    updateDateTime();
    const timeInterval = setInterval(updateDateTime, 1000);
    
    // ============================================
    // CONFIGURACIÓN GLOBAL DE DATATABLES
    // ============================================
    if (typeof $.fn.dataTable !== 'undefined') {
        // Configuración por defecto para DataTables
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '<?php echo URL_BASE; ?>assets/dt/Spanish.json',
                sLengthMenu: '_MENU_ registros por página',
                sSearch: '<i class="bi bi-search" style="color: var(--color-red);"></i> Buscar:',
                sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
                sInfoFiltered: '(filtrado de _MAX_ registros totales)',
                sZeroRecords: 'No se encontraron resultados',
                sEmptyTable: 'No hay datos disponibles en la tabla',
                oPaginate: {
                    sFirst: '<i class="bi bi-chevron-double-left"></i>',
                    sPrevious: '<i class="bi bi-chevron-left"></i>',
                    sNext: '<i class="bi bi-chevron-right"></i>',
                    sLast: '<i class="bi bi-chevron-double-right"></i>'
                }
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            pageLength: 25,
            responsive: true,
            autoWidth: false
        });
        
        // Estilos personalizados para DataTables
        const style = document.createElement('style');
        style.textContent = `
            /* Estilos DataTables - Tema B-Sport */
            .dataTables_wrapper .dataTables_length select {
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                padding: 5px 30px 5px 10px;
                background-color: var(--color-white);
                color: var(--color-black);
                font-weight: 500;
            }
            
            .dataTables_wrapper .dataTables_length select:focus {
                border-color: var(--color-red);
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
            }
            
            .dataTables_wrapper .dataTables_filter input {
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                padding: 8px 15px;
                margin-left: 10px;
                background-color: var(--color-white);
            }
            
            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: var(--color-red);
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
                outline: none;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                border-radius: 6px !important;
                margin: 0 3px;
                padding: 6px 12px;
                font-weight: 500;
                transition: all 0.3s;
                border: none !important;
                background: var(--color-white) !important;
                color: var(--color-black) !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: var(--color-red) !important;
                color: var(--color-white) !important;
                border: none !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%) !important;
                color: var(--color-yellow) !important;
                border: 1px solid var(--color-red) !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
                opacity: 0.5;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
                background: var(--color-white) !important;
                color: var(--color-black) !important;
                transform: none;
                box-shadow: none;
            }
            
            /* Botones de exportación */
            .dt-buttons .btn {
                background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);
                color: var(--color-white) !important;
                border: 2px solid var(--color-red) !important;
                border-radius: 8px !important;
                padding: 8px 16px;
                margin-right: 8px;
                font-weight: 600;
                transition: all 0.3s;
            }
            
            .dt-buttons .btn:hover {
                background: var(--color-red) !important;
                border-color: var(--color-yellow) !important;
                color: var(--color-yellow) !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
            }
            
            .dt-buttons .btn i {
                margin-right: 5px;
            }
            
            /* Ordenamiento */
            table.dataTable thead .sorting:before,
            table.dataTable thead .sorting_asc:before,
            table.dataTable thead .sorting_desc:before,
            table.dataTable thead .sorting_asc_disabled:before,
            table.dataTable thead .sorting_desc_disabled:before {
                color: var(--color-yellow) !important;
            }
            
            table.dataTable thead .sorting:after,
            table.dataTable thead .sorting_asc:after,
            table.dataTable thead .sorting_desc:after,
            table.dataTable thead .sorting_asc_disabled:after,
            table.dataTable thead .sorting_desc_disabled:after {
                color: var(--color-red) !important;
            }
            
            /* Info y resumen */
            .dataTables_info {
                color: var(--color-black) !important;
                font-weight: 500;
            }
            
            /* Loading */
            .dataTables_processing {
                background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);
                color: var(--color-yellow) !important;
                border: 2px solid var(--color-red);
                border-radius: 10px;
                padding: 15px 25px;
                font-weight: 600;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            }
        `;
        document.head.appendChild(style);
        
        console.log(' DataTables inicializado con tema B-Sport');
    }
    
    // ============================================
    // CONFIGURACIÓN DE ALERTIFY
    // ============================================
    if (typeof alertify !== 'undefined') {
        alertify.defaults.transition = 'slide';
        alertify.defaults.theme.ok = 'btn btn-primary';
        alertify.defaults.theme.cancel = 'btn btn-secondary';
        alertify.defaults.theme.input = 'form-control';
        alertify.set('notifier', 'position', 'top-right');
        alertify.set('notifier', 'delay', 4);
        
        // Estilos personalizados para Alertify
        const alertifyStyle = document.createElement('style');
        alertifyStyle.textContent = `
            .alertify-notifier .ajs-message {
                background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%) !important;
                color: var(--color-white) !important;
                border-left: 5px solid var(--color-red) !important;
                border-radius: 10px !important;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3) !important;
            }
            
            .alertify-notifier .ajs-message.ajs-success {
                border-left-color: var(--color-yellow) !important;
            }
            
            .alertify-notifier .ajs-message.ajs-error {
                border-left-color: var(--color-red) !important;
            }
            
            .alertify-notifier .ajs-message.ajs-warning {
                border-left-color: var(--color-yellow) !important;
            }
            
            .alertify .ajs-dialog {
                background: var(--color-white) !important;
                border-radius: 15px !important;
                border: 2px solid var(--color-red) !important;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3) !important;
            }
            
            .alertify .ajs-header {
                background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%) !important;
                color: var(--color-white) !important;
                border-bottom: 2px solid var(--color-red) !important;
                border-radius: 13px 13px 0 0 !important;
                font-weight: 700 !important;
            }
            
            .alertify .ajs-footer {
                border-top: 1px solid var(--color-red) !important;
            }
            
            .alertify .ajs-footer .ajs-button {
                border-radius: 8px !important;
                font-weight: 600 !important;
                transition: all 0.3s !important;
            }
            
            .alertify .ajs-footer .ajs-button.ajs-ok {
                background: linear-gradient(135deg, var(--color-red) 0%, var(--color-red-dark) 100%) !important;
                color: var(--color-white) !important;
                border: none !important;
            }
            
            .alertify .ajs-footer .ajs-button.ajs-ok:hover {
                background: linear-gradient(135deg, var(--color-yellow) 0%, var(--color-yellow-dark) 100%) !important;
                color: var(--color-black) !important;
            }
        `;
        document.head.appendChild(alertifyStyle);
    }
    
    // Función global para notificaciones
    window.showNotification = function(message, type = 'success', duration = 4) {
        if (typeof alertify !== 'undefined') {
            alertify.notify(message, type, duration);
        } else {
            console.log(`[${type.toUpperCase()}]: ${message}`);
        }
    };
    
    // ============================================
    // FUNCIÓN GLOBAL PARA INICIALIZAR DATATABLES
    // ============================================
    window.initDataTable = function(tableId, options = {}) {
        if (typeof $.fn.dataTable === 'undefined') {
            console.error('DataTables no está disponible');
            return null;
        }
        
        const defaultOptions = {
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard"></i> Copiar',
                    className: 'btn'
                },
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-excel"></i> Excel',
                    className: 'btn'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-pdf"></i> PDF',
                    className: 'btn'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    className: 'btn'
                }
            ],
            dom: 'Bfrtip'
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        return $(tableId).DataTable(finalOptions);
    };
    
    // ============================================
    // LIMPIEZA AL CERRAR
    // ============================================
    window.addEventListener('beforeunload', function() {
        clearInterval(timeInterval);
    });
    
    // ============================================
    // UTILIDADES GLOBALES
    // ============================================
    window.formatCurrency = function(amount) {
        return new Intl.NumberFormat('es-PY', {
            style: 'currency',
            currency: 'PYG',
            minimumFractionDigits: 0
        }).format(amount);
    };
    
    window.formatDate = function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    };
    
    console.log(' Sistema B-Sport inicializado - Tema Rojo/Negro/Amarillo');
});