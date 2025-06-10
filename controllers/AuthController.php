<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

// Incluir PHPMailer y el MailService
require_once __DIR__ . '/../vendor/autoload.php'; // Ruta a Composer autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use App\Services\MailService;
//require_once __DIR__ . '/../src/Services/MailService.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Método para mostrar el formulario de inicio de sesión
    public function showLoginForm()
    {
        // Si hay un mensaje de éxito/error de registro, se mostrará aquí
        $error = $_SESSION['error_message'] ?? '';
        $success = $_SESSION['success_message'] ?? '';
        unset($_SESSION['error_message']);
        unset($_SESSION['success_message']);
        include __DIR__ . '/../views/auth/login.php';
    }

    //Método para PROCESAR el inicio de sesión (solo POST)
   /* public function processLogin() CODIGO ORIGINAL
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error_message'] = "Por favor, ingresa tu email y contraseña.";
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: ' . BASE_URL . '?action=home');
            exit();
        } else {
            $_SESSION['error_message'] = "Email o contraseña incorrectos.";
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }
    }*/
    public function processLogin()//CODIGO SOLO PARA DEPURACION INICIO
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->showLoginForm();
        return;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Por favor, ingresa tu email y contraseña.";
        header('Location: ' . BASE_URL . '?controller=auth&action=login');
        exit();
    }

    $user = $this->userModel->findByEmail($email);

    // --- INICIO DE DEPURACIÓN ---
    error_log("DEBUG: Intentando login para email: " . $email);

    if (!$user) {
        error_log("DEBUG: Usuario NO encontrado para email: " . $email);
    } else {
        error_log("DEBUG: Usuario encontrado. ID: " . $user['id'] . ", Email: " . $user['email']);
        // NUNCA imprimas contraseñas en logs de producción
        // error_log("DEBUG: Contraseña almacenada (hash): " . $user['password']);
    }

    if ($user && password_verify($password, $user['password'])) {
        error_log("DEBUG: password_verify SÍ fue exitoso.");
        // --- FIN DE DEPURACIÓN ---

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        header('Location: ' . BASE_URL . '?action=home');
        exit();
    } else {
        // --- INICIO DE DEPURACIÓN ---
        error_log("DEBUG: password_verify NO fue exitoso. Redirigiendo a login.");
        // --- FIN DE DEPURACIÓN ---

        $_SESSION['error_message'] = "Email o contraseña incorrectos.";
        header('Location: ' . BASE_URL . '?controller=auth&action=login');
        exit();
    }
}//FIN DEPURACION

    // Método para cerrar sesión
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '?action=login');
        exit();
    }

    // Método para mostrar el formulario de registro
    public function showRegisterForm()
    {
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['error_message']);
        include __DIR__ . '/../views/auth/register.php';
    }

    // Método para PROCESAR el registro (solo POST)
    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'vendedor';

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error_message'] = "Todos los campos son obligatorios.";
            header('Location: ' . BASE_URL . '?controller=auth&action=register');
            exit();
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error_message'] = "Este email ya está registrado.";
            header('Location: ' . BASE_URL . '?controller=auth&action=register');
            exit();
        }

        if ($this->userModel->create($name, $email, $password, $role)) {
            $_SESSION['success_message'] = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        } else {
            $_SESSION['error_message'] = "Error al registrar el usuario.";
            header('Location: ' . BASE_URL . '?controller=auth&action=register');
            exit();
        }
    }

    // Método para mostrar el formulario de recuperación de contraseña.
    public function forgotPassword()
    {
        // Obtener mensajes de error si se redirige aquí desde sendResetEmail
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['error_message']);
        include __DIR__ . '/../views/auth/forgot_password.php';
    }

    // Método que envía un correo con el enlace de recuperación de contraseña
    public function sendResetEmail()
    {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $_SESSION['error_message'] = "Correo requerido."; // Usar error_message
            header('Location: ' . BASE_URL . '?controller=auth&action=forgotPassword');
            exit();
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $_SESSION['error_message'] = "Usuario no encontrado."; // Usar error_message
            header('Location: ' . BASE_URL . '?controller=auth&action=forgotPassword');
            exit();
        }

        $token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->userModel->saveResetToken($user['id'], $token, $expiry);

        $resetUrl = BASE_URL . "?controller=auth&action=resetForm&token=$token";

        $resetLink = "
            <a href='$resetUrl'
                style='display:inline-block;padding:10px 20px;font-size:16px;color:#fff;
                        background-color:#28a745;text-decoration:none;border-radius:5px;'>
                Recuperar contraseña
            </a>
        ";

        $subject = 'Recuperación de contraseña';
        $body = "
            <p>Hola <strong>{$user['name']}</strong>,</p>
            <p>Has solicitado restablecer tu contraseña.</p>
            <p>Haz clic en el siguiente botón para continuar:</p>
            <p>$resetLink</p>
            <p>Este enlace expirará en 1 hora.</p>
        ";

        try {
            $mailer = new MailService();
            $mailer->send($email, $user['name'], $subject, $body);

            $_SESSION['success_message'] = "Se ha enviado un correo con el enlace de recuperación."; // Usar success_message
            header('Location: ' . BASE_URL . '?controller=auth&action=login'); // Redirige al login
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error al enviar el correo: " . $e->getMessage(); // Usar error_message
            header('Location: ' . BASE_URL . '?controller=auth&action=forgotPassword'); // Redirige a forgotPassword
            exit();
        }
    }

    // Muestra el formulario para que el usuario introduzca una nueva contraseña
    public function resetForm()
    {
        $token = $_GET['token'] ?? '';
        // Obtener mensajes de error/éxito si se redirige aquí
        $error = $_SESSION['error_message'] ?? '';
        $success = $_SESSION['success_message'] ?? '';
        unset($_SESSION['error_message']);
        unset($_SESSION['success_message']);
        include __DIR__ . '/../views/auth/reset_password.php';
    }

    // Procesa la nueva contraseña enviada por el usuario
    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? ''; // Obtener la confirmación

        // Validar que las contraseñas coincidan
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error_message'] = "Las contraseñas no coinciden.";
            header('Location: ' . BASE_URL . '?controller=auth&action=resetForm&token=' . urlencode($token));
            exit();
        }
        // Validar longitud mínima
        if (strlen($newPassword) < 6) {
            $_SESSION['error_message'] = "La contraseña debe tener al menos 6 caracteres.";
            header('Location: ' . BASE_URL . '?controller=auth&action=resetForm&token=' . urlencode($token));
            exit();
        }


        $user = $this->userModel->findByToken($token);

        if (!$user || strtotime($user['token_expiry']) < time()) {
            $_SESSION['error_message'] = "Token inválido o expirado.";
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->userModel->updatePassword($user['id'], $hashed);

        $_SESSION['success_message'] = "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
        header('Location: ' . BASE_URL . '?controller=auth&action=login');
        exit();
    }
}
