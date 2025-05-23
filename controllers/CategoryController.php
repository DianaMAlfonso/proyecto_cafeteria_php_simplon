<?php
require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Muestra la lista de todas las categorías.
     */
    public function index() {
        $categories = $this->categoryModel->getAllCategories();
        include __DIR__ . '/../views/categories/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create() {
        include __DIR__ . '/../views/categories/create.php';
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'El nombre de la categoría es obligatorio.'];
                header('Location: /categories/create');
                exit();
            }

            if ($this->categoryModel->createCategory($name, $description)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Categoría creada con éxito.'];
                header('Location: /categories');
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al crear la categoría. Podría ser un nombre duplicado.'];
                header('Location: /categories/create');
                exit();
            }
        } else {
            http_response_code(405); // Método no permitido
            echo "Método de solicitud no permitido.";
        }
    }

    /**
     * Muestra el formulario para editar una categoría existente.
     * @param int $id El ID de la categoría a editar.
     */
    public function edit($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Categoría no encontrada.'];
            header('Location: /categories');
            exit();
        }
        include __DIR__ . '/../views/categories/edit.php';
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     * @param int $id El ID de la categoría a actualizar.
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'El nombre de la categoría es obligatorio.'];
                header("Location: /categories/edit/$id");
                exit();
            }

            if ($this->categoryModel->updateCategory($id, $name, $description)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Categoría actualizada con éxito.'];
                header('Location: /categories');
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al actualizar la categoría. Podría ser un nombre duplicado o ID inválido.'];
                header("Location: /categories/edit/$id");
                exit();
            }
        } else {
            http_response_code(405); // Método no permitido
            echo "Método de solicitud no permitido.";
        }
    }

    /**
     * Elimina una categoría.
     * @param int $id El ID de la categoría a eliminar.
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Usar POST para eliminar por seguridad
            $result = $this->categoryModel->deleteCategory($id);
            if ($result['success']) {
                $_SESSION['message'] = ['type' => 'success', 'text' => $result['message']];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => $result['message']];
            }
            header('Location: /categories');
            exit();
        } else {
            http_response_code(405); // Método no permitido
            echo "Método de solicitud no permitido.";
        }
    }
}
?>