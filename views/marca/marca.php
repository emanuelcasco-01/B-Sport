<?php
session_start();
require_once "../../config/conexion.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// Consulta a la tabla marca
$sql = "SELECT * FROM marca ORDER BY id_marca DESC";
$resultado = $conexion->query($sql);
$lista_marca = $resultado->fetchAll(PDO::FETCH_ASSOC);

include_once "../layout/header.php"; 
?>

<div class="page-header">
    <h1>
        <i class="bi bi-tag text-red"></i>
        <span>Gestión de <span class="gold-text">Marcas</span></span>
    </h1>
    <div class="header-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMarca" onclick="prepararNuevaMarca()">
            <i class="bi bi-plus-circle"></i> Registrar Nueva Marca
        </button>
    </div>
</div>

<hr>

<div class="card shadow-sm border-0 fade-in">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-table me-2"></i> Listado de Marcas
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblMarca" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Marca</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_marca as $marca): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($marca['nombre']) ?></td>
                            <td>
                                <span class="badge <?= ($marca['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($marca['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarMarca" 
                                            data-id="<?= $marca['id_marca'] ?>"
                                            data-nombre="<?= htmlspecialchars($marca['nombre']) ?>"
                                            data-estado="<?= $marca['estado'] ?>"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btnEliminarMarca" 
                                            data-id="<?= $marca['id_marca'] ?>"
                                            data-nombre="<?= htmlspecialchars($marca['nombre']) ?>"
                                            title="Desactivar">
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

<div class="modal fade" id="modalMarca" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form id="formMarca" class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTituloMarca">
                    <i class="bi bi-tag me-2"></i> Gestión de Marca
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_marca" id="id_marca">
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Nombre de la Marca</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" 
                            placeholder="Ej: Adidas, Nike, Puma..." required >
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Estado</label>
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
                    <i class="bi bi-check-circle"></i> Guardar Marca
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>
<script src="<?= URL_BASE ?>assets/js/marca.js"></script>