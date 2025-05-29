<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Reporte: Producto con Mayor Stock</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <?php if ($mostStockProduct): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Producto: <?= htmlspecialchars($mostStockProduct['name']) ?> (ID: <?= htmlspecialchars($mostStockProduct['id']) ?>)</h5>
                <p class="card-text">Stock Actual: <strong><?= htmlspecialchars($mostStockProduct['stock']) ?></strong> unidades</p>
                <p class="card-text">Referencia: <?= htmlspecialchars($mostStockProduct['reference']) ?></p>
                <p class="card-text">Precio: $<?= htmlspecialchars($mostStockProduct['price']) ?></p>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No hay productos registrados para mostrar.
        </div>
    <?php endif; ?>

    <p class="mt-3"><a href="<?= BASE_URL ?>?controller=auth&action=home" class="btn btn-secondary">Volver al Inicio</a></p>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
