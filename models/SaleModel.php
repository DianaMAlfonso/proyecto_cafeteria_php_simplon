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
            $stmt = $this->conn->prepare("INSERT INTO sales (product_id, quantity, user_id) VALUES (:product_id, :quantity, :user_id)");
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // Este bind ahora sí tiene su marcador de posición
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

    /**
     * Obtiene una venta por su ID.
     * @param int $saleId El ID de la venta.
     * @return array|null La venta o null si no se encuentra.
     */
    public function getSaleById($saleId)
    { // Nuevo método
        try {
            $stmt = $this->conn->prepare("
                SELECT
                    s.id AS sale_id,
                    s.product_id,
                    s.quantity,
                    s.sale_date,
                    p.name AS product_name,
                    p.stock AS current_product_stock, -- Necesario para recalcular stock al editar
                    p.price AS product_price,
                    u.name AS seller_name
                FROM sales s
                JOIN products p ON s.product_id = p.id
                LEFT JOIN users u ON s.user_id = u.id
                WHERE s.id = :sale_id
            ");
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener venta por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza una venta existente y ajusta el stock del producto. // Nuevo método
     * @param int $saleId El ID de la venta a actualizar.
     * @param int $newProductId El nuevo ID del producto (si cambia).
     * @param int $newQuantity La nueva cantidad vendida.
     * @return array Un array con 'success' (bool) y 'message' (string).
     */
    public function updateSale($saleId, $newProductId, $newQuantity)
    {
        $this->conn->beginTransaction();
        try {
            // 1. Obtener la venta original para obtener el producto y la cantidad anterior
            $originalSale = $this->getSaleById($saleId);
            if (!$originalSale) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Venta original no encontrada.'];
            }

            $oldProductId = $originalSale['product_id'];
            $oldQuantity = $originalSale['quantity'];

            // 2. Revertir stock del producto original
            $stmt = $this->conn->prepare("UPDATE products SET stock = stock + :old_quantity WHERE id = :old_product_id");
            $stmt->bindParam(':old_quantity', $oldQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':old_product_id', $oldProductId, PDO::PARAM_INT);
            $stmt->execute();

            // 3. Obtener stock del nuevo producto (o el mismo si no cambió) para validación
            $stmt = $this->conn->prepare("SELECT stock FROM products WHERE id = :new_product_id FOR UPDATE");
            $stmt->bindParam(':new_product_id', $newProductId, PDO::PARAM_INT);
            $stmt->execute();
            $newProductData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$newProductData) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Producto seleccionado para la venta no encontrado.'];
            }

            $currentStock = $newProductData['stock'];

            // 4. Validar si hay suficiente stock para la nueva cantidad
            if ($currentStock < $newQuantity) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Stock insuficiente para la nueva cantidad del producto.'];
            }

            // 5. Restar la nueva cantidad del stock
            $stmt = $this->conn->prepare("UPDATE products SET stock = stock - :new_quantity WHERE id = :new_product_id");
            $stmt->bindParam(':new_quantity', $newQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':new_product_id', $newProductId, PDO::PARAM_INT);
            $stmt->execute();

            // 6. Actualizar la venta
            $stmt = $this->conn->prepare("UPDATE sales SET product_id = :new_product_id, quantity = :new_quantity WHERE id = :sale_id");
            $stmt->bindParam(':new_product_id', $newProductId, PDO::PARAM_INT);
            $stmt->bindParam(':new_quantity', $newQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Venta actualizada con éxito.'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error en updateSale: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar la venta: ' . $e->getMessage()];
        }
    }

    /**
     * Elimina una venta y devuelve el stock al producto. // Nuevo método
     * @param int $saleId El ID de la venta a eliminar.
     * @return array Un array con 'success' (bool) y 'message' (string).
     */
    public function deleteSale($saleId)
    {
        $this->conn->beginTransaction();
        try {
            // 1. Obtener la venta para saber qué producto y cantidad devolver
            $sale = $this->getSaleById($saleId);
            if (!$sale) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Venta no encontrada para eliminar.'];
            }

            // 2. Devolver el stock al producto
            $stmt = $this->conn->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :product_id");
            $stmt->bindParam(':quantity', $sale['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $sale['product_id'], PDO::PARAM_INT);
            $stmt->execute();

            // 3. Eliminar la venta
            $stmt = $this->conn->prepare("DELETE FROM sales WHERE id = :sale_id");
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Venta eliminada con éxito y stock devuelto.'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error en deleteSale: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar la venta: ' . $e->getMessage()];
        }
    }
}
