<?php
// Inicia la sesión al principio de todo para asegurar que esté disponible
session_start();

// --- DEBUG: Confirma que este index.php se está ejecutando ---
error_log("DEBUG: index.php está siendo ejecutado desde: " . __FILE__);
// --- FIN DEBUG ---

// Define la URL base de tu aplicación
// Asegúrate de que esta URL sea la que usas en el navegador para acceder a la carpeta 'public'
// Por ejemplo: http://localhost/cafeteria_alianza/public/
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/cafeteria_alianza/public/');

// Incluye los controladores necesarios
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/SaleController.php';

// Obtiene el controlador y la acción de la URL, o usa valores por defecto
$controllerName = $_GET['controller'] ?? 'auth';
$actionName = $_GET['action'] ?? 'login';

// --- DEBUG: Muestra la solicitud actual y el estado de la sesión ---
error_log("DEBUG INDEX: Request: controller=" . $controllerName . ", action=" . $actionName);
error_log("DEBUG INDEX: Session user_id: " . ($_SESSION['user_id'] ?? 'NULL'));
// --- FIN DEBUG ---

// Lógica de seguridad para redirigir si el usuario no está logueado
// y la acción solicitada NO es una de las permitidas para usuarios no autenticados.
// ¡'processLogin' debe estar aquí para que el login pueda ejecutarse!
if (!isset($_SESSION['user_id']) && !in_array($actionName, ['login', 'register', 'forgotPassword', 'sendResetEmail', 'resetForm', 'resetPassword', 'processLogin'])) {
    error_log("DEBUG INDEX: Usuario no logueado y acción protegida. Redirigiendo a login.");
    header('Location: ' . BASE_URL . '?controller=auth&action=login');
    exit();
}

// Inicializa el controlador a null para evitar errores si no se encuentra el caso
$controller = null;

// Enrutador principal: determina qué controlador y acción ejecutar
switch ($controllerName) {
    case 'auth':
        $controller = new AuthController();
        switch ($actionName) {
            case 'login':
                // DEBUG: Agregamos logs específicos aquí para ver si entramos
                error_log("DEBUG INDEX: Entrando a la acción 'login'. Método: " . $_SERVER['REQUEST_METHOD']);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    error_log("DEBUG INDEX: Es POST, llamando a processLogin().");
                    $controller->processLogin();
                } else {
                    error_log("DEBUG INDEX: Es GET, llamando a showLoginForm().");
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
            case 'home': // La acción 'home' para usuarios logueados
                if (isset($_SESSION['user_id'])) {
                    // DEBUG: Indicamos que home.php se va a incluir
                    error_log("DEBUG INDEX: Incluyendo views/home.php para usuario logueado.");
                    include __DIR__ . '/../views/home.php';
                } else {
                    // Si intenta ir a home sin estar logueado, redirige a login
                    error_log("DEBUG INDEX: Intento de acceso a home sin login. Redirigiendo.");
                    header('Location: ' . BASE_URL . '?controller=auth&action=login');
                    exit();
                }
                break;
            case 'forgotPassword':
                $controller->forgotPassword();
                break;
            case 'sendResetEmail':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->sendResetEmail();
                } else {
                    header('Location: ' . BASE_URL . '?controller=auth&action=forgotPassword');
                    exit();
                }
                break;
            case 'resetForm':
                $controller->resetForm();
                break;
            case 'resetPassword':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->resetPassword();
                } else {
                    header('Location: ' . BASE_URL . '?controller=auth&action=login');
                    exit();
                }
                break;
            case 'processLogin': // Este caso ya no debería ser necesario si la seguridad inicial lo maneja
                // Sin embargo, lo mantenemos como fallback o si el formulario apunta aquí directamente
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    error_log("DEBUG INDEX: Llamada directa a processLogin desde switch.");
                    $controller->processLogin();
                } else {
                    error_log("DEBUG INDEX: Acceso a processLogin vía GET. Redirigiendo a login.");
                    header('Location: ' . BASE_URL . '?controller=auth&action=login');
                    exit();
                }
                break;
            default:
                // Redirige a home si está logueado, de lo contrario a login
                if (isset($_SESSION['user_id'])) {
                    header('Location: ' . BASE_URL . '?controller=auth&action=home');
                } else {
                    header('Location: ' . BASE_URL . '?controller=auth&action=login');
                }
                exit();
                break;
        }
        break;

    case 'user':
        // Asegúrate de que solo usuarios logueados puedan acceder a estas acciones
        if (!isset($_SESSION['user_id'])) {
            error_log("DEBUG INDEX: Acceso no autorizado a User controller. Redirigiendo a login.");
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }
        // Puedes añadir más lógica de permisos aquí (por rol)
        $controller = new UserController();
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
                header('Location: ' . BASE_URL . '?controller=user&action=index');
                exit();
                break;
        }
        break;

    case 'product':
        if (!isset($_SESSION['user_id'])) {
            error_log("DEBUG INDEX: Acceso no autorizado a Product controller. Redirigiendo a login.");
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }
        $controller = new ProductController();
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
        if (!isset($_SESSION['user_id'])) {
            error_log("DEBUG INDEX: Acceso no autorizado a Category controller. Redirigiendo a login.");
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }
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
        if (!isset($_SESSION['user_id'])) {
            error_log("DEBUG INDEX: Acceso no autorizado a Sale controller. Redirigiendo a login.");
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit();
        }
        $controller = new SaleController();
        switch ($actionName) {
            case 'index':
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
            case 'edit':
                $saleId = $_GET['id'] ?? null;
                $controller->edit($saleId);
                break;
            case 'update':
                $saleId = $_POST['sale_id'] ?? null;
                if (!$saleId && isset($_GET['id'])) {
                    $saleId = $_GET['id'];
                }
                $controller->update($saleId);
                break;
            case 'delete':
                $saleId = $_GET['id'] ?? null;
                $controller->delete($saleId);
                break;
            case 'most_sold':
                $controller->showMostSoldProduct();
                break;
            default:
                header('Location: ' . BASE_URL . '?controller=sale&action=index');
                exit();
                break;
        }
        break;

    default:
        // Si el controlador no es reconocido, redirige a home si está logueado, de lo contrario a login
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?controller=auth&action=home');
        } else {
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
        }
        exit();
        break;
}
?>
