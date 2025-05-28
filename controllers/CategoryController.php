<?php
require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->checkAdmin(); // Asegura que solo los administradores puedan gestionar categorías
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Acceso Denegado. Solo los administradores pueden gestionar categorías.'];
            header('Location: ' . BASE_URL . '?controller=auth&action=home');
            exit();
        }
    }

    // Muestra la lista de categorías
    public function index()
    {
        $categories = $this->categoryModel->getAllCategories();
        include __DIR__ . '/../views/categories/list.php';
    }

    // Muestra el formulario para crear una categoría
    public function create()
    {
        $error = '';
        $category = []; // Para evitar errores en la vista si no hay datos
        include __DIR__ . '/../views/categories/create_edit.php';
    }

    // Procesa la creación de una categoría
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? ''); // Asegúrate de obtenerla

            if (empty($name)) {
                $error = "El nombre de la categoría es obligatorio.";
            } else {
                // --- AÑADIR ESTA LÍNEA DE DEPURACIÓN AQUÍ ---
            error_log("Intentando crear categoría con nombre: " . $name);//depuracion
                if ($this->categoryModel->createCategory($name, $description)) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Categoría creada exitosamente.'];
                    header('Location: ' . BASE_URL . '?controller=category&action=index');
                    exit();
                } else {
                    $error = "Error al crear la categoría.";
                }
            }
            if ($error) {
                $_SESSION['message'] = ['type' => 'error', 'text' => $error];
                $category['name'] = $name; // Pre-rellenar el formulario si hay un error
                include __DIR__ . '/../views/categories/create_edit.php';
            }
        } else {
             header('Location: ' . BASE_URL . '?controller=category&action=create'); // Evita accesos directos por GET
             exit();
        }
    }

    // Muestra el formulario para editar una categoría
    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Categoría no encontrada.'];
            header('Location: ' . BASE_URL . '?controller=category&action=index');
            exit();
        }
        $error = '';
        include __DIR__ . '/../views/categories/create_edit.php';
    }

    // Procesa la actualización de una categoría
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $error = "El nombre de la categoría es obligatorio.";
            } else {
                if ($this->categoryModel->updateCategory($id, $name)) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Categoría actualizada exitosamente.'];
                     header('Location: ' . BASE_URL . '?controller=category&action=index');
                    exit();
                } else {
                    $error = "Error al actualizar la categoría.";
                }
            }
            if ($error) {
                $_SESSION['message'] = ['type' => 'error', 'text' => $error];
                $category = ['id' => $id, 'name' => $name]; // Pre-rellenar el formulario si hay un error
                include __DIR__ . '/../views/categories/create_edit.php';
            }
        } else {
            header('Location: ' . BASE_URL . '?controller=category&action=edit&id=' . urlencode($id)); // Evita accesos directos por GET
            exit();
        }
    }

    // Elimina una categoría (POST request)
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Llama al modelo y obtiene el array de resultado
            $result = $this->categoryModel->deleteCategory($id);

            // Usa el array para establecer el mensaje de sesión
            if ($result['success']) {
                $_SESSION['message'] = ['type' => 'success', 'text' => $result['message']];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => $result['message']];
            }
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Método no permitido para eliminar.'];
        }
    }
}