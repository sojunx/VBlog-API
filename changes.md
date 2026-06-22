# Chi tiết thay đổi của Branch: `refactor_auth_API`

Tài liệu này ghi lại các thay đổi được thực hiện trên branch `refactor_auth_API` nhằm hỗ trợ lưu trữ cả Access Token (AT) và Refresh Token (RT) qua HTTP-Only Cookie và trả về thông tin user khi đăng nhập thành công.

---

## 1. Khởi tạo Cơ sở dữ liệu (Database Setup)
Trước khi cập nhật API, toàn bộ các bảng dữ liệu chưa được khởi tạo. Đã thực thi các lệnh sau:
* Chạy migration để dựng cấu trúc bảng: `vendor/bin/phinx migrate -e local`
* Chạy seeder để nạp dữ liệu người dùng mẫu: `vendor/bin/phinx seed:run -e local` (Bao gồm tài khoản: `user@localhost.com` mật khẩu `user`).

---

## 2. Chi tiết các tệp thay đổi

### 📂 `src/Actions/Users/AuthenticateUserAction.php`
* **Thay đổi:**
  * Thêm cookie `access_token` vào tiêu đề phản hồi (`Set-Cookie`) bên cạnh `refresh_token`.
  * Thay đổi cấu trúc dữ liệu trả về trong JSON body của response. Thay vì trả về `access_token` trực tiếp trong body, API giờ đây chỉ trả về đối tượng `user` chứa thông tin cơ bản:
    ```json
    {
      "user": {
        "id": "uuid-string",
        "email": "user@example.com",
        "created_at": "YYYY-MM-DD HH:MM:SS"
      }
    }
    ```

### 📂 `src/Actions/Users/RegrantUserAccessAction.php`
* **Thay đổi:**
  * Khi client gọi endpoint làm mới token (`GET /users/refresh`), hệ thống sẽ trả về đồng thời cả `access_token` mới và `refresh_token` mới dưới dạng cookies.

### 📂 `src/Actions/Users/LogoutUserAction.php`
* **Thay đổi:**
  * Sửa logic xóa cookie: Khi người dùng đăng xuất, cả hai cookie `access_token` và `refresh_token` đều sẽ được xóa khỏi trình duyệt bằng cách thiết lập lại Max-Age bằng 0 (`Max-Age=0`).

### 📂 `src/Middlewares/AuthMiddleware.php`
* **Thay đổi (Hỗ trợ xác thực linh hoạt):**
  * Cập nhật lại cách lấy token xác thực: Middleware sẽ ưu tiên kiểm tra token nằm trong Cookie `access_token` của request trước.
  * Nếu không tìm thấy trong Cookie, middleware sẽ tự động chuyển sang đọc từ header `Authorization: Bearer <token>` để đảm bảo tính tương thích ngược với các client cũ.
