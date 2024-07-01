Các câu lệnh artisan cần dùng

// tạo controller
php artisan make::controller Api/TênController --resource  
// tạo migration
gặp trực tiếp nói hoặc có thể xem ytb hoặc chatGPT cách tạo 1 migration và generate bảng mới
//tạo model
php artisan make:model TênModel
trong Model phải tạo $fillable, use SoftDeletes(), nếu không cần cột created_at ,updated_at thì protected $timestamp = false
// tạo Resource
php artisan make:resource tên_collection
trong collection vừa tạo thì tạo hàm toArray rồi tạo key => value cần trả về
//tạo end point api trong file route/api.php. xem cách tạo end point của staff hoặc role

// ví dụ với role: {{base_url}} = http://127.0.0.1:8000/api/admin

index - Lấy danh sách các vai trò
URL: {{base_url}}/roles
Phương thức: GET

create - Hiển thị form để tạo mới một vai trò
URL: {{base_url}}/roles/create
Phương thức: GET

store - Lưu vai trò mới vào cơ sở dữ liệu
URL: {{base_url}}/roles
Phương thức: POST

show - Hiển thị thông tin chi tiết của một vai trò
URL: {{base_url}}/roles/{id}
Phương thức: GET

edit - Hiển thị form để chỉnh sửa một vai trò
URL: {{base_url}}/roles/{id}/edit
Phương thức: GET

update - Cập nhật thông tin của một vai trò
URL: {{base_url}}/roles/{id}
Phương thức: PUT hoặc PATCH

destroy - Xóa một vai trò
URL: {{base_url}}/roles/{id}
Phương thức: DELETE
