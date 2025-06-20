# Cafetería Alianza: Sistema de Gestión de Ventas e Inventario
Sistema web para la gestión eficiente de ventas, inventario, productos y usuarios para la cafetería "Alianza". Desarrollado en PHP con arquitectura MVC.


## ✨ Características

* **Gestión de Usuarios:** CRUD completo para administradores y vendedores.
* **Gestión de Productos:** CRUD de productos con control de stock.
* **Gestión de Categorías:** CRUD para organizar los productos.
* **Gestión de Ventas:** Registro y seguimiento de transacciones.
* **Autenticación y Autorización:** Login/Logout de usuarios y control de acceso basado en roles.
* **Restablecimiento de Contraseña:** Funcionalidad para recuperar el acceso.
* **Reporting Básico:** (Ej: Productos más vendidos, productos con más stock).


## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 8.2+
* **Base de Datos:** MySQL
* **Frontend:** Bootstrap, JavaScript
* **Servidor Web:** Apache
* **Control de Versiones:** Git

##  Requisitos Previos

Asegúrate de tener instalado lo siguiente:

* **Servidor Web con PHP:**
* **XAMPP** (recomendado para Windows/macOS)    
* **MySQL:** Incluido en XAMPP
* **Composer:** (Si gestionas dependencias de PHP con Composer).


## 🚀 Instalación y Configuración         

Sigue estos pasos para configurar el proyecto en tu entorno local:

1.  **Clona el repositorio:**
    ```bash
    git clone [https://github.com/DianaMAlfonso/proyecto_cafeteria_php_simplon.git]
    cd cafeteria_alianza
    ```

2.  **Configura tu servidor web:**
    * Copia el contenido de la carpeta `cafeteria_alianza` a la carpeta `htdocs` de XAMPP (o tu directorio raíz del servidor web).
    * Asegúrate de que la carpeta `public` sea accesible directamente, o configura un Virtual Host para apuntar a ella.

3.  **Configura la base de datos:**
    * Abre `phpMyAdmin` (o tu cliente MySQL preferido).
    * Crea una nueva base de datos llamada `cafeteria_db`.
    * Importa el archivo `database.sql` (ubicado en `cafeteria_alianza/database/database.sql`) en la base de datos `cafeteria_db`.

4.  **Configura las credenciales de la base de datos:**
    * Abre el archivo `config/Database.php`.
    * Actualiza las siguientes constantes con tus credenciales de MySQL:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'cafeteria_db');
        ```

5.  **Accede a la aplicación:**
    * Abre tu navegador y ve a `http://localhost/cafeteria_alianza/public/`
    * Deberías ver la página de inicio de sesión.
    

## 💡 Uso de la Aplicación

1.  **Inicio de Sesión:**
    * Accede a la página de login (la primera vez usarás las credenciales definidas en `database.sql` o registrarás una nueva cuenta).
    * Credenciales iniciales de ejemplo (si tu `database.sql` las incluye):
        * **Usuario Admin:** `admin@example.com` / `password123`
        * **Usuario Empleado:** `empleado@example.com` / `password123`

2.  **Panel de Administración:**
    * Después de iniciar sesión, serás redirigido al panel principal.
    * Utiliza el menú de navegación para acceder a las secciones de **Productos**, **Usuarios**, **Categorías** y **Ventas**.

3.  **Gestión de Productos:**
    * Navega a "Productos".
    * Puedes `Añadir Nuevo Producto`, `Editar` existentes o `Eliminar` productos. El stock se actualiza automáticamente con las ventas.

4.  **Registrar Ventas:**
    * Ve a la sección "Ventas".
    * Selecciona los productos y las cantidades para registrar una nueva venta. El stock se descontará automáticamente.

## 📂 Estructura del Proyecto

## . ├── config/ # Archivos de configuración de la BD, etc. ├── controllers/ # Lógica de los controladores (ej: AuthController.php) ├── database/ # Archivos SQL para la base de datos │ └── database.sql ├── models/ # Modelos de datos (ej: UserModel.php) ├── public/ # Punto de entrada de la aplicación (index.php, CSS, JS, imágenes) │ ├── css/ │ ├── js/ │ └── index.php ├── views/ # Vistas HTML/PHP (ej: auth/login.php, home.php) │ └── layout/ # Encabezado y pie de página comunes └── README.md # Este archivo

## ✉️ Contacto

Si tienes alguna pregunta o sugerencia, puedes contactarme en [dianakendel13@gmail.com].

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas! Si encuentras un error o tienes una mejora, por favor abre un 'issue' o envía un 'pull request'.