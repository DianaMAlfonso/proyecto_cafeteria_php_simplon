<?php
session_start();

define('BASE_URL', '/cafeteria_alianza/public/index.php');

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
//require_once __DIR__ . '/../controllers/SaleController.php'; // Incluir el controlador de Ventas

$controllerName = $_GET['controller'] ?? 'auth';
$actionName = $_GET['action'] ?? 'login';

// Lógica de seguridad para redirigir si no está logueado y no es una acción de autenticación
if (!isset($_SESSION['user_id']) && !in_array($actionName, ['login', 'register'])) {
    header('Location: ' . BASE_URL . '?controller=auth&action=login');
    exit();
}

$controller = null; // Inicializa la variable controller para evitar errores

switch ($controllerName) {
    case 'auth':
        $controller = new AuthController(); // Instancia el controlador
        switch ($actionName) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->processLogin();
                } else {
                    $controller->showLoginForm();
                }
                break;
            case 'logout':
                $controller->logout();
                break;
            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->processRegister();
                } else {
                    $controller->showRegisterForm();
                }
                break;
            case 'home':
                if (isset($_SESSION['user_id'])) {
                    include __DIR__ . '/../views/home.php';
                } else {
                    header('Location: ' . BASE_URL . '?controller=auth&action=login');
                    exit();
                }
                break;
            default:
                if (isset($_SESSION['user_id'])) {
                    header('Location: ' . BASE_URL . '?controller=auth&action=home');
                } else {
                    header('Location: ' . BASE_URL . '?controller=auth&action=login');
                }
                break;
        }
        break;

    case 'user':
        // La verificación de administrador se hace dentro del UserController->checkAdmin()
        $controller = new UserController();
        switch ($actionName) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'edit':
                // Requiere un ID, asumiendo que se pasa via GET: ?controller=user&action=edit&id=X
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'delete':
                // Requiere un ID, asumiendo que se pasa via GET o POST: ?controller=user&action=delete&id=X
                $id = $_GET['id'] ?? null; // Podrías querer solo POST para eliminar
                $controller->delete($id);
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=user&action=index');
                break;
        }
        break;

    case 'product':
        $controller = new ProductController();
        switch ($actionName) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->store();
                } else {
                    $controller->create(); // Muestra el formulario para crear producto
                }
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update($id);
                } else {
                    $controller->edit($id); // Muestra el formulario para editar producto
                }
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            // Aquí puedes añadir rutas para los reportes de productos
            case 'most_stock':
                $controller->showProductWithMostStock();
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=product&action=index');
                break;
        }
        break;

    case 'category': // Nuevo caso para el controlador de categorías
        $controller = new CategoryController();
        switch ($actionName) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->store();
                } else {
                    $controller->create();
                }
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update($id);
                } else {
                    $controller->edit($id);
                }
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                // Para eliminar, se recomienda usar POST, así que la lógica ya está dentro del delete()
                $controller->delete($id);
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=category&action=index');
                break;
        }
        break;

    case 'sale': // Nuevo caso para el controlador de ventas
        $controller = new SaleController();
        switch ($actionName) {
            case 'form': // Para mostrar el formulario de venta
                $controller->showSaleForm();
                break;
            case 'process': // Para procesar la venta
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->processSaleRequest();
                } else {
                    http_response_code(405);
                    echo "Método de solicitud no permitido.";
                }
                break;
            case 'most_sold': // Para mostrar el producto más vendido
                $controller->showMostSoldProduct();
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=sale&action=form');
                break;
        }
        break;

    default:
        // Manejo por defecto para controladores no reconocidos
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?controller=auth&action=home');
        } else {
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
        }
        break;
}
?>