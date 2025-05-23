<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class ProductController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->checkAdmin(); // Asegúrate de que solo los administradores puedan gestionar productos
    }

    private function checkAdmin() {
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . '?action=home&msg=Acceso Denegado');
            exit();
        }
    }

    // Muestra la lista de productos
    public function index() {
        $products = $this->productModel->getAllProducts();
        include __DIR__ . '/../views/products/list.php';
    }

    // Muestra el formulario para crear un producto y lo procesa al enviar
    public function create() {
        $error = '';
        $categories = $this->categoryModel->getAllCategories();
        $product = [];

        include __DIR__ . '/../views/products/create_edit.php';
    }
    
    public function store() {
        $error = '';
        $categories = $this->categoryModel->getAllCategories();
        $product = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $reference = $_POST['reference'] ?? '';
            $price = $_POST['price'] ?? '';
            $weight = $_POST['weight'] ?? '';
            $category_id = $_POST['category_id'] ?? null; // Asume null si no se envía o es opcional
            $stock = $_POST['stock'] ?? '';

            // Validaciones básicas
            if (empty($name) || empty($reference) || empty($price) || empty($stock)) {
                $error = "Nombre, Referencia, Precio y Stock son campos obligatorios.";
            } elseif (!is_numeric($price) || $price < 0) {
                $error = "El precio debe ser un número positivo.";
            } elseif (!is_numeric($stock) || $stock < 0) {
                $error = "El stock debe ser un número entero positivo.";
            } elseif (!empty($weight) && !is_numeric($weight)) { // Validar peso si no está vacío
                 $error = "El peso debe ser un número válido.";
            } else {
                // Verificar si la referencia ya existe
                if ($this->productModel->findByReference($reference)) {
                    $error = "Esta referencia ya está registrada.";
                } else {
                    // Si todo es válido, intenta crear el producto
                    if ($this->productModel->create($name, $reference, $price, $weight, $category_id, $stock)) {
                        $_SESSION['message'] = "Producto creado exitosamente.";
                        header('Location: ' . BASE_URL . '?controller=product&action=index');
                        exit();
                    } else {
                        $error = "Error al crear el producto.";
                    }
                }
            }
        }
        // Incluye la vista del formulario (para GET o si hay errores en POST)
        include __DIR__ . '/../views/products/create_edit.php';
    }

    // Muestra el formulario para editar un producto y lo procesa al enviar
    public function edit() {
        $error = '';
        $id = $_GET['id'] ?? null;
        $product = null;

        if ($id) {
            $product = $this->productModel->getProductById($id);
        }

        if (!$product) {
            $_SESSION['message'] = "Producto no encontrado.";
            header('Location: ' . BASE_URL . '?controller=product&action=index');
            exit();
        }

        // Si usas categorías, obténlas para el dropdown
        // $categories = $this->categoryModel->getAllCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $reference = $_POST['reference'] ?? '';
            $price = $_POST['price'] ?? '';
            $weight = $_POST['weight'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $stock = $_POST['stock'] ?? '';

            // Validaciones básicas (similar a create, pero con consideración para el ID)
            if (empty($name) || empty($reference) || empty($price) || empty($stock)) {
                $error = "Nombre, Referencia, Precio y Stock son campos obligatorios.";
            } elseif (!is_numeric($price) || $price < 0) {
                $error = "El precio debe ser un número positivo.";
            } elseif (!is_numeric($stock) || $stock < 0) {
                $error = "El stock debe ser un número entero positivo.";
            } elseif (!empty($weight) && !is_numeric($weight)) {
                 $error = "El peso debe ser un número válido.";
            } else {
                // Verificar si la referencia ya existe para OTRO producto (no para el que estamos editando)
                $existingProduct = $this->productModel->findByReference($reference);
                if ($existingProduct && $existingProduct['id'] != $id) {
                    $error = "Esta referencia ya está registrada para otro producto.";
                } else {
                    // Si todo es válido, intenta actualizar el producto
                    if ($this->productModel->update($id, $name, $reference, $price, $weight, $category_id, $stock)) {
                        $_SESSION['message'] = "Producto actualizado exitosamente.";
                        header('Location: ' . BASE_URL . '?controller=product&action=index');
                        exit();
                    } else {
                        $error = "Error al actualizar el producto.";
                    }
                }
            }
        }
        // Incluye la vista del formulario (para GET o si hay errores en POST)
        include __DIR__ . '/../views/products/create_edit.php';
    }

    // Elimina un producto
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            if ($this->productModel->delete($id)) {
                $_SESSION['message'] = "Producto eliminado exitosamente.";
            } else {
                $_SESSION['message'] = "Error al eliminar el producto.";
            }
        } else {
            $_SESSION['message'] = "ID de producto no especificado.";
        }
        header('Location: ' . BASE_URL . '?controller=product&action=index');
        exit();
    }
}
?>