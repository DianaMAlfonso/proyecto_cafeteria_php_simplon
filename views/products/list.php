<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Gestión de Productos</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <p>
        <a href="<?= BASE_URL ?>?controller=product&action=create" class="btn btn-success">Crear Nuevo Producto</a>
    </p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Referencia</th>
                <th>Precio</th>
                <th>Peso</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Fecha de Creación</th>
                <th>Acciones</th>
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
                        <td><?= htmlspecialchars($product['weight']) ?></td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td> <td><?= htmlspecialchars($product['stock']) ?></td>
                        <td><?= htmlspecialchars($product['creation_date']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>?controller=product&action=edit&id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?= BASE_URL ?>?controller=product&action=delete&id=<?= htmlspecialchars($product['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No hay productos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>