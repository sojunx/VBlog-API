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

---

# Chi tiết thay đổi của BE Branch: `book_feature`
**Thời gian:** 26/06/2026 08:58 (Local Time)

Tài liệu này ghi lại các thay đổi liên quan đến việc cấu trúc lại cơ sở dữ liệu để chuyển đổi VBlog sang mô hình đánh giá sách (Book Review Blog) theo thiết kế ERD mới, di chuyển mối quan hệ danh mục từ bài viết sang sách và tích hợp hệ thống đánh giá bằng bình luận.

---

## 1. Tạo mới Migration thay đổi toàn bộ Schema cho Book Review
- **Tệp tạo mới:** `db/migrations/20260626015631_update_schema_for_book_reviews.php`
- **Nội dung:**
  - **Xóa bảng liên kết cũ:** `category_posts` để phục vụ việc chuyển dịch mối quan hệ danh mục.
  - **Tạo bảng `books`:** Lưu trữ thông tin sách được review (bao gồm `id`, `title`, `author`, `isbn` unique, `cover_image_url`, `created_at` sử dụng kiểu `DATETIME` để tránh lệch múi giờ).
  - **Tạo bảng `category_book`:** Thiết lập mối quan hệ nhiều-nhiều (Many-to-Many) giữa Danh mục và Sách thông qua khóa chính hỗn hợp `(category_id, book_id)`.
  - **Cập nhật bảng `categories`:** 
    - Đổi tên trường `name` thành `title` cho đồng bộ với thiết kế.
    - Bổ sung trường `slug` (VARCHAR(255)) cùng chỉ mục unique để phục vụ định tuyến thân thiện.
  - **Cập nhật bảng `posts`:**
    - Thay đổi kiểu dữ liệu khóa chính `id` từ `INT` thành `BIGINT UNSIGNED` tự tăng.
    - Bổ sung trường `book_id` (INT UNSIGNED, khóa ngoại liên kết bảng `books` với hành vi `ON DELETE CASCADE`).
    - Bổ sung trường `slug` (VARCHAR(255), unique index).
    - Bổ sung các trường quản lý: `summary` (tóm tắt review), `status` (trạng thái bài viết, default `'draft'`), `view_count` (lượt xem, default `0`), `published_at` (kiểu `DATETIME`), `deleted_at` (kiểu `DATETIME`, hỗ trợ xóa mềm - soft delete).
  - **Tạo bảng bình luận và đánh giá `comments`:** 
    - Chứa `id` khóa chính `BIGINT UNSIGNED`.
    - Liên kết tới bài viết `post_id` (BIGINT, khóa ngoại) và tài khoản bình luận `user_id` (VARCHAR(36) nullable, khóa ngoại hỗ trợ ẩn danh).
    - Chứa nội dung bình luận `content` (TEXT), điểm đánh giá bài viết `rating` (INT từ 1-5 sao, nullable), trạng thái bình luận `status` và thời gian tạo `created_at` (kiểu `DATETIME`).

---

## 2. Tạo mới Permission và phân quyền Admin cho việc thêm sách
- **Tệp tạo mới:** `db/migrations/20260626020513_add_book_create_permission.php`
- **Nội dung:** 
  - Thêm permission mới mang mã code `'book.create'` vào bảng `permissions`.
  - Tự động map permission này tới quyền của vai trò Admin (`role_id = 1`) trong bảng `role_permissions` để hạn chế quyền truy cập ở mức API.

---

## 3. Tạo mới Repository xử lý nghiệp vụ sách (Books)
- **Tệp tạo mới:** `src/Repositories/BooksRepository.php`
- **Nội dung:**
  - Kế thừa lớp `AbstractRepository` để tự động inject `PDO`.
  - Xây dựng phương thức `create()` để thêm sách mới và trả về khóa tự tăng vừa tạo.
  - Xây dựng phương thức `findById()` và `findByIsbn()` để hỗ trợ truy vấn và kiểm tra trùng lặp mã sách.

---

## 4. Xây dựng API tạo sách mới (chỉ có Admin mới được thực hiện)
- **Tệp tạo mới/thay đổi:**
  - `src/Actions/Books/CreateBookAction.php` (Tạo mới)
  - `config/routes.php` (Thay đổi)
- **Nội dung:**
  - Lớp `CreateBookAction` kế thừa `BAction` và khai báo cấu hình phân quyền thông qua biến:
    ```php
    protected string $requiredPermission = 'book.create';
    ```
    Điều này bắt buộc luồng Middleware kiểm tra xem `role_id` của tài khoản gửi request có tương ứng với permission `'book.create'` hay không (nếu không, tự động chặn và trả lỗi `403 Forbidden`).
  - Thực hiện kiểm tra tính hợp lệ dữ liệu gửi lên:
    - Bắt buộc phải có `title`, `author`, `isbn` và `cover_image_url`.
    - Thực hiện kiểm tra xem mã ISBN này đã tồn tại trong DB chưa, nếu trùng lặp ném ra `ConflictException`.
  - Đăng ký endpoint `POST /api/v1/books` bọc bởi `AuthMiddleware` trong file định tuyến `routes.php`.

