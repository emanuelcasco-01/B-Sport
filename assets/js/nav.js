(function() {
    'use strict';
    
    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMenu);
    } else {
        initMenu();
    }
    
    function initMenu() {
        // ============================================
        // MARCAR ITEM ACTIVO SEGÚN URL ACTUAL
        // ============================================
        const currentUrl = window.location.pathname;
        const currentFile = currentUrl.split('/').pop();
        
        // Marcar items activos
        const allLinks = document.querySelectorAll('.sidebar-item[data-link], .submenu-item[data-link]');
        
        allLinks.forEach(link => {
            const linkFile = link.getAttribute('data-link');
            const href = link.getAttribute('href');
            
            if (href && href !== '#' && !href.startsWith('#')) {
                const hrefFile = href.split('/').pop();
                
                if (currentFile === hrefFile || currentUrl.includes(href.replace(/^\.\.\//, ''))) {
                    link.classList.add('active-link');
                    
                    // Expandir el menú padre
                    const parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        const toggleBtn = document.querySelector(`[href="#${parentCollapse.id}"]`);
                        if (toggleBtn) {
                            toggleBtn.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            }
        });
        
        // ============================================
        // GUARDAR ESTADO DE LOS MENÚS EXPANDIDOS
        // ============================================
        const collapses = document.querySelectorAll('.collapse');
        
        collapses.forEach(collapse => {
            if (!collapse.id) return;
            
            // Cargar estado guardado
            const savedState = localStorage.getItem(`bs_menu_${collapse.id}`);
            if (savedState === 'expanded') {
                collapse.classList.add('show');
                const toggleBtn = document.querySelector(`[href="#${collapse.id}"]`);
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
            }
            
            // Eventos de Bootstrap
            collapse.addEventListener('shown.bs.collapse', function() {
                localStorage.setItem(`bs_menu_${this.id}`, 'expanded');
            });
            
            collapse.addEventListener('hidden.bs.collapse', function() {
                localStorage.setItem(`bs_menu_${this.id}`, 'collapsed');
            });
        });
        
        // ============================================
        // CERRAR SIDEBAR EN MÓVIL AL HACER CLIC
        // ============================================
        const sidebarLinks = document.querySelectorAll('.sidebar-item[href]:not([data-bs-toggle]), .submenu-item');
        const wrapper = document.getElementById('wrapper');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && wrapper) {
                    // Pequeño delay para permitir la navegación
                    setTimeout(() => {
                        wrapper.classList.add('sidebar-collapsed');
                        if (sidebarOverlay) {
                            sidebarOverlay.classList.remove('active');
                        }
                        localStorage.setItem('sidebar-collapsed', 'true');
                    }, 100);
                }
            });
        });
        
        // ============================================
        // PREVENIR CIERRE DEL COLLAPSE AL HACER CLIC EN SUBMENÚ
        // ============================================
        document.querySelectorAll('.has-submenu').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Usar Bootstrap Collapse API
                    const bsCollapse = bootstrap.Collapse.getInstance(target) || new bootstrap.Collapse(target, { toggle: false });
                    bsCollapse.toggle();
                    
                    // Actualizar aria-expanded
                    const isExpanded = target.classList.contains('show');
                    this.setAttribute('aria-expanded', !isExpanded);
                }
            });
        });
        
        console.log('Menú B-Sport inicializado correctamente');
    }
})();