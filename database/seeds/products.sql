USE simple_store;

INSERT INTO products (id, name, category, description, price, stock, image_path, is_active) VALUES
  (1, 'Tai nghe Studio One', 'Âm thanh', 'Âm thanh rõ nét, đệm tai mềm và kết nối Bluetooth ổn định.', 1290000, 18, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85', 1),
  (2, 'Đồng hồ Minimal', 'Phụ kiện', 'Mặt số tối giản, dây đeo thoải mái cho mỗi ngày.', 890000, 12, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=85', 1),
  (3, 'Bình giữ nhiệt Daily', 'Nhà cửa', 'Giữ nóng và lạnh, thiết kế gọn nhẹ để mang theo.', 320000, 30, 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=900&q=85', 1),
  (4, 'Balo Metro', 'Phụ kiện', 'Ngăn chứa laptop và vật dụng cá nhân, chất liệu bền đẹp.', 760000, 9, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=85', 1),
  (5, 'Đèn bàn Arc', 'Nhà cửa', 'Ánh sáng ấm, điều chỉnh linh hoạt cho góc làm việc.', 540000, 15, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=900&q=85', 1),
  (6, 'Giày chạy bộ Move', 'Thời trang', 'Đế êm và nhẹ, phù hợp cho những bước chân hằng ngày.', 1150000, 7, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=85', 1)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  category = VALUES(category),
  description = VALUES(description),
  price = VALUES(price),
  stock = VALUES(stock),
  image_path = VALUES(image_path),
  is_active = VALUES(is_active);