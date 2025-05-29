<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Gestión de Ventas</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <p>Aquí puedes ver el historial completo de ventas.</p>

    <div class="mb-3">
        <h4>Reportes Rápidos:</h4>
        <a href="<?= BASE_URL ?>?controller=product&action=most_stock" class="btn btn-secondary btn-sm mr-2">Producto con Mayor Stock</a>
        <a href="<?= BASE_URL ?>?controller=sale&action=most_sold" class="btn btn-secondary btn-sm">Producto Más Vendido</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Producto</th>
                <th>Referencia</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total Venta</th>
                <th>Vendedor</th>
                <th>Email Vendedor</th>
                <th>Fecha Venta</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['sale_id']) ?></td>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= htmlspecialchars($sale['product_reference']) ?></td>
                        <td>$<?= htmlspecialchars($sale['product_price']) ?></td>
                        <td><?= htmlspecialchars($sale['quantity']) ?></td>
                        <td>$<?= htmlspecialchars($sale['quantity'] * $sale['product_price']) ?></td>
                        <td><?= htmlspecialchars($sale['seller_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($sale['seller_email'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($sale['sale_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No se han registrado ventas aún.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>