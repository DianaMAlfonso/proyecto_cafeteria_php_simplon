<?php include __DIR__ . '/layout/header.php'; ?>

<div class="jumbotron text-center">
    <h1 class="display-4">¡Bienvenido a la Cafetería Alianza!</h1>
    <p class="lead">Sistema de Gestión de Inventario y Ventas</p>
    <hr class="my-4">
    <p>Has iniciado sesión como: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (Rol: <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>)</p>
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <p>Como administrador, puedes:</p>
        <ul class="list-unstyled">
            <li><a href="<?= BASE_URL ?>?controller=user&action=index" class="btn btn-info btn-sm mb-2">Gestionar Usuarios</a></li>
            <li><a href="<?= BASE_URL ?>?controller=product&action=index" class="btn btn-info btn-sm mb-2">Gestionar Productos</a></li>
            <li><a href="<?= BASE_URL ?>?controller=category&action=index" class="btn btn-info btn-sm mb-2">Gestionar Categorías</a></li>
            <li><a href="<?= BASE_URL ?>?controller=sale&action=index" class="btn btn-info btn-sm mb-2">Gestionar Ventas</a></li>
            <li><a href="<?= BASE_URL ?>?controller=sale&action=form" class="btn btn-info btn-sm mb-2">Realizar Ventas</a></li>
            </ul>
    <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'vendedor'): ?>
        <p>Como vendedor, puedes:</p>
        <ul class="list-unstyled">
            <li><a href="<?= BASE_URL ?>?controller=sale&action=form" class="btn btn-info btn-sm mb-2">Realizar Ventas</a></li>
            <li><a href="<?= BASE_URL ?>?controller=product&action=index" class="btn btn-info btn-sm mb-2">Ver Productos Disponibles</a></li>
        </ul>
    <?php endif; ?>
    <p class="mt-4">
        <a class="btn btn-danger btn-lg" href="<?= BASE_URL ?>?controller=auth&action=logout" role="button">Cerrar Sesión</a>
    </p>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>