<?php include __DIR__ . '/layout/header.php'; ?>

<div class="jumbotron text-center">
    <h1 class="display-4">¡Bienvenido a la Cafetería Alianza!</h1>
    <p class="lead">Sistema de Gestión de Inventario y Ventas</p>
    <hr class="my-4">
    <p>Has iniciado sesión como: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (Rol: <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>)</p>
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <p>Como administrador, puedes:</p>
        <ul>
            <a href="/cafeteria_alianza/public/index.php?controller=user&action=index" class="btn btn-info btn-sm">Gestionar Usuarios</a>
            <a href="<?= BASE_URL ?>?controller=product&action=index" class="btn btn-info btn-sm">Gestionar Productos</a>
            <li><a href="<?= BASE_URL ?>?controller=category&action=index" class="btn btn-info btn-sm">Gestionar Categorías</a></li>
            <li>Ver reportes de ventas (próximamente)</li>
        </ul>
    <?php else: ?>
        <p>Como vendedor, puedes:</p>
        <ul>
            <li>Realizar Ventas (próximamente)</li>
            <li>Ver productos disponibles (próximamente)</li>
        </ul>
    <?php endif; ?>
    <p class="mt-4">
        <a class="btn btn-danger btn-lg" href="/cafeteria_alianza/public/index.php?action=logout" role="button">Cerrar Sesión</a>
    </p>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>