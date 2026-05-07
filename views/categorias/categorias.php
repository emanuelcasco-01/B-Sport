<?php
session_start();
require_once "../../config/conexion.php"; 

// Verificar permisos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. CONSULTA DE Categorías
$sql = "SELECT * FROM categoria ORDER BY id_categoria DESC";

$resultado = $conexion->query($sql);
$lista_categorias = $resultado->fetchAll(PDO::FETCH_ASSOC);



include_once "../layout/header.php"; 
?>

<!-- ============================================ -->
<!-- PAGE HEADER - CON COLOR DORADO                -->
<!-- ============================================ -->
<div class="page-header">
    <h1>
        <i class="bi bi-collection"></i>
        <span>Gestión de <span class="gold-text">Categorias</span></span>
    </h1>
    <div class="header-actions">
        <?php if ($_SESSION['id_rol'] == 1): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="prepararNuevo()">
                <i class="bi bi-plus-circle-dotted"></i> Registrar Nueva Categoria
            </button>
        <?php endif; ?>
    </div>
</div>

<hr>

<!-- ============================================ -->
<!-- TABLA DE CATEGORÍAS                          -->
<!-- ============================================ -->
<div class="card shadow-sm border-0 fade-in">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Listado de Categorias
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblCategorias" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_categorias as $categoria): ?>
                        <tr>
                            <td>
                                <span class="fw-bold" style="color: var(--color-red);">
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($categoria['descripcion']) ?></td>
                            <td>
                                <span class="badge <?= ($categoria['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($categoria['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarCategoria" 
                                            data-id_categoria="<?= $categoria['id_categoria'] ?>"
                                            data-nombre="<?= htmlspecialchars($categoria['nombre']) ?>"
                                            data-descripcion="<?= htmlspecialchars($categoria['descripcion']) ?>"
                                            data-estado="<?= $categoria['estado'] ?>"
                                            title="Editar Categoría">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btnEliminarCategoria" 
                                            onclick="eliminarCategoria(this)"
                                            data-id_categoria="<?php echo $categoria['id_categoria']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($categoria['nombre']); ?>"
                                            data-descripcion="<?php echo htmlspecialchars   ($categoria['descripcion'] ?? ''); ?>">
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
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalTitulo" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formCategoria" class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);">
                <h5 class="modal-title" id="modalTitulo" style="color: var(--color-white);">
                    <i class="bi bi-bookmark-plus" style="color: var(--color-yellow);"></i>
                    Gestión de Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_categoria" id="id_categoria">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-card-text"></i> Nombre
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-body-text"></i> Descripción
                        </label>
                        <input type="text" name="descripcion" id="descripcion" class="form-control" required>
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
<!-- Modal para confirmar eliminación de categoría -->
<div class="modal fade" id="modalEliminarCategoria" tabindex="-1" aria-labelledby="modalEliminarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarCategoriaLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar Eliminación de Categoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="categoriaEliminarId">
                
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Advertencia:</strong> Está a punto de eliminar permanentemente esta categoría. Revise los datos antes de continuar.
                </div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Datos de la Categoría</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold" style="width: 30%;">Nombre:</td>
                                    <td><span id="eliminarNombreCategoria" class="fw-bold text-primary"></span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Descripción:</td>
                                    <td><span id="eliminarDescripcion" class="text-muted"></span></td>
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
                <button type="button" class="btn btn-danger" onclick="confirmarEliminarCategoria()">
                    <i class="bi bi-trash-fill me-1"></i>Eliminar Definitivamente
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>

<!-- Script específico para medidas -->
<script src="<?= URL_BASE ?>assets/js/categoria.js"></script>
