<?php
session_start();

define('BASE_URL', '/cafeteria_alianza/public/index.php');

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/SaleController.php';

$controllerName = $_GET['controller'] ?? 'auth';
$actionName = $_GET['action'] ?? 'login';

//Lógica de seguridad para redirigir si no está logueado y no es una acción de autenticación
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
        $controller = new UserController();
        switch ($actionName) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store': // Añadido para POST de creación de usuario
                $controller->store();
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'update': // Añadido para POST de actualización de usuario
                $id = $_GET['id'] ?? null;
                $controller->update($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=user&action=index');
                exit();
                break;
        }
        break;

    case 'product':
        $controller = new ProductController(); // Aquí se llama al constructor y checkAdmin()
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
                $controller->delete($id);
                break;
            case 'most_stock':
                $controller->showProductWithMostStock();
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=product&action=index');
                exit();
                break;
        }
        break;

    case 'category':
        $controller = new CategoryController();
        switch ($actionName) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'update':
                $id = $_GET['id'] ?? null;
                $controller->update($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=category&action=index');
                exit();
                break;
        }
        break;

    case 'sale':
        $controller = new SaleController();
        switch ($actionName) {
            case 'index': // Nueva acción para listar todas las ventas
                $controller->index();
                break;
            case 'form':
                $controller->showSaleForm();
                break;
            case 'process':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->processSaleRequest();
                } else {
                    http_response_code(405);
                    echo "Método de solicitud no permitido.";
                }
                break;
            case 'most_sold':
                $controller->showMostSoldProduct();
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=sale&action=form');
                exit();
                break;
        }
        break;

    default:
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?controller=auth&action=home');
        } else {
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
        }
        exit();
        break;
}
