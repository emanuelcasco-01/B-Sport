<?php
session_start();
require_once "../../config/conexion.php";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/auth/login.php");
    exit();
}

// 1. Cargar datos para los selectores del formulario
$categorias = $conexion->query("SELECT id_categoria, nombre FROM categoria WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);
$marcas     = $conexion->query("SELECT id_marca, nombre FROM marca WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);
$unidades   = $conexion->query("SELECT id_unidad, nombre FROM unidad_medida WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);
$ivas       = $conexion->query("SELECT id_iva, porcentaje FROM iva WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);
$tallas     = $conexion->query("SELECT id_talla, nombre_talla FROM talla WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);
$colores    = $conexion->query("SELECT id_color, nombre_color FROM color WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);

// 2. Consulta principal de artículos
$sql = "SELECT a.*, c.nombre as categoria, m.nombre as marca 
        FROM articulo a 
        LEFT JOIN categoria c ON a.id_categoria = c.id_categoria 
        LEFT JOIN marca m ON a.id_marca = m.id_marca 
        ORDER BY a.id_articulo DESC";
$lista_articulos = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);

include_once "../layout/header.php";
?>

<div class="page-header">
    <h1>
        <i class="bi bi-box-seam text-red"></i>
        <span>Gestión de <span class="gold-text">Artículos</span></span>
    </h1>
    <div class="header-actions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalArticulo" onclick="prepararNuevoArticulo()">
            <i class="bi bi-plus-circle"></i> Nuevo Artículo
        </button>
    </div>
</div>

<hr>

<div class="card shadow-sm border-0 fade-in">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-table me-2"></i> Listado de Artículos
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tblArticulo" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría/Marca</th>
                        <th>Tipo</th>
                        <th>Precio Venta</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_articulos as $a): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($a['codigo_barra'] ?: 'S/C') ?></span></td>
                            <td class="fw-bold"><?= htmlspecialchars($a['nombre']) ?></td>
                            <td>
                                <small class="d-block text-muted">Cat: <?= htmlspecialchars($a['categoria'] ?: 'N/A') ?></small>
                                <small class="d-block text-muted">Mar: <?= htmlspecialchars($a['marca'] ?: 'N/A') ?></small>
                            </td>
                            <td><span class="badge border text-dark"><?= $a['tipo'] ?></span></td>
                            <td class="fw-bold text-success"><?= number_format($a['precio_venta'], 0, ',', '.') ?> Gs.</td>
                            <td>
                                <span class="badge <?= ($a['estado'] == 1) ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ($a['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btnEditarArticulo" 
                                            data-id="<?= $a['id_articulo'] ?>"
                                            data-codigo="<?= htmlspecialchars($a['codigo_barra']) ?>"
                                            data-nombre="<?= htmlspecialchars($a['nombre']) ?>"
                                            data-tipo="<?= $a['tipo'] ?>"
                                            data-producido="<?= $a['es_producido'] ?>"
                                            data-categoria="<?= $a['id_categoria'] ?>"
                                            data-marca="<?= $a['id_marca'] ?>"
                                            data-unidad="<?= $a['id_unidad'] ?>"
                                            data-iva="<?= $a['id_iva'] ?>"
                                            data-compra="<?= $a['precio_compras'] ?>"
                                            data-venta="<?= $a['precio_venta'] ?>"
                                            data-estado="<?= $a['estado'] ?>"
                                            title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-info btnVerVariantes" 
                                            data-id="<?= $a['id_articulo'] ?>"
                                            data-nombre="<?= htmlspecialchars($a['nombre']) ?>"
                                            title="Gestionar Variantes">
                                        <i class="bi bi-layers"></i>
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

<!-- Modal para Artículo -->
<div class="modal fade" id="modalArticulo" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formArticulo" class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTituloArticulo">
                    <i class="bi bi-box-seam me-2"></i>Gestión de Artículo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_articulo" id="id_articulo">
                
                <div class="row g-3">
                    <!-- Primera fila -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Código Barra</label>
                        <input type="text" name="codigo_barra" id="codigo_barra" class="form-control" 
                            placeholder="Opcional" maxlength="50">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nombre del Artículo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre_articulo" class="form-control" required>
                    </div>

                    <!-- Segunda fila -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="PRODUCTO">PRODUCTO</option>
                            <option value="INSUMO">INSUMO</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">¿Es Producido?</label>
                        <select name="es_producido" id="es_producido" class="form-select">
                            <option value="1">SÍ (Producción propia)</option>
                            <option value="0" selected>NO (Compra-Venta)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Categoría <span class="text-danger">*</span></label>
                        <select name="id_categoria" id="id_categoria" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($categorias as $c): ?>
                                <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tercera fila -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Marca</label>
                        <select name="id_marca" id="id_marca" class="form-select">
                            <option value="">Seleccione...</option>
                            <?php foreach($marcas as $m): ?>
                                <option value="<?= $m['id_marca'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Unidad de Medida <span class="text-danger">*</span></label>
                        <select name="id_unidad" id="id_unidad" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($unidades as $u): ?>
                                <option value="<?= $u['id_unidad'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Impuesto (IVA) <span class="text-danger">*</span></label>
                        <select name="id_iva" id="id_iva" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($ivas as $i): ?>
                                <option value="<?= $i['id_iva'] ?>"><?= $i['porcentaje'] ?>%</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cuarta fila - Precios -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary">Precio de Compra</label>
                        <div class="input-group">
                            <span class="input-group-text">Gs.</span>
                            <input type="number" name="precio_compra" id="precio_compra" 
                                class="form-control"  min="0" value="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Precio de Venta</label>
                        <div class="input-group">
                            <span class="input-group-text">Gs.</span>
                            <input type="number" name="precio_venta" id="precio_venta" 
                                class="form-control"  min="0" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Guardar Artículo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Gestión de Variantes -->
<div class="modal fade" id="modalVariantes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="tituloModalVariantes">
                    <i class="bi bi-layers me-2"></i>Gestión de Variantes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoVariantes">
                <!-- Se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<?php include_once "../layout/footer.php"; ?>
<script src="<?= URL_BASE ?>assets/js/articulo.js"></script>