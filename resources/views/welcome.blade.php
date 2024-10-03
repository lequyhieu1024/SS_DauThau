<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bidding | Septenary Solution !</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Styles -->
</head>

<body class="antialiased">
<div class="">
    <div class="image-container">
        <img src="{{ asset('img/logo.jpg') }}" style="width: 100%; height:920px" alt="Septenary Solutions">
    </div>
</div>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('fd0c3289fdf6749d5b9c', {
        cluster: 'ap1',
    });

    var channel = pusher.subscribe('update-project-status');

    channel.bind('status.updated', function (data) {
        console.log('Received data:', data);
        alert(JSON.stringify(data));
    });

</script>
</body>
</html>

{{--useEffect(() => {--}}
{{--const echo = new Echo({--}}
{{--broadcaster: 'pusher',--}}
{{--key: 'your_app_key',--}}
{{--cluster: 'your_app_cluster',--}}
{{--forceTLS: true,--}}
{{--});--}}

{{--const staffId = /* ID của staff người dùng hiện tại */;--}}

{{--echo.private(`bidding-septenary.${staffId}`)--}}
{{--.listen('ProjectCreated', (e) => {--}}
{{--console.log('Received notification:', e.content); // Chỉ hiển thị content--}}
{{--setNotifications((prev) => [...prev, e.content]); // Lưu content vào state--}}
{{--});--}}

{{--return () => {--}}
{{--echo.disconnect();--}}
{{--};--}}
{{--}, []);--}}

