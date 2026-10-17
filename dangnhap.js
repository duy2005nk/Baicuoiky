function kiemTraDangNhap(taikhoan, matkhau) {
    // Ràng buộc: Mật khẩu phải chứa ký tự @
    const coDauA = matkhau.includes('@');
    
    if (!coDauA) {
        return "Lỗi: Mật khẩu bắt buộc phải chứa ký tự @!";
    }
    return "Đăng nhập thành công!";
}

// Giả lập test tính năng
console.log(kiemTraDangNhap("user1", "123456"));   
console.log(kiemTraDangNhap("user1", "123456@"));
