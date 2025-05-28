<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <?php
    $isEdit = isset($category['id']);
    $formAction = $isEdit ? BASE_URL . '?controller=category&action=update&id=' . htmlspecialchars($category['id']) : BASE_URL . '?controller=category&action=store';
    $pageTitle = $isEdit ? 'Editar Categoría' : 'Crear Nueva Categoría';
    ?>

    <h2><?= $pageTitle ?></h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <form action="<?= $formAction ?>" method="POST">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Actualizar Categoría' : 'Crear Categoría' ?></button>
        <a href="<?= BASE_URL ?>?controller=category&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>