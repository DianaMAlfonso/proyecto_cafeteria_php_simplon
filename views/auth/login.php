<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg border-0" style="width: 100%; max-width: 400px;">
        <h4 class="mb-4 text-center">Iniciar Sesión</h4>

        <?php
        // Mostrar mensajes de error
        if (isset($error) && !empty($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        // Mostrar mensajes de éxito
        if (isset($success) && !empty($success)) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>

        <form action="<?= BASE_URL ?>?controller=auth&action=processLogin" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>

        <p class="mt-3 text-center">
            <a href="<?= BASE_URL ?>?controller=auth&action=register">¿No tienes cuenta? Regístrate aquí.</a>
        </p>
        <p class="mt-3 text-center">
            <a href="<?= BASE_URL ?>?controller=auth&action=forgotPassword">¿Olvidaste tu contraseña?</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>