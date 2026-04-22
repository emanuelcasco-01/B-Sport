<?php
session_start();
require_once "../../config/conexion.php"; 

// Verificar permisos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. CONSULTA DE USUARIOS
$sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.username, u.email, u.id_rol, u.estado, r.nombre AS nombre_rol
        FROM usuario u 
        INNER JOIN rol r ON u.id_rol = r.id_rol
        ORDER BY u.id_usuario DESC";
$resultado = $conexion->query($sql);
$lista_usuarios = $resultado->fetchAll(PDO::FETCH_ASSOC);

// 2. CONSULTA DE ROLES PARA EL SELECT DEL MODAL
$sql_roles = "SELECT id_rol, nombre FROM rol WHERE estado = 1 ORDER BY nombre";
$roles_query = $conexion->query($sql_roles);
$lista_roles = $roles_query->fetchAll(PDO::FETCH_ASSOC);

include_once "../layout/header.php"; 
?>

<!-- ============================================ -->
<!-- PAGE HEADER - CON COLOR DORADO                -->
<!-- ============================================ -->
<div class="page-header">
    <h1>
        <i class="bi bi-people-fill"></i>
        <span>Gestión de <span class="gold-text">Usuarios</span></span>
    </h1>
    <div class="header-actions">
        <?php if ($_SESSION['id_rol'] == 1): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="prepararNuevo()">
                <i class="bi bi-person-plus-fill"></i> Registrar Nuevo Usuario
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Breadcrumb -->
<hr>

<!-- ============================================ -->
<!-- TABLA DE USUARIOS                             -->
<!-- ============================================ -->
<div class="card shadow-sm border-0 fade-in">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Listado de Usuarios
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblUsuarios" style="width:100%">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $user): ?>
                        <tr>
                            <td>
                                <span class="fw-bold" style="color: var(--color-red);">
                                    <?= htmlspecialchars($user['username']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($user['nombre'] . " " . $user['apellido']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($user['nombre_rol']) ?></span>
                            </td>
                            <td>
                                <span class="badge <?= ($user['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($user['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditar" 
                                            data-id="<?= $user['id_usuario'] ?>"
                                            data-nombre="<?= htmlspecialchars($user['nombre']) ?>"
                                            data-apellido="<?= htmlspecialchars($user['apellido']) ?>"
                                            data-username="<?= htmlspecialchars($user['username']) ?>"
                                            data-email="<?= htmlspecialchars($user['email']) ?>"
                                            data-rol="<?= $user['id_rol'] ?>"
                                            data-estado="<?= $user['estado'] ?>"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" 
                                            onclick="eliminarUsuario(<?= $user['id_usuario'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                            title="Eliminar">
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
<!-- MODAL DE USUARIOS                             -->
<!-- ============================================ -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalTitulo" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formUsuario" class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);">
                <h5 class="modal-title" id="modalTitulo" style="color: var(--color-white);">
                    <i class="bi bi-person-gear me-2" style="color: var(--color-yellow);"></i>
                    Gestión de Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_usuario" id="id_usuario">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-person text-red"></i> Nombre
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-person text-red"></i> Apellido
                        </label>
                        <input type="text" name="apellido" id="apellido" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-at text-red"></i> Nombre de Usuario
                        </label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-envelope text-red"></i> Correo Electrónico
                        </label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-lock text-red"></i> Contraseña
                        </label>
                        <input type="password" name="clave" id="clave" class="form-control" 
                            placeholder="Mínimo 8 caracteres">
                        <small id="passHelp" class="text-muted d-block mt-1"></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-lock-fill text-red"></i> Confirmar Contraseña
                        </label>
                        <input type="password" name="confirmar_clave" id="confirmar_clave" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-tag text-red"></i> Rol
                        </label>
                        <select name="id_rol" id="id_rol" class="form-select" required>
                            <option value="">Seleccione un rol...</option>
                            <?php foreach ($lista_roles as $rol): ?>
                                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
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

<!-- Script específico para usuarios -->
<script src="<?= URL_BASE ?>assets/js/usuarios.js"></script>