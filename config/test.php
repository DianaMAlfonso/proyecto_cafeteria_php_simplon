<?php
require_once __DIR__ . '/../config/database.php';// Cambia la ruta según tu estructura de carpetas

try {
    $conn = getDbConnection();
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}