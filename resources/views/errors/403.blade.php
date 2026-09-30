<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>غير مسموح — {{ config('app.name') }}</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#f4f6fa;color:#172033;font-family:Tahoma,Arial,sans-serif}.card{width:min(100%,480px);padding:36px 28px;background:#fff;border:1px solid #e4e7ec;border-radius:18px;box-shadow:0 12px 36px #10182814;text-align:center}.icon{width:64px;height:64px;margin:0 auto 18px;display:grid;place-items:center;border-radius:50%;background:#fee4e2;color:#b42318;font-size:30px}.code{color:#0866ff;font-weight:700;letter-spacing:.08em}h1{margin:8px 0 12px;font-size:25px}p{margin:0;color:#667085;line-height:1.8}.actions{display:flex;justify-content:center;gap:10px;margin-top:24px;flex-wrap:wrap}a{padding:11px 18px;border-radius:9px;text-decoration:none;font-weight:700}.primary{background:#0866ff;color:#fff}.secondary{background:#f2f4f7;color:#344054}
    </style>
</head>
<body>
    <main class="card">
        <div class="icon" aria-hidden="true">🔒</div>
        <div class="code">403</div>
        <h1>هذه الصفحة للمدير فقط</h1>
        <p>حسابك لا يملك صلاحية دخول لوحة الإدارة. يمكنك العودة إلى الموقع أو تسجيل الدخول بحساب مخوّل.</p>
        <div class="actions"><a class="primary" href="{{ url('/') }}">العودة للموقع</a><a class="secondary" href="{{ route('login') }}">تسجيل الدخول</a></div>
    </main>
</body>
</html>
