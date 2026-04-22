<?php
session_start();
require_once "../../config/conexion.php"; 

// Verificar permisos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. CONSULTA DE Ivas
$sql = "SELECT * FROM iva ORDER BY id_iva DESC";

$resultado = $conexion->query($sql);
$lista_ivas = $resultado->fetchAll(PDO::FETCH_ASSOC);



include_once "../layout/header.php"; 
?>

<!-- ============================================ -->
<!-- PAGE HEADER - CON COLOR DORADO                -->
<!-- ============================================ -->
<div class="page-header">
    <h1>
        <i class="bi bi-percent"></i>
        <span>Gestión de <span class="gold-text">Ivas</span></span>
    </h1>
    <div class="header-actions">
        <?php if ($_SESSION['id_rol'] == 1): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIva" onclick="prepararNuevo()">
                <i class="bi bi-pass"></i> Registrar Nuevo Iva
            </button>
        <?php endif; ?>
    </div>
</div>

<hr>

<!-- ============================================ -->
<!-- TABLA DE Ivas                          -->
<!-- ============================================ -->
<div class="card shadow-sm border-0 fade-in">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Listado de Ivas
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblIva" style="width:100%">
                <thead>
                    <tr>
                        <th>Iva</th>
                        <th>Porcentaje decimal</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_ivas as $iva): ?>
                        <tr>
                            <td>
                                <span class="fw-bold" style="color: var(--color-red);">
                                    <?= htmlspecialchars($iva['nombre']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($iva['porcentaje']) ?></td>
                            <td>
                                <span class="badge <?= ($iva['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($iva['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarIva" 
                                            data-id_iva="<?= $iva['id_iva'] ?>"
                                            data-nombre="<?= htmlspecialchars($iva['nombre']) ?>"
                                            data-porcentaje="<?= htmlspecialchars($iva['porcentaje']) ?>"
                                            data-estado="<?= $iva['estado'] ?>"
                                            title="Editar Iva">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" 
                                            onclick="eliminarIva(<?= $iva['id_iva'] ?>, '<?= htmlspecialchars($iva['nombre']) ?>')"
                                            title="Eliminar Iva">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
<!-- MODAL DE MEDIDAS                             -->
<!-- ============================================ -->
<div class="modal fade" id="modalIva" tabindex="-1" aria-labelledby="modalTitulo" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formIva" class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);">
                <h5 class="modal-title" id="modalTitulo" style="color: var(--color-white);">
                    <i class="bi bi-bookmark-plus" style="color: var(--color-yellow);"></i>
                    Gestión de Iva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_iva" id="id_iva">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-tag text-red"></i> Iva
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text text-red"></i> Porcentaje
                        </label>
                        <input type="text" name="porcentaje" id="porcentaje" class="form-control" required>
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

<?php include_once "../layout/footer.php"; ?>

<!-- Script específico para medidas -->
<script src="<?= URL_BASE ?>assets/js/iva.js"></script>