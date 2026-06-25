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

---

# Chi tiết thay đổi của BE Branch: `refactor_user_feature`
**Thời gian:** 25/06/2026 14:26 (Local Time)

Tài liệu này ghi lại các thay đổi liên quan đến việc bổ sung cột `user_name` (trích xuất tự động từ email) vào bảng `users` và tích hợp phân quyền trả về chuỗi `role` (ví dụ: `admin`, `user`) sau khi đăng nhập và khi lấy thông tin tài khoản hiện tại.

---

## 1. Tạo mới Migration thêm cột `user_name` và chuyển đổi dữ liệu cũ
- **Tệp tạo mới:** `db/migrations/20260625071908_add_user_name_to_users_table.php`
- **Nội dung:**
  - Thêm cột `user_name` kiểu `string` (varchar(255)) vào bảng `users`.
  - Tự động thực thi câu lệnh SQL để cập nhật các bản ghi hiện có bằng cách lấy chuỗi trước ký tự `@` từ trường `email`:
    ```sql
    UPDATE users SET user_name = SUBSTRING_INDEX(email, '@', 1)
    ```

---

## 2. Cập nhật Repository lưu trữ thông tin User
- **Tệp thay đổi:** `src/Repositories/UsersRepository.php`
- **Nội dung:** 
  - Sửa đổi phương thức `insert()` để khi tạo mới tài khoản, tự động trích xuất `user_name` từ email gửi lên (sử dụng `explode('@', $email)[0]`) và ghi xuống cột `user_name` trong DB.

---

## 3. Thêm phương thức truy vấn mã Code của Role
- **Tệp thay đổi:** `src/Repositories/RolesRepository.php`
- **Nội dung:**
  - Thêm phương thức `findRoleCodeByUserId(string $user_id): ?string` thực hiện câu lệnh `JOIN` giữa bảng `roles` và `user_roles` để lấy trực tiếp chuỗi vai trò (`code` - như `'admin'`, `'user'`) của user trong đúng 1 câu lệnh truy vấn.

---

## 4. Cập nhật DTO trả về thông tin User
- **Tệp thay đổi:** `src/DTOs/UserDto.php`
- **Nội dung:**
  - Bổ sung thuộc tính `user_name` và `role` vào DTO trả về cho Client.
  - Sử dụng định dạng `user_name` (snake_case) để giữ nguyên cấu trúc đặt tên giống với Database theo yêu cầu.

---

## 5. Tích hợp Role vào API Đăng nhập và Lấy thông tin hiện tại
- **Tệp thay đổi:**
  - `src/Actions/Users/AuthenticateUserAction.php` (Đăng nhập)
  - `src/Actions/Users/GetUserAction.php` (Lấy thông tin User hiện tại)
- **Nội dung:** 
  - Inject thêm `RolesRepository` vào cả hai Action.
  - Lấy mã `role` tương ứng của người dùng từ Database và đưa vào `UserDto` để trả về trong response JSON cho Client.

---

## 6. Thêm các trường lưu trữ ảnh đại diện (Avatar) cho User
- **Tệp tạo mới:** `db/migrations/20260625083404_add_avatar_fields_to_users_table.php`
- **Nội dung:**
  - Thực hiện bổ sung 2 trường `avatar_url` (VARCHAR(255)) và `avatar_public_id` (VARCHAR(255)) kiểu nullable (mặc định `NULL`) vào bảng `users`.
  - Giúp lưu trữ đường dẫn ảnh đại diện (avatar_url) và mã quản lý ảnh trên dịch vụ lưu trữ đám mây Cloudinary (avatar_public_id) khi thực hiện tải ảnh đại diện lên.

---

## 7. Tích hợp Cloudinary Provider và Route Cập nhật Profile
- **Tệp thay đổi/tạo mới:**
  - `src/Services/CloudinaryService.php` (Tạo mới)
  - `config/dependencies.php` (Thay đổi)
  - `config/routes.php` (Thay đổi)
  - `src/Actions/Users/UpdateUserProfileAction.php` (Tạo mới)
  - `src/Repositories/UsersRepository.php` (Thay đổi)
  - `src/DTOs/UserDto.php` (Thay đổi)
- **Nội dung:**
  - Cài đặt thư viện `cloudinary/cloudinary_php` SDK v2 thông qua Composer.
  - Tạo service wrapper `CloudinaryService` hỗ trợ tải ảnh đại diện lên Cloudinary (qua luồng tạm thời PSR-7) và xóa ảnh cũ trên Cloudinary thông qua public ID.
  - Đăng ký `CloudinaryService` vào container PHP-DI bằng cách inject các cấu hình môi trường từ `.env`.
  - Bổ sung các phương thức cập nhật `updateAvatar()`, `updateUsername()` và `updatePassword()` vào `UsersRepository`.
  - Thêm thuộc tính `avatar_url` vào `UserDto` và giữ nguyên tên trường này gửi về cho client.
  - Định nghĩa API route `POST /api/v1/users/update` bảo vệ bởi `AuthMiddleware` để xử lý cập nhật hợp nhất của tài khoản (đổi mật khẩu, đổi tên người dùng, upload ảnh avatar).

