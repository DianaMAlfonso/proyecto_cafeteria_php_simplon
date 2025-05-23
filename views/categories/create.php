<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h2>Crear Nueva Categoría</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "<div class='alert alert-$messageType'>$messageText</div>";
        unset($_SESSION['message']);
    }
    ?>

    <form action="/categories/store" method="POST">
        <div class="form-group">
            <label for="name">Nombre de la Categoría:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Categoría</button>
        <a href="/categories" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>