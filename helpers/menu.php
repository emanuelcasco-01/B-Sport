<?php
$menu = [
   
    "Compras" => [
        "permiso" => 2,
        "icono" => "bi-cart-fill",
        "items" => [
            ["tipo" => "titulo", "nombre" => "Mantenimientos"],
            ["nombre" => "Proveedores", "ruta" => "views/mantenimiento/proveedores.php"],
            ["nombre" => "Metodo de Pago", "ruta" => "views/metodo_pago/metodo_pago.php"],
            ["nombre" => "Talla", "ruta" => "views/talla/talla.php"],
            ["nombre" => "Color", "ruta" => "views/color/color.php"],
            ["nombre" => "Artículos", "ruta" => "views/articulo/articulo.php"],
            ["nombre" => "Categorías", "ruta" => "views/mantenimiento/categorias.php"],
            ["nombre" => "Marcas", "ruta" => "views/marca/marca.php"],
            ["nombre" => "IVA", "ruta" => "views/mantenimiento/config.php"],
            ["nombre" => "Medidas", "ruta" => "views/mantenimiento/medidas.php"],
            ["tipo" => "titulo", "nombre" => "Operaciones"],
            ["nombre" => "Registrar Compras", "ruta" => "views/compras/nueva.php"],
            ["nombre" => "Ajustar Stock", "ruta" => "views/compras/stock.php"],
            ["nombre" => "Historial de compras", "ruta" => "views/compras/historial.php"],
            ["nombre" => "Pagos a proveedores", "ruta" => "views/compras/pagos.php"]
        ]
    ],

    "Ventas" => [
        "permiso" => 4,
        "icono" => "bi-cash-stack",
        "items" => [
            ["tipo" => "titulo", "nombre" => "Mantenimientos"],
            ["nombre" => "Clientes", "ruta" => "views/mantenimiento/clientes.php"],
            ["nombre" => "Bancos", "ruta" => "views/mantenimiento/bancos.php"],
            
            ["tipo" => "titulo", "nombre" => "Operaciones"],
            ["nombre" => "Punto de Venta", "ruta" => "views/ventas/nueva.php"],
            ["nombre" => "Caja (Apertura/Cierre)", "ruta" => "views/ventas/caja.php"],
            ["nombre" => "Historial de ventas", "ruta" => "views/ventas/historial.php"]
        ]
    ],

    "Producción" => [
        "permiso" => 3,
        "icono" => "bi-scissors",
        "items" => [
            ["tipo" => "titulo", "nombre" => "Mantenimientos"],
            ["nombre" => "Estados de Pedido", "ruta" => "views/mantenimiento/estados.php"],
            
            ["tipo" => "titulo", "nombre" => "Operaciones"],
            ["nombre" => "Gestión de Pedidos", "ruta" => "views/produccion/pedidos.php"],
            ["nombre" => "Órdenes de Producción", "ruta" => "views/produccion/ordenes.php"],
            ["nombre" => "Seguimiento Real", "ruta" => "views/produccion/seguimiento.php"]
        ]
    ],

    "Reportes" => [
        "permiso" => 5,
        "icono" => "bi-file-earmark-bar-graph",
        "items" => [
            ["nombre" => "Reportes de Compras", "ruta" => "views/reportes/compras.php"],
            ["nombre" => "Reportes de Ventas", "ruta" => "views/reportes/ventas.php"],
            ["nombre" => "Reportes de Producción", "ruta" => "views/reportes/produccion.php"]
        ]
    ],
     "Seguridad" => [
        "permiso" => 1,
        "icono" => "bi-shield-lock",
        "items" => [
            ["nombre" => "Usuarios", "ruta" => "views/usuarios/usuarios.php"],
            ["nombre" => "Roles y Permisos", "ruta" => "views/roles/roles.php"]
        ]
    ]
];

