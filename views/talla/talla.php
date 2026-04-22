<?php
session_start();
require_once "../../config/conexion.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// Consulta ajustada a la tabla talla
$sql = "SELECT * FROM talla ORDER BY id_talla DESC";
$resultado = $conexion->query($sql);
$lista_talla = $resultado->fetchAll(PDO::FETCH_ASSOC);

include_once "../layout/header.php"; 
?>

<div class="page-header">
    <h1>
        <i class="bi bi-rulers text-red"></i>
        <span>Gestión de <span class="gold-text">Tallas</span></span>
    </h1>
    <div class="header-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTalla" onclick="prepararNuevaTalla()">
            <i class="bi bi-plus-circle"></i> Registrar Nueva Talla
        </button>
    </div>
</div>

<hr>

<div class="card shadow-sm border-0 fade-in">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-table me-2"></i> Listado de Tallas Registradas
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblTalla" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Sigla</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_talla as $talla): ?>
                        <tr>
                            <td><?= htmlspecialchars($talla['nombre_talla']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($talla['sigla']) ?></span></td>
                            <td>
                                <span class="badge <?= ($talla['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($talla['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarTalla" 
                                            data-id="<?= $talla['id_talla'] ?>"
                                            data-nombre_talla="<?= htmlspecialchars($talla['nombre_talla']) ?>"
                                            data-sigla="<?= htmlspecialchars($talla['sigla']) ?>"
                                            data-estado="<?= $talla['estado'] ?>"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btnEliminarTalla" 
                                            data-id="<?= $talla['id_talla'] ?>" 
                                            data-nombre_talla="<?= htmlspecialchars($talla['nombre_talla']) ?>"
                                            data-estado="<?= $talla['estado'] ?>"
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

<div class="modal fade" id="modalTalla" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formTalla" class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTituloTalla">
                    <i class="bi bi-rulers me-2"></i> Gestión de Talla
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_talla" id="id_talla">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre de la Talla</label>
                        <input type="text" name="nombre_talla" id="nombre_talla" class="form-control" 
                            placeholder="Ej: Mediano, Grande, Calzado 42" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Sigla</label>
                        <input type="text" name="sigla" id="sigla" class="form-control" 
                            placeholder="Ej: M, G, 42" required>
                    </div>
                    <div class="col-md-3">
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
                    <i class="bi bi-check-circle"></i> Guardar Talla
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>
<script src="<?= URL_BASE ?>assets/js/talla.js"></script>