<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') - Hải Sản Tươi Ngon</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Inter',system-ui,Arial,sans-serif; background:#f5f8fb; color:#2b2f38; }
        .box { text-align:center; padding:40px 24px; max-width:460px; }
        .code { font-size:72px; font-weight:800; color:#1577c4; line-height:1; }
        h1 { font-size:20px; margin:14px 0 8px; }
        p { color:#7b8794; margin:0 0 24px; line-height:1.6; }
        a { display:inline-block; background:#f26a1b; color:#fff; text-decoration:none; font-weight:700; padding:11px 22px; border-radius:6px; }
        a:hover { background:#e0560c; }
    </style>
</head>
<body>
<div class="box">
    <div class="code">@yield('code')</div>
    <h1>@yield('title')</h1>
    <p>@yield('message')</p>
    <a href="{{ url('/') }}">Về trang chủ</a>
</div>
</body>
</html>
