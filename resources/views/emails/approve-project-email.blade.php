<!DOCTYPE html>
<html>
<head>
    <title>Thông báo dự án</title>
</head>
<body>
<h4>Xin chào <b>{{ $data['user']['name'] }}</b>:</h4>

@if($data['status'] == \App\Enums\ProjectStatus::REJECT->value)
    <p>Rất tiếc, dự án của bạn đã bị từ chối phê duyệt.</p>
@else
    <p>Chúc mừng, dự án của bạn đã được phê duyệt!</p>
@endif
<i>Ghi chú : </i> <br>
<p> {{$data['notes']}} </p>
<p>Vui lòng đăng nhập hệ thống {{ env('APP_FE_URL') }} bằng thông tin sau để kiểm tra:</p>

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
        <td>{{ $data['user']['email'] }}</td>
        <td>{{ $data['user']['taxcode'] }}</td>
        <td>Mật khẩu được cấp của bạn</td>
    </tr>
    </tbody>
</table>
</body>
</html>
