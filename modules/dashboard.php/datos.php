<?php
require_once "../../config/conexion.php";

// Compras
$sqlCompras = "SELECT COUNT(*) as total FROM compra WHERE estado = 'ACTIVA'";
$totalCompras = $conexion->query($sqlCompras)->fetch_assoc()['total'];

// Ventas (por ahora en 0 si no tenés tabla)
$totalVentas = 0;

// Productos
$sqlProductos = "SELECT COUNT(*) as total FROM articulo WHERE estado = 1";
$totalProductos = $conexion->query($sqlProductos)->fetch_assoc()['total'];

// Stock bajo
$sqlStock = "SELECT COUNT(*) as total FROM articulo 
            WHERE stock <= stock_minimo AND estado = 1";
$stockBajo = $conexion->query($sqlStock)->fetch_assoc()['total'];

// Respuesta JSON
echo json_encode([
    "compras" => $totalCompras,
    "ventas" => $totalVentas,
    "productos" => $totalProductos,
    "stock" => $stockBajo
]);