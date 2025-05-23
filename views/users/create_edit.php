<?php require_once __DIR__ . '/../layout/header.php'; ?>

<?php
$is_edit = isset($user) && $user['id'];
$form_title = $is_edit ? 'Editar Usuario' : 'Crear Nuevo Usuario';
$action_url = $is_edit ? '<?= BASE_URL ?>?controller=user&action=edit' : '<?= BASE_URL ?>?controller=user&action=create';

// Precargar valores para edición
$id = $is_edit ? htmlspecialchars($user['id']) : '';
$name = $is_edit ? htmlspecialchars($user['name']) : '';
$email = $is_edit ? htmlspecialchars($user['email']) : '';
$role = $is_edit ? htmlspecialchars($user['role']) : 'vendedor';
?>

<h2><?= $form_title ?></h2>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mt-3">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form action="<?= BASE_URL ?>?controller=user&action=<?= isset($user) ? 'edit' : 'create' ?>" method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $id ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>" required> </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <?php if ($is_edit): ?>
                            <small class="form-text text-muted">Deja este campo vacío si no quieres cambiar la contraseña.</small>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" id="role" name="role" required> <option value="vendedor" <?= ($role == 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
                            <option value="admin" <?= ($role == 'admin') ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><?= $is_edit ? 'Actualizar Usuario' : 'Crear Usuario' ?></button>
                        <a href="<?= BASE_URL ?>?controller=user&action=index" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>