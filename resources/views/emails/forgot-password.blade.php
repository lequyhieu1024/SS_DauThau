<!DOCTYPE html>
<html>

<head>
    <title>Cập nhật mật khẩu mới tại trang web Septenary Solution JSC cho tài khoản {{ $data['name'] }}</title>
</head>

<body>
<h4>Cập nhật mật khẩu mới tại trang web Septenary Solution JSC cho tài khoản {{ $data['name'] }}</h4>
<p>Xin chào , <b class="text-primary">{{ $data['name'] }}</b></p>
<p>Bạn vừa yêu cầu thay đổi mật khẩu, nếu đó là bạn, hãy click <b><a target="_blank" href="https://staging.septenarysolution.site/auth/change-password?token={{ $data['token'] }}&email={{$data['email']}}">vào đây</a></b> để thay đổi mật khẩu</p>
<br>
<h5>Thông tin người dùng</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Email</th>
            <th>Mã số thuế</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $data['email'] }}</td>
        <td>{{ $data['taxcode'] }}</td>
    </tr>
    </tbody>

</table>
</body>

</html>
