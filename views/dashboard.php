<?php require_once "layout/header.php"; ?>
<?php require_once "layout/nav.php"; ?>

<h2>Bienvenido, <?php echo $_SESSION['nombre']; ?></h2>
<p class="text-muted">Panel principal del sistema</p>

<div class="row mt-4">

    <div class="col-md-3">
        <div class="card bg-primary text-white p-3">
            <h5>Compras</h5>
            <p>Gestión de compras</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white p-3">
            <h5>Ventas</h5>
            <p>Gestión de ventas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white p-3">
            <h5>Producción</h5>
            <p>Control de producción</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white p-3">
            <h5>Pedidos</h5>
            <p>Gestión de pedidos</p>
        </div>
    </div>
</div>
<?php require_once "layout/footer.php"; ?>