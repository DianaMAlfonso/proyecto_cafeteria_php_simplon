<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg border-0" style="width: 100%; max-width: 400px;">

        <h4 class="mb-4 text-center">Restablecer contraseña</h4>
        <form action="<?= BASE_URL ?>?controller=auth&action=sendResetEmail" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label"><span>Introduce tu correo electrónico:</span></label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
        </form>

        <?php
        // Mostrar mensajes de error si existen
        if (isset($error) && !empty($error)) {
            echo "<div class='mt-3 alert alert-danger text-center'>$error</div>";
        }
        ?>
    </div>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>