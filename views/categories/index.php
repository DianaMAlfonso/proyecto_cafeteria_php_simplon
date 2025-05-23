<?php include __DIR__ . '/../partials/header.php'; ?>

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

    <p>
        <a href="/categories/create" class="btn btn-success">Crear Nueva Categoría</a>
    </p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['id']) ?></td>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td><?= htmlspecialchars($category['description']) ?></td>
                        <td>
                            <a href="/categories/edit/<?= htmlspecialchars($category['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="/categories/delete/<?= htmlspecialchars($category['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta categoría? Si tiene productos asociados, no se podrá eliminar.');">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay categorías registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>