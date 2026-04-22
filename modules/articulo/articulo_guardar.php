<?php 
session_start();
require_once "../../config/conexion.php";

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => false, 'msg' => 'Sesión no válida o expirada']);
    exit();
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit();
}

try {
    // ============================================
    // 1. RECIBIR Y SANITIZAR DATOS
    // ============================================
    
    $codigo_barra = isset($_POST['codigo_barra']) ? trim($_POST['codigo_barra']) : '';
    $nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'PRODUCTO';
    $es_producido = isset($_POST['es_producido']) ? intval($_POST['es_producido']) : 0;
    $id_categoria = isset($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $id_marca = isset($_POST['id_marca']) ? intval($_POST['id_marca']) : null;
    $id_unidad = isset($_POST['id_unidad']) ? intval($_POST['id_unidad']) : null;
    $id_iva = isset($_POST['id_iva']) ? intval($_POST['id_iva']) : null;
    $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
    $precio_compra = isset($_POST['precio_compra']) ? floatval($_POST['precio_compra']) : 0;
    $precio_venta = isset($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    
    // Usuario que crea
    $id_usuario = $_SESSION['id_usuario'];

    // ============================================
    // 2. VALIDACIONES
    // ============================================
    if (empty($nombre) || empty($id_categoria) || empty($id_marca) || empty($id_unidad) || empty($id_iva) || empty($precio_compra) || empty($precio_venta)) {
        echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
        exit;
    }

    // Validar longitud mínima del nombre
    if (strlen($nombre) < 3) {
        echo json_encode(['status' => false, 'msg' => 'El nombre debe tener al menos 3 caracteres']);
        exit();
    }
    
    // Validar caracteres permitidos en nombre
    if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\+\#\/\(\)]+$/", $nombre)) {
        echo json_encode(['status' => false, 'msg' => 'El nombre contiene caracteres no permitidos']);
        exit();
    }

    // Validar tipo (solo valores permitidos)
    $tipos_permitidos = ['PRODUCTO', 'INSUMO'];
    if (!in_array($tipo, $tipos_permitidos)) {
        echo json_encode(['status' => false, 'msg' => 'Tipo de artículo no válido']);
        exit();
    }
    
    // Validar es_producido (0 o 1)
    if (!in_array($es_producido, [0, 1])) {
        $es_producido = 0;
    }
    
    // Validar precios (no negativos)
    if ($precio_compras < 0) {
        echo json_encode(['status' => false, 'msg' => 'El precio de compra no puede ser negativo']);
        exit();
    }
    
    if ($precio_venta < 0) {
        echo json_encode(['status' => false, 'msg' => 'El precio de venta no puede ser negativo']);
        exit();
    }
    
    // Validar código de barras único (si se proporciona)
    if (!empty($codigo_barra)) {
        // Verificar longitud del código
        if (strlen($codigo_barra) > 50) {
            echo json_encode(['status' => false, 'msg' => 'El código de barras no puede exceder 50 caracteres']);
            exit();
        }
        
        // Verificar si ya existe
        $sql = "SELECT id_articulo FROM articulo WHERE codigo_barra = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$codigo_barra]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => false, 'msg' => 'El código de barras ya está registrado']);
            exit();
        }
    }
    
    // Verificar que la categoría exista y esté activa
    if ($id_categoria) {
        $sql = "SELECT id_categoria FROM categoria WHERE id_categoria = ? AND estado = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_categoria]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['status' => false, 'msg' => 'La categoría seleccionada no es válida']);
            exit();
        }
    }
    
    // Verificar que la marca exista y esté activa 
    if ($id_marca) {
        $sql = "SELECT id_marca FROM marca WHERE id_marca = ? AND estado = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_marca]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['status' => false, 'msg' => 'La marca seleccionada no es válida']);
            exit();
        }
    }
    
    // Verificar que la unidad exista y esté activa
    if ($id_unidad) {
        $sql = "SELECT id_unidad FROM unidad_medida WHERE id_unidad = ? AND estado = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_unidad]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['status' => false, 'msg' => 'La unidad de medida seleccionada no es válida']);
            exit();
        }
    }
    
    // Verificar que el IVA exista y esté activo
    if ($id_iva) {
        $sql = "SELECT id_iva FROM iva WHERE id_iva = ? AND estado = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_iva]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['status' => false, 'msg' => 'El IVA seleccionado no es válido']);
            exit();
        }
    }

    // ============================================
    // 3. INSERTAR EN LA BASE DE DATOS
    // ============================================
    
    // Convertir nombre a mayúsculas para estandarizar
    $nombre = strtoupper($nombre);
    
    // Preparar la consulta
    $sql = "INSERT INTO articulo (
                codigo_barra,
                nombre,
                tipo,
                es_producido,
                id_categoria,
                id_marca,
                id_unidad,
                id_iva,
                precio_compras,
                precio_venta,
                estado,
                creado_por,
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            )";
    
    $stmt = $conexion->prepare($sql);
    
    $resultado = $stmt->execute([
        $codigo_barra,
        $nombre,
        $tipo,
        $es_producido,
        $id_categoria,
        $id_marca,
        $id_unidad,
        $id_iva,
        $precio_compras,
        $precio_venta,
        $estado,
        $id_usuario
    ]);
    
    // Obtener el ID del artículo insertado
    $id_articulo = $conexion->lastInsertId();
    
    // ============================================
    // RESPUESTA
    // ============================================
    
    if ($resultado && $id_articulo) {
        echo json_encode([
            'status' => true,
            'msg' => 'Artículo registrado correctamente',
            'id_articulo' => $id_articulo,
            'data' => [
                'nombre' => $nombre,
                'codigo_barra' => $codigo_barra ?: 'N/A',
                'tipo' => $tipo
            ]
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'msg' => 'Error al registrar el artículo. Por favor, intente nuevamente.'
        ]);
    }

} catch (PDOException $e) {
    // Log del error (opcional)
    error_log("Error en articulo_guardar.php: " . $e->getMessage());
    
    // Mensaje amigable para el usuario
    $mensaje = 'Error técnico en el servidor';
    
    // En desarrollo, mostrar más detalles
    if ($_SERVER['SERVER_NAME'] === 'localhost') {
        $mensaje .= ': ' . $e->getMessage();
    }
    
    echo json_encode([
        'status' => false,
        'msg' => $mensaje
    ]);
    
} catch (Exception $e) {
    error_log("Error general en articulo_guardar.php: " . $e->getMessage());
    
    echo json_encode([
        'status' => false,
        'msg' => 'Error inesperado. Por favor, contacte al administrador.'
    ]);
}