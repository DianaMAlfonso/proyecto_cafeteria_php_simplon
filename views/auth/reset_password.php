    <?php require_once __DIR__ . '/../layout/header.php'; ?>

    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg border-0" style="width: 100%; max-width: 400px;">
            <h4 class="mb-4 text-center">Establecer Nueva Contraseña</h4>

            <?php
            // ESTA ES LA SECCIÓN CLAVE PARA MOSTRAR LOS MENSAJES
            if (isset($error) && !empty($error)) {
                echo "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($error) . "</div>";
            }
            if (isset($success) && !empty($success)) {
                echo "<div class='alert alert-success' role='alert'>" . htmlspecialchars($success) . "</div>";
            }
            ?>

            <form action="<?= BASE_URL ?>?controller=auth&action=resetPassword" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div class="mb-3">
                    <label for="new_password" class="form-label">Nueva Contraseña:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Restablecer Contraseña</button>
            </form>

            <p class="mt-3 text-center">
                <a href="<?= BASE_URL ?>?controller=auth&action=login">Volver al inicio de sesión</a>
            </p>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layout/footer.php'; ?>