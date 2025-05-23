<?php require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
        //session_start();
        $this->checkAdmin();
    }

    private function checkAdmin() {
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /cafeteria_alianza/public/index.php?action=home&msg=Acceso Denegado');
            exit();
        }
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        include __DIR__ . '/../views/users/list.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'vendedor';

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $error = "Todos los campos son obligatorios.";
                include __DIR__ . '/../views/users/create_edit.php';
                return;
            }

            if ($this->userModel->findByEmail($email)) {
                $error = "Este email ya está registrado.";
                include __DIR__ . '/../views/users/create_edit.php';
                return;
            }

            if ($this->userModel->create($name, $email, $password, $role)) {
                $_SESSION['message'] = "Usuario creado exitosamente.";
                header('Location: /cafeteria_alianza/public/index.php?controller=user&action=index');
                exit();
            } else {
                $error = "Error al crear el usuario.";
                include __DIR__ . '/../views/users/create_edit.php';
                return;
            }
        } else {
            include __DIR__ . '/../views/users/create_edit.php';
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $user = null;
        if ($id) {
            $user = $this->userModel->getUserById($id);
        }

        if (!$user) {
            $_SESSION['message'] = "Usuario no encontrado.";
            header('Location: /cafeteria_alianza/public/index.php?controller=user&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'vendedor';
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($email) || empty($role) || empty($id)) {
                $error = "Todos los campos son obligatorios.";
                include __DIR__ . '/../views/users/create_edit.php';
                return;
            }

            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] != $id) {
                $error = "Este email ya está registrado para otro usuario.";
                include __DIR__ . '/../views/users/create_edit.php';
                return;
            }

            if ($this->userModel->update($id, $name, $email, $role, $password)) {
                $_SESSION['message'] = "Usuario actualizado exitosamente.";
                header('Location: /cafeteria_alianza/public/index.php?controller=user&action=index');
                exit();
            } else {
                $error = "Error al actualizar el usuario.";
                include __DIR__ . '/../views/users/create_edit.php';
                return;
            }
        } else {
            include __DIR__ . '/../views/users/create_edit.php';
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            if ($this->userModel->delete($id)) {
                $_SESSION['message'] = "Usuario eliminado exitosamente.";
            } else {
                $_SESSION['message'] = "Error al eliminar el usuario.";
            }
        } else {
            $_SESSION['message'] = "ID de usuario no especificado.";
        }
        header('Location: /cafeteria_alianza/public/index.php?controller=user&action=index');
        exit();
    }
}
?>