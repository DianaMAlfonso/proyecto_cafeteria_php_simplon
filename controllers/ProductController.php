<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class ProductController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->checkAdmin(); // Asegúrate de que solo los administradores puedan gestionar productos
    }

    private function checkAdmin()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Acceso Denegado. Solo los administradores pueden gestionar productos.'];
            header('Location: ' . BASE_URL . '?controller=auth&action=home'); // Redirige a la página de inicio o donde consideres
            exit();
        }
    }

    // Muestra la lista de productos
    public function index()
    {
        $products = $this->productModel->getAllProducts(); // Este método ya debería traer el category_name
        include __DIR__ . '/../views/products/list.php';
    }

    // Muestra el formulario para crear un producto
    public function create()
    {
        $error = '';
        $categories = $this->categoryModel->getAllCategories(); // Obtener todas las categorías
        $product = []; // Para que la vista no dé error si se usa $product en el formulario vacío

        // La lógica de POST se ha movido al método `store()` para una mejor separación de responsabilidades
        include __DIR__ . '/../views/products/create_edit.php';
    }

    // Procesa la creación de un producto (desde POST)
    public function store()
    {
        $error = '';
        $categories = $this->categoryModel->getAllCategories(); // Necesario si hay error y se vuelve a mostrar el formulario
        $product = []; // Para que la vista no dé error si se usa $product

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $reference = trim($_POST['reference'] ?? '');
            $price = $_POST['price'] ?? '';
            $weight = $_POST['weight'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $stock = $_POST['stock'] ?? '';
            $creation_date = date('Y-m-d H:i:s'); // Obtener la fecha actual

            // Validaciones
            if (empty($name) || empty($reference) || empty($price) || empty($stock) || empty($category_id) || empty($weight)) {
                $error = "Nombre, Referencia, Precio, Peso, Categoría y Stock son campos obligatorios.";
            } elseif (!is_numeric($price) || $price < 0) {
                $error = "El precio debe ser un número positivo.";
            } elseif (!is_numeric($stock) || $stock < 0) {
                $error = "El stock debe ser un número entero positivo.";
            } elseif (!is_numeric($weight) || $weight < 0) {
                $error = "El peso debe ser un número positivo.";
            } else {
                // Verificar si la referencia ya existe
                if ($this->productModel->findByReference($reference)) {
                    $error = "Esta referencia ya está registrada.";
                } else {
                    if ($this->productModel->createProduct($name, $reference, $price, $weight, $category_id, $stock, $creation_date)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Producto creado exitosamente.'];
                        header('Location: ' . BASE_URL . '?controller=product&action=index');
                        exit();
                    } else {
                        $error = "Error al crear el producto.";
                        $_SESSION['message'] = ['type' => 'error', 'text' => $error];
                    }
                }
            }
            // Si hay un error, lo pasamos a la sesión para mostrarlo en la vista
            if ($error) {
                $_SESSION['message'] = ['type' => 'error', 'text' => $error];
            }
        }
        // Si no es POST, o hubo un error en POST, volvemos a mostrar el formulario (con los datos pre-rellenados si es posible)
        include __DIR__ . '/../views/products/create_edit.php';
    }


    // Muestra el formulario para editar un producto
    public function edit($id)
    {
        $error = '';
        $product = $this->productModel->getProductById($id);
        $categories = $this->categoryModel->getAllCategories(); // Obtener todas las categorías

        if (!$product) {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Producto no encontrado."];
            header('Location: ' . BASE_URL . '?controller=product&action=index');
            exit();
        }

        // La lógica de POST se ha movido al método `update()`
        include __DIR__ . '/../views/products/create_edit.php';
    }

    // Procesa la actualización de un producto (desde POST)
    public function update($id)
    {
        $error = '';
        $product = $this->productModel->getProductById($id); // Se necesita para rellenar la vista si hay error
        $categories = $this->categoryModel->getAllCategories(); // Necesario si hay error y se vuelve a mostrar el formulario

        if (!$product) {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Producto no encontrado para actualizar."];
            header('Location: ' . BASE_URL . '?controller=product&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $reference = trim($_POST['reference'] ?? '');
            $price = $_POST['price'] ?? '';
            $weight = $_POST['weight'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $stock = $_POST['stock'] ?? '';
            // No se actualiza la fecha de creación en la edición, se mantiene la original

            // Validaciones
            if (empty($name) || empty($reference) || empty($price) || empty($stock) || empty($category_id) || empty($weight)) {
                $error = "Nombre, Referencia, Precio, Peso, Categoría y Stock son campos obligatorios.";
            } elseif (!is_numeric($price) || $price < 0) {
                $error = "El precio debe ser un número positivo.";
            } elseif (!is_numeric($stock) || $stock < 0) {
                $error = "El stock debe ser un número entero positivo.";
            } elseif (!is_numeric($weight) || $weight < 0) {
                $error = "El peso debe ser un número positivo.";
            } else {
                // Verificar si la referencia ya existe para OTRO producto (no para el que estamos editando)
                $existingProduct = $this->productModel->findByReference($reference);
                if ($existingProduct && $existingProduct['id'] != $id) {
                    $error = "Esta referencia ya está registrada para otro producto.";
                } else {
                    if ($this->productModel->updateProduct($id, $name, $reference, $price, $weight, $category_id, $stock)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => "Producto actualizado exitosamente."];
                        header('Location: ' . BASE_URL . '?controller=product&action=index');
                        exit();
                    } else {
                        $error = "Error al actualizar el producto.";
                        $_SESSION['message'] = ['type' => 'error', 'text' => $error];
                    }
                }
            }
            if ($error) {
                $_SESSION['message'] = ['type' => 'error', 'text' => $error];
                // Para que la vista `create_edit.php` tenga los datos del producto actual y el error
                // Es importante que la variable $product se mantenga con los datos actuales para rellenar el formulario.
            }
        }
        include __DIR__ . '/../views/products/create_edit.php';
    }


    // Elimina un producto
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Asegurarse que la eliminación sea via POST
            if ($id) {
                if ($this->productModel->deleteProduct($id)) { // Usar deleteProduct() del ProductModel
                    $_SESSION['message'] = ['type' => 'success', 'text' => "Producto eliminado exitosamente."];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => "Error al eliminar el producto. Asegúrate de que no tenga ventas asociadas."];
                }
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => "ID de producto no especificado para eliminar."];
            }
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Método no permitido para eliminar."];
        }
        header('Location: ' . BASE_URL . '?controller=product&action=index');
        exit();
    }

    // Muestra el producto con mayor stock.
    public function showProductWithMostStock()
    {
        $mostStockProduct = $this->productModel->getProductWithMostStock();
        include __DIR__ . '/../views/reports/most_stock_product.php';
    }
}