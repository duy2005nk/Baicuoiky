# Cửa hàng Ngày Mới

Dự án PHP và MySQL chạy trên XAMPP. Khách có thể duyệt/tìm/lọc sản phẩm, thêm và cập nhật giỏ hàng, rồi đặt hàng thanh toán khi nhận hàng. Đơn được lưu cùng chi tiết vào MySQL và tồn kho được cập nhật trong transaction.

## Cấu trúc

```text
app/
	Controllers/       Xử lý yêu cầu của cửa hàng
	Models/            Sản phẩm, đơn hàng và truy cập dữ liệu
	Views/
		products/        Danh sách sản phẩm
		cart/            Giỏ hàng
		checkout/        Thông tin đặt hàng
		orders/          Xác nhận đơn hàng
		admin/           Quản trị (mở rộng)
		auth/            Đăng nhập quản trị (mở rộng)
config/              Cấu hình ứng dụng và database
database/
	schema.sql         Bảng sản phẩm, đơn hàng và chi tiết đơn
	seeds/products.sql Dữ liệu mẫu
public/              Document root của Apache
	assets/css/        CSS
	assets/js/         Tìm kiếm và lọc sản phẩm
routes/              Khai báo route (mở rộng)
storage/logs/        Log runtime (không đưa lên Git)
tests/               Kiểm thử
```

## Chạy với XAMPP

1. Mở XAMPP Control Panel và khởi động Apache, MySQL.
2. Mở phpMyAdmin tại `http://localhost/phpmyadmin`, chọn Import và lần lượt import `database/schema.sql`, sau đó `database/seeds/products.sql`.
3. Từ thư mục dự án, chạy `C:\xampp\php\php.exe -S 127.0.0.1:8000 -t public`.
4. Mở `http://127.0.0.1:8000`.

Mặc định ứng dụng dùng `127.0.0.1`, database `simple_store`, user `root`, mật khẩu trống như cấu hình XAMPP thường gặp. Có thể ghi đè bằng biến môi trường `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`. Nếu MySQL chưa sẵn sàng, trang hiển thị catalog mẫu nhưng chức năng thêm giỏ và đặt hàng sẽ khóa đến khi kết nối database thành công.