<?php
session_start();
require_once "../../config/conexion.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// Consulta ajustada a tu nueva estructura
$sql = "SELECT * FROM color ORDER BY id_color DESC";
$resultado = $conexion->query($sql);
$lista_color = $resultado->fetchAll(PDO::FETCH_ASSOC);

include_once "../layout/header.php"; 
?>

<div class="page-header">
    <h1>
        <i class="bi bi-palette text-red"></i>
        <span>Gestión de <span class="gold-text">Colores</span></span>
    </h1>
    <div class="header-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalColor" onclick="prepararNuevoColor()">
            <i class="bi bi-plus-circle"></i> Registrar Nuevo Color
        </button>
    </div>
</div>

<hr>

<div class="card shadow-sm border-0 fade-in">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-table me-2"></i> Listado de Colores
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblColor" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Nombre del Color</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_color as $color): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($color['nombre_color']) ?></td>
                            <td>
                                <span class="badge <?= ($color['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($color['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarColor" 
                                            data-id="<?= $color['id_color'] ?>"
                                            data-nombre_color="<?= htmlspecialchars($color['nombre_color']) ?>"
                                            data-estado="<?= $color['estado'] ?>"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btnEliminarColor" 
                                            data-id="<?= $color['id_color'] ?>"
                                            data-nombre_color="<?= htmlspecialchars($color['nombre_color']) ?>"
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

<div class="modal fade" id="modalColor" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <form id="formColor" class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTituloColor">
                    <i class="bi bi-palette me-2"></i> Gestión de Color
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_color" id="id_color">
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Nombre del Color</label>
                        <input type="text" name="nombre_color" id="nombre_color" class="form-control" 
                            placeholder="Ej: Rojo, Azul Francia, Negro..." required >
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Estado del Color</label>
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
                    <i class="bi bi-check-circle"></i> Guardar Color
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>
<script src="<?= URL_BASE ?>assets/js/color.js"></script>