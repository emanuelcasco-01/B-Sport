<?php

if (!defined('URL_BASE')) {
    define('URL_BASE', '/');
}
?>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-content-wrapper -->
</div>
<!-- /#wrapper -->

<!-- Footer - Negro con acentos rojos y amarillos -->
<footer class="footer text-white py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-1 fw-bold">

                    <span style="background: linear-gradient(135deg, #fff 0%, var(--color-yellow) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        B-SPORT
                    </span> 
                    <span style="color: var(--color-red);">|</span> 
                    <span style="color: var(--color-white);">Sistema de Gestión de Facturación y Producción</span>
                </p>
                <small style="color: #888;">
                    <i class="bi bi-c-circle me-1" style="color: var(--color-red);"></i>2026 - Versión 0.0.2 
                    <span style="color: var(--color-yellow);">|</span> 
                    <span style="color: var(--color-red);">B-Sport</span>
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex flex-column flex-md-row justify-content-md-end align-items-center gap-3">
                    <small style="color: #aaa;">
                        <i class="bi bi-calendar3 me-2" style="color: var(--color-red);"></i>
                        <span id="currentDate" style="color: var(--color-white);"></span>
                    </small>
                    <small style="color: #aaa;">
                        <i class="bi bi-clock me-2" style="color: var(--color-yellow);"></i>
                        <span id="currentTime" style="color: var(--color-white);"></span>
                    </small>
                    <div>
                        <a href="<?php echo URL_BASE; ?>views/ayuda.php" class="text-decoration-none me-3" title="Ayuda" style="color: var(--color-yellow); transition: all 0.3s;">
                            <i class="bi bi-question-circle fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================ -->
<!-- LIBRERÍAS JAVASCRIPT                          -->
<!-- ============================================ -->

<!-- 1. JQUERY - Requerido para DataTables -->
<script src="<?php echo URL_BASE; ?>assets/dt/jquery-3.7.0.js"></script>

<!-- 2. Bootstrap Bundle con Popper -->
<script src="<?php echo URL_BASE; ?>assets/bt/bootstrap.bundle.min.js"></script>

<!-- 3. Alertify JS - Notificaciones -->
<script src="<?php echo URL_BASE; ?>assets/alertify/alertify.min.js"></script>

<!-- 4. DATATABLES BASE -->
<script src="<?php echo URL_BASE; ?>assets/dt/jquery.dataTables.min.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/dataTables.bootstrap5.min.js"></script>

<!-- 5. EXTENSIONES DE DATATABLES (Botones, Exportación) -->
<script src="<?php echo URL_BASE; ?>assets/dt/botones/dataTables.buttons.min.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/botones/buttons.bootstrap5.min.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/botones/jszip.min.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/botones/pdfmake.min.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/botones/vfs_fonts.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/botones/buttons.html5.min.js"></script>
<script src="<?php echo URL_BASE; ?>assets/dt/botones/buttons.print.min.js"></script>

<!-- ============================================ -->
<!-- SCRIPTS PERSONALIZADOS                       -->
<!-- ============================================ -->
<script src="<?= URL_BASE ?>assets/js/footer.js"></script>

<!-- ============================================ -->
<!-- ESTILOS ADICIONALES PARA EL FOOTER           -->
<!-- ============================================ -->
<link rel="stylesheet" href="<?= URL_BASE ?>assets/css/footer.css">

</body>
</html>