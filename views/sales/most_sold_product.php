<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Reporte: Producto Más Vendido</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <?php if ($mostSoldProduct): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Producto: <?= htmlspecialchars($mostSoldProduct['name']) ?> (ID: <?= htmlspecialchars($mostSoldProduct['id']) ?>)</h5>
                <p class="card-text">Cantidad Total Vendida: <strong><?= htmlspecialchars($mostSoldProduct['total_sold']) ?></strong> unidades</p>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No se han registrado ventas aún o no hay productos para mostrar.
        </div>
    <?php endif; ?>

    <p class="mt-3"><a href="<?= BASE_URL ?>?controller=auth&action=home" class="btn btn-secondary">Volver al Inicio</a></p>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>