---

## 5. Tạo mới Permission và phân quyền Admin cho việc xem/liệt kê sách
- **Tệp tạo mới:** `db/migrations/20260626023441_add_book_read_permission.php`
- **Nội dung:** 
  - Thêm permission mới mang mã code `'book.read'` vào bảng `permissions`.
  - Tự động map permission này tới quyền của vai trò Admin (`role_id = 1`) trong bảng `role_permissions` để hạn chế quyền truy cập ở mức API.

---

## 6. Cập nhật Repository và tạo BookDto
- **Tệp tạo mới/thay đổi:**
  - `src/Repositories/BooksRepository.php` (Thay đổi)
  - `src/DTOs/BookDto.php` (Tạo mới)
- **Nội dung:**
  - Bổ sung phương thức `findAll()` vào `BooksRepository` để lấy danh sách tất cả các cuốn sách từ cơ sở dữ liệu.
  - Tạo lớp `BookDto` chứa cấu trúc thuộc tính sạch (`id`, `title`, `author`, `isbn`, `cover_image_url`, `created_at`) để định dạng dữ liệu trả về cho API client.

---

## 7. Xây dựng API lấy chi tiết sách và lấy danh sách sách (Chỉ có Admin mới được thực hiện)
- **Tệp tạo mới/thay đổi:**
  - `src/Actions/Books/GetBookAction.php` (Tạo mới)
  - `src/Actions/Books/ListBooksAction.php` (Tạo mới)
  - `config/routes.php` (Thay đổi)
- **Nội dung:**
  - Cả hai lớp `GetBookAction` và `ListBooksAction` đều khai báo biến `$requiredPermission = 'book.read'` để bắt buộc quyền Admin truy cập thông qua AuthMiddleware.
  - `ListBooksAction` truy vấn danh sách sách từ `BooksRepository::findAll()`, định dạng qua `BookDto` và trả về mảng dữ liệu.
  - `GetBookAction` truy vấn sách theo ID từ `BooksRepository::findById()`, ném ra `NotFoundException` nếu không tồn tại, định dạng qua `BookDto` và trả về chi tiết.
  - Đăng ký các endpoints `GET /api/v1/books` và `GET /api/v1/books/{id}` bọc bởi `AuthMiddleware` trong file định tuyến `routes.php`.

---

## 8. Tạo mới Permission và phân quyền Admin cho việc xóa sách
- **Tệp tạo mới:** `db/migrations/20260626024537_add_book_delete_permission.php`
- **Nội dung:** 
  - Thêm permission mới mang mã code `'book.delete'` vào bảng `permissions`.
  - Tự động map permission này tới quyền của vai trò Admin (`role_id = 1`) trong bảng `role_permissions` để hạn chế quyền truy cập ở mức API.

---

## 9. Cập nhật phương thức xóa vào Repository
- **Tệp thay đổi:** `src/Repositories/BooksRepository.php`
- **Nội dung:** Bổ sung phương thức `delete(int $id)` thực thi câu lệnh SQL `DELETE FROM books WHERE id = ?` để hỗ trợ xóa sách.

---

## 10. Xây dựng API xóa sách theo ID (Chỉ có Admin mới được thực hiện)
- **Tệp tạo mới/thay đổi:**
  - `src/Actions/Books/DeleteBookAction.php` (Tạo mới)
  - `config/routes.php` (Thay đổi)
- **Nội dung:**
  - Lớp `DeleteBookAction` khai báo biến `$requiredPermission = 'book.delete'` để bắt buộc quyền Admin truy cập thông qua AuthMiddleware.
  - Action kiểm tra sự tồn tại của cuốn sách bằng `BooksRepository::findById()`, ném ra `NotFoundException` (404) nếu không tìm thấy.
  - Tiến hành xóa thông qua `BooksRepository::delete()` và trả về phản hồi JSON báo thành công.
  - Đăng ký endpoint `DELETE /api/v1/books/{id}` bọc bởi `AuthMiddleware` trong file định tuyến `routes.php`.

---

## 11. Tạo mới Permission và phân quyền Admin cho việc cập nhật sách
- **Tệp tạo mới:** `db/migrations/20260626025209_add_book_update_permission.php`
- **Nội dung:** 
  - Thêm permission mới mang mã code `'book.update'` vào bảng `permissions`.
  - Tự động map permission này tới quyền của vai trò Admin (`role_id = 1`) trong bảng `role_permissions` để hạn chế quyền truy cập ở mức API.

