<?php
require_once __DIR__ . '/../models/SaleModel.php';
require_once __DIR__ . '/../models/ProductModel.php';


class SaleController {
    private $saleModel;
    private $productModel;

    public function __construct() {
        $this->saleModel = new SaleModel();
        $this->productModel = new ProductModel();
        // La verificación de acceso se hará en cada método según el rol necesario
    }

    // Método para verificar si el usuario es Vendedor o Admin (para acciones de venta)
    private function checkVendedorOrAdmin() {
        if (!isset($_SESSION['user_role']) || (!in_array($_SESSION['user_role'], ['admin', 'vendedor']))) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Acceso Denegado. Solo administradores y vendedores pueden acceder al módulo de ventas.'];
            header('Location: ' . BASE_URL . '?controller=auth&action=home');
            exit();
        }
    }

    // Método para verificar si el usuario es Admin (para reportes o gestión completa)
    private function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Acceso Denegado. Solo los administradores pueden realizar esta acción.'];
            header('Location: ' . BASE_URL . '?controller=auth&action=home');
            exit();
        }
    }

    /**
     * Muestra la lista de todas las ventas (Solo para Admin). // Nueva función
     */
    public function index() {
        $this->checkAdmin(); // Solo administradores pueden ver la lista completa de ventas
        $sales = $this->saleModel->getAllSales();
        include __DIR__ . '/../views/sales/list.php'; // Asegúrate de crear esta vista
    }

    /**
     * Muestra el formulario para realizar una venta y la lista de productos. (Para Vendedor y Admin)
     */
    public function showSaleForm() {
        $this->checkVendedorOrAdmin(); // Vendedores y Admins pueden acceder
        $products = $this->productModel->getAllProducts();
        include __DIR__ . '/../views/sales/sale_form.php';
    }

    /**
     * Procesa la solicitud de venta. (Para Vendedor y Admin)
     */
    public function processSaleRequest() {
        $this->checkVendedorOrAdmin(); // Vendedores y Admins pueden procesar ventas
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $quantity = $_POST['quantity'] ?? null;
            $userId = $_SESSION['user_id'] ?? null; // Obtener el ID del usuario de la sesión

            // Validaciones básicas de entrada
            if (!filter_var($productId, FILTER_VALIDATE_INT) || $productId <= 0) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'ID de producto inválido.'];
                header('Location: ' . BASE_URL . '?controller=sale&action=form');
                exit();
            }
            if (!filter_var($quantity, FILTER_VALIDATE_INT) || $quantity <= 0) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Cantidad inválida. Debe ser un número entero positivo.'];
                header('Location: ' . BASE_URL . '?controller=sale&action=form');
                exit();
            }
            if (empty($userId)) { // Asegurarse de que el userId esté disponible
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ID de usuario no disponible para registrar la venta. Por favor, inicie sesión de nuevo.'];
                header('Location: ' . BASE_URL . '?controller=auth&action=login');
                exit();
            }

            $result = $this->saleModel->processSale($productId, $quantity, $userId); // Pasar userId al modelo

            if ($result['success']) {
                $_SESSION['message'] = ['type' => 'success', 'text' => $result['message']];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => $result['message']];
            }
            header('Location: ' . BASE_URL . '?controller=sale&action=form');
            exit();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Método de solicitud no permitido.'];
            header('Location: ' . BASE_URL . '?controller=sale&action=form');
            exit();
        }
    }

    
    /**
     * Muestra el formulario para editar una venta. (Solo para Admin) // Nuevo método
     * @param int $saleId El ID de la venta a editar.
     */
    public function edit($saleId) {
        $this->checkAdmin();
        $sale = $this->saleModel->getSaleById($saleId);
        $products = $this->productModel->getAllProducts(); // Para el dropdown de productos
        
        if (!$sale) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Venta no encontrada.'];
            header('Location: ' . BASE_URL . '?controller=sale&action=index');
            exit();
        }
        include __DIR__ . '/../views/sales/create_edit.php'; // Usa una vista unificada
    }

    /**
     * Procesa la actualización de una venta. (Solo para Admin) // Nuevo método
     * @param int $saleId El ID de la venta a actualizar.
     */
    public function update($saleId) {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newProductId = $_POST['product_id'] ?? null;
            $newQuantity = $_POST['quantity'] ?? null;

            // Validaciones
            if (!filter_var($newProductId, FILTER_VALIDATE_INT) || $newProductId <= 0) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'ID de producto inválido.'];
                header('Location: ' . BASE_URL . '?controller=sale&action=edit&id=' . urlencode($saleId));
                exit();
            }
            if (!filter_var($newQuantity, FILTER_VALIDATE_INT) || $newQuantity <= 0) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Cantidad inválida. Debe ser un número entero positivo.'];
                header('Location: ' . BASE_URL . '?controller=sale&action=edit&id=' . urlencode($saleId));
                exit();
            }

            $result = $this->saleModel->updateSale($saleId, $newProductId, $newQuantity);

            if ($result['success']) {
                $_SESSION['message'] = ['type' => 'success', 'text' => $result['message']];
                header('Location: ' . BASE_URL . '?controller=sale&action=index');
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => $result['message']];
                // Redirigir de nuevo al formulario de edición con el error
                header('Location: ' . BASE_URL . '?controller=sale&action=edit&id=' . urlencode($saleId));
                exit();
            }
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Método no permitido para actualizar.'];
            header('Location: ' . BASE_URL . '?controller=sale&action=index');
            exit();
        }
    }

    /**
     * Elimina una venta. (Solo para Admin) // Nuevo método
     * @param int $saleId El ID de la venta a eliminar.
     */
    public function delete($saleId) {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->saleModel->deleteSale($saleId);

            if ($result['success']) {
                $_SESSION['message'] = ['type' => 'success', 'text' => $result['message']];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => $result['message']];
            }
            header('Location: ' . BASE_URL . '?controller=sale&action=index');
            exit();
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Método no permitido para eliminar.'];
            header('Location: ' . BASE_URL . '?controller=sale&action=index');
            exit();
        }
    }

    /**
     * Muestra el producto más vendido. (Solo para Admin)
     */
    public function showMostSoldProduct() {
        $this->checkAdmin(); // Solo administradores pueden ver este reporte
        $mostSoldProduct = $this->saleModel->getMostSoldProduct();
        include __DIR__ . '/../views/sales/most_sold_product.php';
    }
}