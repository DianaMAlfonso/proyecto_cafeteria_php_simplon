<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Método para mostrar el formulario de inicio de sesión
    public function showLoginForm() {
        include __DIR__ . '/../views/auth/login.php';
    }

    // Método para PROCESAR el inicio de sesión (solo POST)
    public function processLogin() {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Por favor, ingresa tu email y contraseña.";
            include __DIR__ . '/../views/auth/login.php';
            return; // Detiene la ejecución para mostrar el error
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // session_start() ya se llama en public/index.php, no es necesario aquí.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: /cafeteria_alianza/public/index.php?action=home');
            exit();
        } else {
            $error = "Email o contraseña incorrectos.";
            include __DIR__ . '/../views/auth/login.php';
            return;
        }
    }

    // Método para cerrar sesión
    public function logout() {
        session_unset(); // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión
        header('Location: /cafeteria_alianza/public/index.php?action=login');
        exit();
    }

    // Método para mostrar el formulario de registro
    public function showRegisterForm() {
        include __DIR__ . '/../views/auth/register.php';
    }

    // Método para PROCESAR el registro (solo POST)
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'vendedor';

        if (empty($name) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios.";
            include __DIR__ . '/../views/auth/register.php';
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $error = "Este email ya está registrado.";
            include __DIR__ . '/../views/auth/register.php';
            return;
        }

        if ($this->userModel->create($name, $email, $password, $role)) {
            $success = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
            include __DIR__ . '/../views/auth/login.php';
            return;
        } else {
            $error = "Error al registrar el usuario.";
            include __DIR__ . '/../views/auth/register.php';
            return;
        }
    }
}
?>