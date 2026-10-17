<?php

class Store
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function products(): array
    {
        $statement = $this->database->query(
            'SELECT id, name, category, description, price, stock, image_path
             FROM products
             WHERE is_active = 1
             ORDER BY id'
        );

        return $statement->fetchAll();
    }

    public function findProduct(int $id): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, name, price, stock, is_active FROM products WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product ?: null;
    }

    public function cartItems(array $cart): array
    {
        $ids = array_values(array_filter(array_map('intval', array_keys($cart)), function ($id) {
            return $id > 0;
        }));

        if (!$ids) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $this->database->prepare(
            "SELECT id, name, price, stock, image_path
             FROM products
             WHERE is_active = 1 AND id IN ($placeholders)"
        );
        $statement->execute($ids);
        $products = [];

        foreach ($statement->fetchAll() as $product) {
            $productId = (int) $product['id'];
            $quantity = max(1, (int) ($cart[$productId] ?? 1));
            $product['quantity'] = $quantity;
            $product['line_total'] = round((float) $product['price'] * $quantity, 2);
            $products[$productId] = $product;
        }

        return array_values($products);
    }

    public function createOrder(array $cart, string $customerName, string $phone, string $address): array
    {
        $ids = array_values(array_filter(array_map('intval', array_keys($cart)), function ($id) {
            return $id > 0;
        }));

        if (!$ids) {
            throw new DomainException('Gio hang dang trong. Hay chon san pham truoc.');
        }

        sort($ids);
        $this->database->beginTransaction();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $statement = $this->database->prepare(
                "SELECT id, name, price, stock, is_active
                 FROM products
                 WHERE id IN ($placeholders)
                 ORDER BY id
                 FOR UPDATE"
            );
            $statement->execute($ids);
            $products = $statement->fetchAll();

            if (count($products) !== count($ids)) {
                throw new DomainException('Mot san pham trong gio khong con kha dung.');
            }

            $total = 0.0;
            $items = [];
            foreach ($products as $product) {
                $id = (int) $product['id'];
                $quantity = (int) ($cart[$id] ?? 0);

                if ((int) $product['is_active'] !== 1 || $quantity < 1 || $quantity > (int) $product['stock']) {
                    throw new DomainException('So luong san pham "' . $product['name'] . '" vuot qua ton kho.');
                }

                $unitPrice = (float) $product['price'];
                $lineTotal = round($unitPrice * $quantity, 2);
                $total += $lineTotal;
                $items[] = [
                    'id' => $id,
                    'name' => $product['name'],
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPrice, 2, '.', ''),
                    'line_total' => number_format($lineTotal, 2, '.', ''),
                ];
            }

            $total = round($total, 2);
            $orderStatement = $this->database->prepare(
                'INSERT INTO orders (customer_name, phone, address, total_amount)
                 VALUES (:customer_name, :phone, :address, :total_amount)'
            );
            $orderStatement->execute([
                'customer_name' => $customerName,
                'phone' => $phone,
                'address' => $address,
                'total_amount' => number_format($total, 2, '.', ''),
            ]);
            $orderId = (int) $this->database->lastInsertId();

            $itemStatement = $this->database->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, line_total)
                 VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :line_total)'
            );
            $stockStatement = $this->database->prepare(
                'UPDATE products SET stock = stock - :quantity WHERE id = :id'
            );

            foreach ($items as $item) {
                $itemStatement->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
                $stockStatement->execute([
                    'quantity' => $item['quantity'],
                    'id' => $item['id'],
                ]);
            }

            $this->database->commit();

            return ['id' => $orderId, 'total' => $total];
        } catch (Throwable $exception) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $exception;
        }
    }

    public static function sampleProducts(): array
    {
        return [
            ['id' => 1, 'name' => 'Tai nghe Studio One', 'category' => 'Âm thanh', 'description' => 'Âm thanh rõ nét, đệm tai mềm và kết nối Bluetooth ổn định.', 'price' => 1290000, 'stock' => 18, 'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85'],
            ['id' => 2, 'name' => 'Đồng hồ Minimal', 'category' => 'Phụ kiện', 'description' => 'Mặt số tối giản, dây đeo thoải mái cho mỗi ngày.', 'price' => 890000, 'stock' => 12, 'image_path' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=85'],
            ['id' => 3, 'name' => 'Bình giữ nhiệt Daily', 'category' => 'Nhà cửa', 'description' => 'Giữ nóng và lạnh, thiết kế gọn nhẹ để mang theo.', 'price' => 320000, 'stock' => 30, 'image_path' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=900&q=85'],
            ['id' => 4, 'name' => 'Balo Metro', 'category' => 'Phụ kiện', 'description' => 'Ngăn chứa laptop và vật dụng cá nhân, chất liệu bền đẹp.', 'price' => 760000, 'stock' => 9, 'image_path' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=85'],
            ['id' => 5, 'name' => 'Đèn bàn Arc', 'category' => 'Nhà cửa', 'description' => 'Ánh sáng ấm, điều chỉnh linh hoạt cho góc làm việc.', 'price' => 540000, 'stock' => 15, 'image_path' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=900&q=85'],
            ['id' => 6, 'name' => 'Giày chạy bộ Move', 'category' => 'Thời trang', 'description' => 'Đế êm và nhẹ, phù hợp cho những bước chân hằng ngày.', 'price' => 1150000, 'stock' => 7, 'image_path' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=85'],
        ];
    }
}