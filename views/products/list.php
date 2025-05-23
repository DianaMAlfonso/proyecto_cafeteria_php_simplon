<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4 text-center">
    <h2>Gestión de Productos</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>?controller=product&action=create" class="btn btn-primary mb-3">Crear Nuevo Producto</a>

    <?php if (empty($products)): ?>
        <div class="alert alert-info" role="alert">
            No hay productos registrados.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Referencia</th>
                        <th>Precio</th>
                        <th>Peso</th>
                        <th>Categoría ID</th>
                        <th>Stock</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['reference']) ?></td>
                            <td><?= htmlspecialchars($product['price']) ?></td>
                            <td><?= htmlspecialchars($product['weight'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($product['category_id'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($product['stock']) ?></td>
                            <td><?= htmlspecialchars($product['created_at']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>?controller=product&action=edit&id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="<?= BASE_URL ?>?controller=product&action=delete&id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>