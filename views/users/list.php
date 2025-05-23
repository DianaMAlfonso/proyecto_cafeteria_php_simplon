<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2>Gestión de Usuarios</h2>
<a href="<?= BASE_URL ?>?controller=user&action=create" class="btn btn-success mb-3">Crear Nuevo Usuario</a>

<?php if (empty($users)): ?>
    <div class="alert alert-info" role="alert">
        No hay usuarios registrados.
    </div>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>?controller=user&action=edit&id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="<?= BASE_URL ?>?controller=user&action=delete&id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>