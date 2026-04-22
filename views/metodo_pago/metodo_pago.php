<?php
session_start();
require_once "../../config/conexion.php"; 

// Verificar permisos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. CONSULTA DE MÉTODOS DE PAGO (Corregido el comentario)
$sql = "SELECT * FROM metodo_pago ORDER BY id_metodo_pago DESC";
$resultado = $conexion->query($sql);
$lista_metodo_pago = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Incluir el header (ya incluye navbar, sidebar y apertura de contenido)
include_once "../layout/header.php"; 

?>
<link rel="stylesheet" href="../assets/css/roles.css">

<div class="page-header">
    <h1>
        <i class="bi bi-currency-dollar"></i>
        <span>Gestión de <span class="gold-text">Métodos de Pago</span></span>
    </h1>
    <div class="header-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMetodoPago" onclick="prepararNuevoMetodoPago()">
            <i class="bi bi-plus-circle"></i> Registrar Nuevo Método de Pago
        </button>
    </div>
</div>

<hr>

<div class="card shadow-sm border-0 fade-in">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Listado de Métodos de Pago
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblMetodoPago" style="width:100%">
                <thead>
                    <tr>
                        <th>Método de Pago</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_metodo_pago as $metodo_pago): ?>
                        <tr>
                            <td>
                                <span class="fw-bold" style="color: var(--color-red);">
                                    <?= htmlspecialchars($metodo_pago['nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= ($metodo_pago['estado'] == 1) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($metodo_pago['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarMetodoPago" 
                                            data-id="<?= $metodo_pago['id_metodo_pago'] ?>"
                                            data-nombre="<?= htmlspecialchars($metodo_pago['nombre']) ?>"
                                            data-estado="<?= $metodo_pago['estado'] ?>"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    <button class="btn btn-outline-danger btnEliminarMetodoPago" 
                                            data-id="<?= $metodo_pago['id_metodo_pago'] ?>" 
                                            data-nombre="<?= htmlspecialchars($metodo_pago['nombre']) ?>"
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

<div class="modal fade" id="modalMetodoPago" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formMetodoPago" class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);">
                <h5 class="modal-title" id="modalTituloMetodoPago" style="color: var(--color-white);">
                    <i class="bi bi-currency-exchange me-2" style="color: var(--color-yellow);"></i>
                    Gestión de Método de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id_metodo_pago" id="id_metodo_pago">
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">
                            <i class="bi bi-tag text-red"></i> Nombre del Método de Pago
                        </label>
                        <input type="text" name="nombre" id="nombre" class="form-control" 
                            placeholder="Ej: Transferencia, Efectivo" required>
                    </div>
                    <div class="col-md-4">
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
                    <i class="bi bi-check-circle"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>

<script src="<?= URL_BASE ?>assets/js/metodo_pago.js"></script>