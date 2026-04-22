<?php
session_start();
require_once "../../config/conexion.php"; 

// Verificar permisos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. CONSULTA DE ROLES
$sql = "SELECT * FROM rol ORDER BY id_rol DESC";
$resultado = $conexion->query($sql);
$lista_roles = $resultado->fetchAll(PDO::FETCH_ASSOC);

// 2. CONSULTA DE TODOS LOS PERMISOS
$sql_permisos = "SELECT id_permiso, nombre_permiso FROM permisos ORDER BY nombre_permiso";
$permisos_query = $conexion->query($sql_permisos);
$todos_los_permisos = $permisos_query->fetchAll(PDO::FETCH_ASSOC);

// Incluir el header (ya incluye navbar, sidebar y apertura de contenido)
include_once "../layout/header.php"; 

?>
<link rel="stylesheet" href="../assets/css/roles.css">
<!-- ============================================ -->
<!-- PAGE HEADER - CON COLOR DORADO                -->
<!-- ============================================ -->
<div class="page-header">
    <h1>
        <i class="bi bi-shield-lock-fill"></i>
        <span>Gestión de <span class="gold-text">Roles</span></span>
    </h1>
    <div class="header-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRol" onclick="prepararNuevoRol()">
            <i class="bi bi-shield-plus"></i> Registrar Nuevo Rol
        </button>
    </div>
</div>

<hr>

<!-- ============================================ -->
<!-- TABLA DE ROLES                                -->
<!-- ============================================ -->
<div class="card shadow-sm border-0 fade-in">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Listado de Roles
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblRoles" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre del Rol</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_roles as $rol): ?>
                        <tr>
                            <td>
                                <span class="fw-bold" style="color: var(--color-red);">
                                    <?= htmlspecialchars($rol['nombre']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($rol['descripcion'] ?: 'Sin descripción') ?></td>
                            <td>
                                <span class="badge <?= ($rol['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($rol['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarRol" 
                                            data-id="<?= $rol['id_rol'] ?>"
                                            data-nombre="<?= htmlspecialchars($rol['nombre']) ?>"
                                            data-desc="<?= htmlspecialchars($rol['descripcion']) ?>"
                                            data-estado="<?= $rol['estado'] ?>"
                                            title="Editar Rol">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btnEliminarRol" 
                                            data-id="<?= $rol['id_rol'] ?>" 
                                            data-nombre="<?= htmlspecialchars($rol['nombre']) ?>"
                                            title="Desactivar Rol">
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
<!-- MODAL DE ROLES Y PERMISOS                     -->
<!-- ============================================ -->
<div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formRol" class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);">
                <h5 class="modal-title" id="modalTituloRol" style="color: var(--color-white);">
                    <i class="bi bi-shield-shaded me-2" style="color: var(--color-yellow);"></i>
                    Gestión de Rol
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_rol" id="id_rol">
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">
                            <i class="bi bi-tag text-red"></i> Nombre del Rol
                        </label>
                        <input type="text" name="nombre" id="nombre_rol" class="form-control" 
                            placeholder="Ej: Vendedor, Administrador, Cajero..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-toggle-on text-red"></i> Estado
                        </label>
                        <select name="estado" id="estado_rol" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text text-red"></i> Descripción
                        </label>
                        <textarea name="descripcion" id="descripcion_rol" class="form-control" rows="2" 
                                placeholder="Describe las funciones y responsabilidades de este rol..."></textarea>
                    </div>
                </div>

                <hr class="my-4">
                
                <!-- Sección de Permisos -->
                <div class="permissions-section">
                    <h6 class="fw-bold mb-3 text-dark text-center">
                        <i class="bi bi-check2-all me-2" style="color: var(--color-red);"></i>
                        ASIGNACIÓN DE PERMISOS
                    </h6>
                    <div class="row">
                        <?php foreach ($todos_los_permisos as $p): ?>
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="form-check form-switch permission-switch">
                                    <input class="form-check-input check-permiso" type="checkbox" name="permisos[]" 
                                        value="<?= $p['id_permiso'] ?>" id="p_<?= $p['id_permiso'] ?>">
                                    <label class="form-check-label" for="p_<?= $p['id_permiso'] ?>">
                                        <i class="bi bi-check-circle-fill me-1" style="color: var(--color-yellow); font-size: 0.7rem;"></i>
                                        <?= htmlspecialchars($p['nombre_permiso']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>

<!-- Script específico para roles -->
<script src="<?= URL_BASE ?>assets/js/roles.js"></script>