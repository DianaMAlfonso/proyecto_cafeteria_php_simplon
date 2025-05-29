<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Módulo de Ventas</h2>

    <?php
    // Mostrar mensajes de éxito o error
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo
    }
    ?>

    <h3>Realizar Nueva Venta</h3>
    <form action="<?= BASE_URL ?>?controller=sale&action=process" method="POST">
        <div class="form-group">
            <label for="product_id">ID del Producto:</label>
            <input type="number" class="form-control" id="product_id" name="product_id" required min="1">
        </div>
        <div class="form-group">
            <label for="quantity">Cantidad:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
        </div>
        <button type="submit" class="btn btn-primary">Realizar Venta</button>
    </form>

    <h3 class="mt-5">Productos Disponibles</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Referencia</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['reference']) ?></td>
                        <td>$<?= htmlspecialchars($product['price']) ?></td>
                        <td><?= htmlspecialchars($product['stock']) ?></td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay productos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>