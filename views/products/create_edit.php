<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card mt-5">
            <div class="card-header text-center">
                <h3><?= isset($product) ? 'Editar Producto' : 'Crear Nuevo Producto' ?></h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>?controller=product&action=<?= isset($product) ? 'edit' : 'create' ?>" method="POST">
                    <?php if (isset($product)): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= isset($product) ? htmlspecialchars($product['name']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="reference" class="form-label">Referencia</label>
                        <input type="text" class="form-control" id="reference" name="reference" value="<?= isset($product) ? htmlspecialchars($product['reference']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= isset($product) ? htmlspecialchars($product['price']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="weight" class="form-label">Peso (opcional)</label>
                        <input type="number" step="0.01" class="form-control" id="weight" name="weight" value="<?= isset($product) ? htmlspecialchars($product['weight']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">ID de Categoría (opcional)</label>
                        <input type="number" class="form-control" id="category_id" name="category_id" value="<?= isset($product) ? htmlspecialchars($product['category_id']) : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?= isset($product) ? htmlspecialchars($product['stock']) : '' ?>" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><?= isset($product) ? 'Actualizar Producto' : 'Crear Producto' ?></button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="<?= BASE_URL ?>?controller=product&action=index">Volver a Gestión de Productos</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>