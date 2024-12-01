
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu hỗ trợ</title>
    <style>
        /* Định dạng chung */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9; /* Màu nền sáng hơn */
            color: #555; /* Màu chữ tối hơn một chút */
            line-height: 1.6;
        }

        /* Container chính */
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff; /* Màu nền trắng sáng */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
        }

        /* Tiêu đề */
        h1 {
            color: #2980b9; /* Màu xanh sáng cho tiêu đề */
            text-align: center;
            font-size: 26px; /* Kích thước chữ lớn hơn một chút */
            margin-bottom: 20px;
        }

        /* Định dạng các dòng thông tin */
        p {
            font-size: 16px;
            margin: 10px 0;
        }

        p strong {
            color: #2c3e50; /* Màu xám đậm cho các nhãn */
        }

        /* Định dạng liên kết */
        a {
            color: #3498db; /* Màu xanh sáng cho liên kết */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline; /* Thêm gạch dưới khi hover */
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Yêu cầu hỗ trợ mới</h1>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Số điện thoại:</strong> {{ $data['phone'] ?? 'Không cung cấp' }}</p>
        <p><strong>Tiêu đề:</strong> {{ $data['title'] }}</p>
        <p><strong>Nội dung:</strong> {{ $data['content'] ?? 'Không có nội dung' }}</p>
        <div class="attachment">
            <p><strong>Tài liệu đính kèm:</strong> 
                @if (!empty($data['document']))
                <a href="{{ asset($data['document']) }}">Tải về</a>
                    <!-- <a href="{{ Storage::url($data['document']) }}">Tải về</a> -->
                @else
                    Không có
                @endif
            </p>
        </div>
    </div>
</body>
</html>

