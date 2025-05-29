<?php
require_once __DIR__ . '/../config/database.php';

class SaleModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDbConnection();
    }

    /**
     * Procesa una venta: valida stock, lo resta y registra la venta.
     * @param int $productId El ID del producto a vender.
     * @param int $quantity La cantidad a vender.
     * @param int $userId El ID del usuario que realiza la venta.
     * @return array Un array asociativo con 'success' (bool) y 'message' (string).
     */
    public function processSale($productId, $quantity, $userId)
    {
        $this->conn->beginTransaction();
        try {
            // 1. Obtener el stock actual del producto (FOR UPDATE para evitar condiciones de carrera)
            $stmt = $this->conn->prepare("SELECT stock FROM products WHERE id = :product_id FOR UPDATE");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Producto no encontrado.'];
            }

            $currentStock = $product['stock'];

            // 2. Validar stock
            if ($currentStock <= 0 || $currentStock < $quantity) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'No es posible realizar la venta. Stock insuficiente o agotado.'];
            }

            // 3. Restar del stock la cantidad vendida
            $newStock = $currentStock - $quantity;
            $stmt = $this->conn->prepare("UPDATE products SET stock = :new_stock WHERE id = :product_id");
            $stmt->bindParam(':new_stock', $newStock, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            // 4. Registrar la venta en la tabla de ventas
            $stmt = $this->conn->prepare("INSERT INTO sales (product_id, quantity) VALUES (:product_id, :quantity)");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Venta realizada con éxito.'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error en processSale: " . $e->getMessage()); // Logear el error para depuración
            return ['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()];
        }
    }

    /**
     * Obtiene el producto más vendido (sumando la cantidad total vendida por producto).
     * @return array|null El producto más vendido o null si no hay ventas.
     */
    public function getMostSoldProduct()
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT p.id, p.name, SUM(s.quantity) AS total_sold
                FROM products p
                JOIN sales s ON p.id = s.product_id
                GROUP BY p.id, p.name
                ORDER BY total_sold DESC
                LIMIT 1
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el producto más vendido: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Obtiene todas las ventas, incluyendo el nombre del producto y el nombre del vendedor. // Nueva función
     * @return array Un array de ventas.
     */
    public function getAllSales()
    {
        try {
            $stmt = $this->conn->query("
                SELECT
                    s.id AS sale_id,
                    s.quantity,
                    s.sale_date,
                    p.name AS product_name,
                    p.reference AS product_reference,
                    p.price AS product_price,
                    u.name AS seller_name,
                    u.email AS seller_email
                FROM sales s
                JOIN products p ON s.product_id = p.id
                LEFT JOIN users u ON s.user_id = u.id
                ORDER BY s.sale_date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las ventas: " . $e->getMessage());
            return [];
        }
    }
}
