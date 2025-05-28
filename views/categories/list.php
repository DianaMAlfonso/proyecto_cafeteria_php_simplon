<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h2>Gestión de Categorías</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <a href="<?= BASE_URL ?>?controller=category&action=create" class="btn btn-success mb-3">Crear Nueva Categoría</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['id']) ?></td>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>?controller=category&action=edit&id=<?= htmlspecialchars($category['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?= BASE_URL ?>?controller=category&action=delete&id=<?= htmlspecialchars($category['id']) ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta categoría?');">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay categorías registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>