<?php
session_start();
require_once "../../config/conexion.php"; 

// Verificar permisos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. CONSULTA DE Proveedores
$sql = "SELECT * FROM proveedor ORDER BY id_proveedor DESC";

$resultado = $conexion->query($sql);
$lista_proveedores = $resultado->fetchAll(PDO::FETCH_ASSOC);



include_once "../layout/header.php"; 
?>

<!-- ============================================ -->
<!-- PAGE HEADER - CON COLOR DORADO                -->
<!-- ============================================ -->
<div class="page-header">
    <h1>
        <i class="bi bi-truck"></i>
        <span>Gestión de <span class="gold-text">Proveedores</span></span>
    </h1>
    <div class="header-actions">
        <?php if ($_SESSION['id_rol'] == 1): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor" onclick="prepararNuevo()">
                <i class="bi bi-journal-plus"></i> Registrar Nuevo Proveedor
            </button>
        <?php endif; ?>
    </div>
</div>

<hr>

<!-- ============================================ -->
<!-- TABLA DE PROVEEDORES                          -->
<!-- ============================================ -->
<div class="card shadow-sm border-0 fade-in">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Listado de Proveedores
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblProveedores" style="width:100%">
                <thead>
                    <tr>
                        <th>Ruc</th>
                        <th>Razón Social</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_proveedores as $proveedor): ?>
                        <tr>
                            <td>
                                <span class="fw-bold" style="color: var(--color-red);">
                                    <?= htmlspecialchars($proveedor['ruc']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($proveedor['razon_social']) ?></td>
                            <td><?= htmlspecialchars($proveedor['direccion']) ?></td>
                            <td><?= htmlspecialchars($proveedor['telefono']) ?></td>
                            <td><?= htmlspecialchars($proveedor['email']) ?></td>
                            <td>
                                <span class="badge <?= ($proveedor['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($proveedor['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarProveedor" 
                                            data-id_proveedor="<?= $proveedor['id_proveedor'] ?>"
                                            data-ruc="<?= htmlspecialchars($proveedor['ruc']) ?>"
                                            data-razon_social="<?= htmlspecialchars($proveedor['razon_social']) ?>"
                                            data-direccion="<?= htmlspecialchars($proveedor['direccion']) ?>"
                                            data-telefono="<?= htmlspecialchars($proveedor['telefono']) ?>"
                                            data-email="<?= htmlspecialchars($proveedor['email']) ?>"
                                            data-estado="<?= $proveedor['estado'] ?>"
                                            title="Editar Proveedor">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    c
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DE PROVEEDORES                             -->
<!-- ============================================ -->
<div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalTitulo" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formProveedor" class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);">
                <h5 class="modal-title" id="modalTitulo" style="color: var(--color-white);">
                    <i class="bi bi-bookmark-plus" style="color: var(--color-yellow);"></i>
                    Gestión de Proveedor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_proveedor" id="id_proveedor">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-card-text"></i> Ruc
                        </label>
                        <input type="text" name="ruc" id="ruc" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-people"></i> Razón Social
                        </label>
                        <input type="text" name="razon_social" id="razon_social" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-geo-alt"></i> Dirección
                        </label>
                        <input type="text" name="direccion" id="direccion" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-telephone"></i> Teléfono
                        </label>
                        <input type="text" name="telefono" id="telefono" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-envelope text-red"></i> Correo Electrónico
                        </label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-toggle-on text-red"></i> Estado
                        </label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Guardar Datos
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para confirmar eliminación de proveedor -->
<div class="modal fade" id="modalEliminarProveedor" tabindex="-1" aria-labelledby="modalEliminarProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarProveedorLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="proveedorEliminarId">
                
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Advertencia:</strong> Está a punto de eliminar permanentemente este proveedor. Revise los datos antes de continuar.
                </div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-building me-2"></i>Datos del Proveedor</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold" style="width: 35%;">RUC:</td>
                                    <td><span id="eliminarRuc" class="text-primary"></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Razón Social:</td>
                                    <td><span id="eliminarRazonSocial" class="fw-bold"></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Dirección:</td>
                                    <td><span id="eliminarDireccion"></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Teléfono:</td>
                                    <td><span id="eliminarTelefono"></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td><span id="eliminarEmail"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <p class="text-danger mb-0">
                        <i class="bi bi-shield-exclamation me-1"></i>
                        <small>Esta acción no se puede deshacer</small>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminarProveedor()">
                    <i class="bi bi-trash-fill me-1"></i>Eliminar Definitivamente
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>

<!-- Script específico para medidas -->
<script src="<?= URL_BASE ?>assets/js/proveedores.js"></script>
