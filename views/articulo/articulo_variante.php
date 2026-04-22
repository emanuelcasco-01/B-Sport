<?php
session_start();
require_once "../../config/conexion.php";

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id_articulo'])) {
    exit('Acceso no autorizado');
}

$id_articulo = intval($_GET['id_articulo']);

// Obtener datos del artículo
$stmt = $conexion->prepare("SELECT nombre, codigo_barra FROM articulo WHERE id_articulo = ?");
$stmt->execute([$id_articulo]);
$articulo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$articulo) {
    exit('Artículo no encontrado');
}

// Obtener tallas y colores activos
$tallas = $conexion->query("SELECT id_talla, nombre FROM talla WHERE estado = 1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$colores = $conexion->query("SELECT id_color, nombre FROM color WHERE estado = 1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Obtener variantes existentes
$sql = "SELECT av.*, t.nombre as talla, c.nombre as color 
        FROM articulo_variante av
        LEFT JOIN talla t ON av.id_talla = t.id_talla
        LEFT JOIN color c ON av.id_color = c.id_color
        WHERE av.id_articulo = ?
        ORDER BY t.nombre, c.nombre";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_articulo]);
$variantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="modal-header bg-dark text-white">
    <h5 class="modal-title">
        <i class="bi bi-layers me-2"></i>
        Variantes de: <span class="text-warning"><?= htmlspecialchars($articulo['nombre']) ?></span>
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <input type="hidden" id="id_articulo_variante" value="<?= $id_articulo ?>">
    
    <!-- Formulario para agregar variante -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-plus-circle me-2"></i>Agregar Nueva Variante
        </div>
        <div class="card-body">
            <form id="formVariante">
                <input type="hidden" name="id_articulo" value="<?= $id_articulo ?>">
                <input type="hidden" name="id_variante" id="id_variante">
                
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Código de Barras</label>
                        <input type="text" name="codigo_barra_variante" id="codigo_barra_variante" 
                            class="form-control" placeholder="Opcional" maxlength="50">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Talla <span class="text-danger">*</span></label>
                        <select name="id_talla" id="id_talla" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($tallas as $t): ?>
                                <option value="<?= $t['id_talla'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Color <span class="text-danger">*</span></label>
                        <select name="id_color" id="id_color" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($colores as $c): ?>
                                <option value="<?= $c['id_color'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100" id="btnGuardarVariante">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-secondary w-100 d-none" id="btnCancelarEdicion">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Stock Actual</label>
                        <input type="number" name="stock_actual" id="stock_actual" 
                            class="form-control" step="0.01" min="0" value="0.00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Stock Mínimo</label>
                        <input type="number" name="stock_minimo" id="stock_minimo" 
                            class="form-control" min="0" value="5">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de variantes existentes -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <i class="bi bi-table me-2"></i>Variantes Registradas
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="tblVariantes">
                    <thead class="table-light">
                        <tr>
                            <th>Código Barra</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th class="text-end">Stock Actual</th>
                            <th class="text-end">Stock Mín.</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($variantes)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No hay variantes registradas. Agregue una usando el formulario superior.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($variantes as $v): ?>
                                <tr id="variante_<?= $v['id_variante'] ?>">
                                    <td>
                                        <?php if ($v['codigo_barra_variante']): ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($v['codigo_barra_variante']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($v['talla']) ?></td>
                                    <td>
                                        <span class="d-flex align-items-center">
                                            <span class="color-preview me-2" style="width:20px;height:20px;border-radius:4px;background-color:<?= $v['color'] ?>;"></span>
                                            <?= htmlspecialchars($v['color']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold <?= $v['stock_actual'] <= $v['stock_minimo'] ? 'text-danger' : 'text-success' ?>">
                                            <?= number_format($v['stock_actual'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="text-end"><?= $v['stock_minimo'] ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btnEditarVariante" 
                                                    data-id="<?= $v['id_variante'] ?>"
                                                    data-codigo="<?= htmlspecialchars($v['codigo_barra_variante']) ?>"
                                                    data-talla="<?= $v['id_talla'] ?>"
                                                    data-color="<?= $v['id_color'] ?>"
                                                    data-stock="<?= $v['stock_actual'] ?>"
                                                    data-minimo="<?= $v['stock_minimo'] ?>"
                                                    title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btnEliminarVariante" 
                                                    data-id="<?= $v['id_variante'] ?>"
                                                    data-talla="<?= htmlspecialchars($v['talla']) ?>"
                                                    data-color="<?= htmlspecialchars($v['color']) ?>"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="bi bi-check-circle"></i> Finalizar
    </button>
</div>

<script>
// Código JavaScript específico para variantes
$(document).ready(function() {
    
    // Guardar variante
    $('#formVariante').on('submit', function(e) {
        e.preventDefault();
        
        const idVariante = $('#id_variante').val();
        const idArticulo = $('#id_articulo_variante').val();
        
        // Validar que no exista duplicado de talla+color
        const talla = $('#id_talla').val();
        const color = $('#id_color').val();
        
        if (!talla || !color) {
            alertify.error('Debe seleccionar talla y color');
            return false;
        }
        
        $.ajax({
            url: '<?= URL_BASE ?>modules/articulos/variante_guardar.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    alertify.success(res.msg);
                    // Recargar el contenido del modal
                    $('#modalVariantes .modal-content').load(
                        '<?= URL_BASE ?>views/articulos/modal_variantes.php?id_articulo=' + idArticulo
                    );
                } else {
                    alertify.error(res.msg);
                }
            },
            error: function() {
                alertify.error('Error de conexión');
            }
        });
    });
    
    // Editar variante
    $(document).on('click', '.btnEditarVariante', function() {
        const d = $(this).data();
        
        $('#id_variante').val(d.id);
        $('#codigo_barra_variante').val(d.codigo);
        $('#id_talla').val(d.talla);
        $('#id_color').val(d.color);
        $('#stock_actual').val(d.stock);
        $('#stock_minimo').val(d.minimo);
        
        $('#btnGuardarVariante').html('<i class="bi bi-pencil-square"></i> Actualizar');
        $('#btnCancelarEdicion').removeClass('d-none');
        
        // Scroll al formulario
        $('#formVariante')[0].scrollIntoView({ behavior: 'smooth' });
    });
    
    // Cancelar edición
    $('#btnCancelarEdicion').on('click', function() {
        $('#formVariante')[0].reset();
        $('#id_variante').val('');
        $('#btnGuardarVariante').html('<i class="bi bi-save"></i> Guardar');
        $(this).addClass('d-none');
    });
    
    // Eliminar variante
    $(document).on('click', '.btnEliminarVariante', function() {
        const id = $(this).data('id');
        const talla = $(this).data('talla');
        const color = $(this).data('color');
        const idArticulo = $('#id_articulo_variante').val();
        
        alertify.confirm(
            'Confirmar Eliminación',
            `¿Está seguro de eliminar la variante:<br><br>
             <strong>Talla:</strong> ${talla}<br>
             <strong>Color:</strong> ${color}?`,
            function() {
                $.ajax({
                    url: '<?= URL_BASE ?>modules/articulos/variante_eliminar.php',
                    type: 'POST',
                    data: { id_variante: id },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            alertify.success(res.msg);
                            $('#modalVariantes .modal-content').load(
                                '<?= URL_BASE ?>views/articulos/modal_variantes.php?id_articulo=' + idArticulo
                            );
                        } else {
                            alertify.error(res.msg);
                        }
                    }
                });
            },
            function() {
                alertify.warning('Operación cancelada');
            }
        ).set('labels', {ok: 'Sí, eliminar', cancel: 'Cancelar'});
    });
});
</script>