---

## 12. Cập nhật phương thức cập nhật động vào Repository
- **Tệp thay đổi:** `src/Repositories/BooksRepository.php`
- **Nội dung:** Bổ sung phương thức `update(int $id, array $data)` thực thi câu lệnh SQL động `UPDATE books SET ... WHERE id = :id` để cập nhật chỉ các trường được cung cấp (hỗ trợ cập nhật một phần).

---

## 13. Xây dựng API cập nhật sách theo ID (Chỉ có Admin mới được thực hiện)
- **Tệp tạo mới/thay đổi:**
  - `src/Actions/Books/UpdateBookAction.php` (Tạo mới)
  - `config/routes.php` (Thay đổi)
- **Nội dung:**
  - Lớp `UpdateBookAction` khai báo biến `$requiredPermission = 'book.update'` để bắt buộc quyền Admin truy cập thông qua AuthMiddleware.
  - Action kiểm tra sự tồn tại của cuốn sách bằng `BooksRepository::findById()`, ném ra `NotFoundException` (404) nếu không tìm thấy.
  - Hỗ trợ cập nhật từng phần: Chỉ xử lý các trường được truyền lên (`title`, `author`, `isbn`, `cover_image_url`).
  - Kiểm tra tính hợp lệ dữ liệu: Ràng buộc cực kỳ nghiêm ngặt, bất kỳ trường nào được truyền lên (`title`, `author`, `isbn`, `cover_image_url`) đều không được phép để rỗng hoặc chỉ có khoảng trắng (sau khi trim). Nếu rỗng, hệ thống sẽ ném lỗi `ValidationException` ngay lập tức.
  - Đồng thời kiểm tra mã `isbn` không được trùng lặp với cuốn sách khác trong cơ sở dữ liệu.
  - Tiến hành cập nhật thông qua `BooksRepository::update()` và trả về phản hồi JSON báo thành công.
  - Đăng ký endpoint `PUT /api/v1/books/{id}` bọc bởi `AuthMiddleware` trong file định tuyến `routes.php`.

---

## 14. Tổng hợp các API sách được phát triển hôm nay (26/06/2026)

Tất cả các API dưới đây đều được cấu hình phân quyền nghiêm ngặt và chỉ cho phép tài khoản có vai trò **Admin** gọi (qua `AuthMiddleware`).

### 1. Thêm sách mới (Create Book)
* **Phương thức:** `POST`
* **Endpoint:** `/api/v1/books`
* **Quyền (Permission):** `book.create`
* **Mô tả:** Tạo một cuốn sách mới trong hệ thống. Yêu cầu tất cả các trường dữ liệu truyền lên không được để trống.
* **Request Body mẫu:**
  ```json
  {
    "title": "Nghệ Thuật Đọc Chậm",
    "author": "Thomas Newkirk",
    "isbn": "978-0325048154",
    "cover_image_url": "https://example.com/cover.jpg"
  }
  ```

### 2. Lấy danh sách sách (List Books)
* **Phương thức:** `GET`
* **Endpoint:** `/api/v1/books`
* **Quyền (Permission):** `book.read`
* **Mô tả:** Lấy danh sách toàn bộ các cuốn sách đang lưu trữ trong cơ sở dữ liệu.
* **Request Body:** *Không có*

### 3. Lấy chi tiết sách (Get Book Detail)
* **Phương thức:** `GET`
* **Endpoint:** `/api/v1/books/{id}`
* **Quyền (Permission):** `book.read`
* **Mô tả:** Lấy thông tin chi tiết của một cuốn sách theo mã ID. Trả về `404 Not Found` nếu không tìm thấy sách.
* **Request Body:** *Không có*

### 4. Xóa sách (Delete Book)
* **Phương thức:** `DELETE`
* **Endpoint:** `/api/v1/books/{id}`
* **Quyền (Permission):** `book.delete`
* **Mô tả:** Xóa một cuốn sách khỏi hệ thống theo mã ID. Khi xóa sách, toàn bộ bài viết posts liên quan đến cuốn sách đó cũng sẽ bị xóa liên đới (Cascade Delete).
* **Request Body:** *Không có*

### 5. Cập nhật sách (Update Book)
* **Phương thức:** `PUT`
* **Endpoint:** `/api/v1/books/{id}`
* **Quyền (Permission):** `book.update`
* **Mô tả:** Cập nhật một phần hoặc toàn bộ thông tin sách theo mã ID. Bất kỳ trường nào truyền lên (`title`, `author`, `isbn`, `cover_image_url`) đều không được phép để rỗng (hoặc chỉ chứa khoảng trắng).
* **Request Body mẫu (Cập nhật một phần):**
  ```json
  {
    "title": "Tên sách mới",
    "author": "Tác giả mới"
  }
  ```




