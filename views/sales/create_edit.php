<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Editar Venta #<?= htmlspecialchars($sale['sale_id'] ?? '') ?></h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <form action="<?= BASE_URL ?>?controller=sale&action=update&id=<?= htmlspecialchars($sale['sale_id'] ?? '') ?>" method="POST">
        <input type="hidden" name="sale_id" value="<?= htmlspecialchars($sale['sale_id'] ?? '') ?>">

        <div class="form-group">
            <label for="product_id">Producto:</label>
            <select class="form-control" id="product_id" name="product_id" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($products as $productOption): ?>
                    <option value="<?= htmlspecialchars($productOption['id']) ?>"
                        <?= (isset($sale['product_id']) && $sale['product_id'] == $productOption['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($productOption['name']) ?> (Stock: <?= htmlspecialchars($productOption['stock']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Cantidad Vendida:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($sale['quantity'] ?? '') ?>" required min="1">
        </div>

        <p>Fecha de Venta Original: <strong><?= htmlspecialchars($sale['sale_date'] ?? 'N/A') ?></strong></p>
        <p>Vendedor: <strong><?= htmlspecialchars($sale['seller_name'] ?? 'N/A') ?></strong></p>

        <button type="submit" class="btn btn-primary">Actualizar Venta</button>
        <a href="<?= BASE_URL ?>?controller=sale&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>