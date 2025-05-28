<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <?php
    // Determina si es una creación o edición
    $isEdit = isset($product['id']);
    $formAction = $isEdit ? BASE_URL . '?controller=product&action=edit&id=' . htmlspecialchars($product['id']) : BASE_URL . '?controller=product&action=create';
    $pageTitle = $isEdit ? 'Editar Producto' : 'Crear Nuevo Producto';
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
        <?php if ($isEdit): // Si es edición, el ID puede ser útil, aunque ya esté en la URL ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Nombre del Producto:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="reference">Referencia:</label>
            <input type="text" class="form-control" id="reference" name="reference" value="<?= htmlspecialchars($product['reference'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Precio:</label>
            <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" required min="0" step="1">
        </div>

        <div class="form-group">
            <label for="weight">Peso:</label>
            <input type="number" class="form-control" id="weight" name="weight" value="<?= htmlspecialchars($product['weight'] ?? '') ?>" required min="0" step="1">
        </div>

        <div class="form-group">
            <label for="category_id">Categoría:</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>"
                        <?= ($isEdit && $product['category_id'] == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($product['stock'] ?? '') ?>" required min="0" step="1">
        </div>

        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Actualizar Producto' : 'Crear Producto' ?></button>
        <a href="<?= BASE_URL ?>?controller=product&action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>