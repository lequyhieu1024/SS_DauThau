<!DOCTYPE html>
<html>

<head>
    <title>Tài khoản {{ $data['receiver'] }} đã được kích hoạt thành công</title>
</head>

<body>
<h4>Tài khoản {{ $data['receiver'] }} đã được kích hoạt thành công</h4>
<p>Xin chào , <b class="text-primary">{{ $data['name'] }}</b></p>
<p>Tài khoản của bạn đã được kích hoạt, vui lòng đăng nhập hệ thống {{ env('APP_FE_URL') }} bằng thông tin sau: </p>
<br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Email</th>
            <th>Mã số thuế</th>
            <th>Mật khẩu</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $data['email'] }}</td>
        <td>{{ $data['taxcode'] }}</td>
        <td>{{ $data['password'] }}</td>
    </tr>
    </tbody>

</table>
</body>

</html>
