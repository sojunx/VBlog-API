# Chi tiết thay đổi của BE Branch: `cookie_statuscode`

Tài liệu này ghi lại các thay đổi được thực hiện trên branch `cookie_statuscode` phía Backend (VBlog-API) nhằm tách biệt thời gian sống của Cookie/Token và thay đổi mã trạng thái HTTP (Status Code) khi Access Token hết hạn để phục vụ cơ chế làm mới Token tự động ở Frontend.

---

## 1. Tạo mới Exception `GoneException` (HTTP 410)

- **Tệp tạo mới:** `src/Exceptions/GoneException.php`
- **Nội dung:** Định nghĩa lớp ngoại lệ `GoneException` kế thừa từ `ApiException` với mã HTTP trả về là `410 Gone`.

---

## 2. Cập nhật mã lỗi khi Access Token hết hạn ở Middleware

- **Tệp thay đổi:** `src/Middlewares/AuthMiddleware.php`
- **Nội dung:** 
  - Đổi ngoại lệ ném ra khi Access Token hết hạn từ `UnauthorizedException` (trả về lỗi `401 Unauthorized`) thành `GoneException` (trả về lỗi `410 Gone`).
  - Giúp Frontend dễ dàng phân biệt được:
    - **Lỗi 410:** Access Token chỉ hết hạn tạm thời -> FE tự động gọi API `/refresh` lấy token mới mà không đăng xuất user.
    - **Lỗi 401:** Phiên đăng nhập (Refresh Token) hết hạn thực sự -> FE buộc phải đăng xuất user.

---

## 3. Tách biệt hằng số thời gian sống của Token và Cookie

- **Tệp thay đổi:** `src/Services/SessionService.php`
- **Nội dung:**
  - Định nghĩa riêng biệt các hằng số thời gian sống cho **Token lưu DB** và **Cookie ở trình duyệt**:
    - `ACCESS_TOKEN_EXPIRATION_TIME` & `REFRESH_TOKEN_EXPIRATION_TIME` (Hiệu lực ở Server/DB).
    - `ACCESS_TOKEN_COOKIE_EXPIRATION_TIME` & `REFRESH_TOKEN_COOKIE_EXPIRATION_TIME` (Thời gian lưu trữ cookie ở trình duyệt).
  - Hàm `generate()` tính toán thêm thuộc tính `cookie_expires_at` cho cả 2 token và trả về trong mảng session để các Action xử lý.

---

## 4. Sử dụng thời gian hết hạn cookie riêng biệt khi thiết lập HTTP Cookie

- **Tệp thay đổi:**
  - `src/Actions/Users/AuthenticateUserAction.php` (Đăng nhập)
  - `src/Actions/Users/RegrantUserAccessAction.php` (Làm mới token)
- **Nội dung:** 
  - Cập nhật thuộc tính `Expires` trong chuỗi cấu hình Cookie trả về cho trình duyệt.
  - Thay vì gán cả 2 cookie theo hạn của Refresh Token như trước, giờ đây cookie `access_token` sẽ nhận hạn từ `cookie_expires_at` của access token, và cookie `refresh_token` nhận hạn từ `cookie_expires_at` của refresh token một cách độc lập.
