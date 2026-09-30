<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة الإدارة — {{ config('app.name') }}</title>
    <style>
        :root{--primary:#0866ff;--bg:#f4f6fa;--text:#172033;--muted:#667085;--border:#e4e7ec;--danger:#d92d20;--success:#067647}
        *{box-sizing:border-box;scroll-behavior:smooth}body{margin:0;background:var(--bg);color:var(--text);font-family:Tahoma,Arial,sans-serif}.shell{display:grid;grid-template-columns:250px minmax(0,1fr);min-height:100vh}.side{background:linear-gradient(180deg,#111827,#172338);color:#fff;padding:24px 16px;position:sticky;top:0;height:100vh;overflow:auto}.brand{font-size:20px;font-weight:700;margin:0 8px 26px}.side a{color:#d0d5dd;text-decoration:none;display:block;padding:11px 12px;border-radius:9px;margin:4px 0;transition:.16s}.side a:hover,.side a.active{background:#263b59;color:#fff}.side a.active{box-shadow:inset -3px 0 #60a5fa}.main{padding:30px clamp(16px,3vw,42px);min-width:0;max-width:1680px;width:100%;margin:auto}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding:18px 20px;background:#fff;border:1px solid var(--border);border-radius:14px}.top h1{margin:0 0 6px;font-size:25px}.top a{color:var(--primary);text-decoration:none}.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.stat,.panel{background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:0 3px 12px #1018280a}.stat{padding:19px;border-top:3px solid #dbeafe}.stat b{display:block;font-size:27px;margin-top:8px}.stat span{color:var(--muted);font-size:13px}.panel{margin-top:20px;padding:22px;scroll-margin-top:16px}.panel h2{margin:0 0 18px;font-size:20px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.field{margin-bottom:15px}.field label{display:block;margin-bottom:7px;font-size:14px;font-weight:700}.input{width:100%;padding:11px 12px;border:1px solid #d0d5dd;border-radius:9px;background:#fff;color:var(--text);font:inherit}.input:focus{outline:3px solid #dbeafe;border-color:var(--primary)}.check{display:flex;justify-content:space-between;align-items:center;padding:14px;border:1px solid var(--border);border-radius:9px;margin-bottom:12px;gap:14px}.check input[type=checkbox]{width:19px;height:19px;accent-color:var(--primary)}.btn{border:0;border-radius:8px;padding:10px 14px;cursor:pointer;font:inherit;font-size:13px;font-weight:700;transition:filter .15s,transform .15s}.btn:hover{filter:brightness(.96);transform:translateY(-1px)}.btn-primary{background:var(--primary);color:#fff}.btn-danger{background:#fee4e2;color:var(--danger)}.btn-muted{background:#eef2f6;color:#344054}table{width:100%;border-collapse:collapse}th,td{padding:12px 9px;text-align:right;border-bottom:1px solid var(--border);vertical-align:middle}th{font-size:12px;color:var(--muted);background:#f9fafb}td{font-size:14px}.badge{display:inline-block;border-radius:20px;padding:4px 9px;font-size:12px}.on{background:#dcfae6;color:var(--success)}.off{background:#fee4e2;color:var(--danger)}.inline{display:flex;gap:7px;align-items:center;flex-wrap:wrap}.flash{padding:13px 16px;border-radius:9px;margin-bottom:16px}.ok{background:#dcfae6;color:var(--success)}.err{background:#fee4e2;color:var(--danger)}.search{display:flex;gap:8px;margin-bottom:15px}.pagination{margin-top:15px}.pagination nav>div:first-child{display:none}.pagination nav>div:last-child{display:flex;justify-content:space-between;align-items:center}.pagination a,.pagination span{font-size:13px}.post-text{max-width:430px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.small{font-size:12px;color:var(--muted)}.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:10px}.table-wrap table{min-width:680px}.table-wrap tr:last-child td{border-bottom:0}.color-value{max-width:150px}
        @media(min-width:1400px){.stats{grid-template-columns:repeat(6,minmax(0,1fr))}}
        @media(max-width:1000px){.shell{grid-template-columns:1fr}.side{height:auto;position:sticky;z-index:5;top:0;padding:10px 12px;display:flex;gap:5px;align-items:center;overflow:auto;white-space:nowrap;scrollbar-width:thin;scrollbar-color:#475569 #111827}.side::-webkit-scrollbar{height:5px}.side::-webkit-scrollbar-track{background:#111827}.side::-webkit-scrollbar-thumb{background:#475569;border-radius:10px}.brand{margin:0 8px;flex:none}.side a{display:inline-block;flex:none}.panel{scroll-margin-top:92px}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.grid{grid-template-columns:1fr}.main{padding:16px}.table-wrap{overflow:auto}}
        @media(max-width:520px){.main{padding:10px}.top{padding:14px}.top h1{font-size:20px}.panel{padding:15px}.stats{gap:9px}.stat{padding:14px}.stat b{font-size:23px}.side{font-size:13px}.brand{font-size:16px}}
    </style>
</head>
<body>
<div class="shell">
    <nav class="side" aria-label="التنقل في لوحة الإدارة">
        <div class="brand">⚙ {{ config('app.name') }}</div>
        <a class="active" href="#overview">نظرة عامة</a><a href="#reports">البلاغات</a><a href="#settings">إعدادات الموقع</a><a href="#appearance">المظهر والهوية</a><a href="#users">المستخدمون</a><a href="#content">إدارة المحتوى</a><a href="#activity">سجل الإدارة</a><a href="{{ url('/') }}">عرض الموقع</a>
    </nav>
    <main class="main">
        <header class="top"><div><h1>لوحة الإدارة</h1><div class="small">مرحبًا، {{ auth()->user()->first_name }}</div></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-muted">{{ __('ui.sign_out') }}</button></form></header>
        @if(session('success'))<div class="flash ok" role="status" aria-live="polite">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="flash err" role="alert">{{ $errors->first() }}</div>@endif

        <section id="overview" class="stats">
            <div class="stat"><span>كل المستخدمين</span><b>{{ number_format($stats['users']) }}</b></div>
            <div class="stat"><span>الحسابات النشطة</span><b>{{ number_format($stats['active_users']) }}</b></div>
            <div class="stat"><span>مستخدمون جدد / 30 يوم</span><b>{{ number_format($stats['new_users']) }}</b></div>
            <div class="stat"><span>المنشورات</span><b>{{ number_format($stats['posts']) }}</b></div>
            <div class="stat"><span>التعليقات</span><b>{{ number_format($stats['comments']) }}</b></div>
            <div class="stat"><span>بلاغات تنتظر المراجعة</span><b>{{ number_format($stats['pending_reports']) }}</b></div>
        </section>

        <section id="reports" class="panel"><h2>البلاغات التي تنتظر المراجعة</h2><div class="table-wrap"><table><thead><tr><th>المُبلّغ</th><th>صاحب المنشور</th><th>السبب</th><th>التفاصيل</th><th>المحتوى</th><th>إجراء</th></tr></thead><tbody>
        @forelse($reports as $report)<tr><td>{{ $report->user?->first_name }} {{ $report->user?->last_name }}</td><td>{{ $report->post?->user?->first_name }} {{ $report->post?->user?->last_name }}</td><td>{{ $report->reason }}</td><td>{{ $report->details ?: '—' }}</td><td>@if($report->post)<a href="{{ url('/post/'.$report->post_id) }}" target="_blank">عرض المنشور</a>@else محذوف @endif</td><td><div class="inline">@if($report->post)<form method="POST" action="{{ route('admin.posts.destroy', $report->post) }}" onsubmit="return confirm('حذف المنشور المبلغ عنه؟')">@csrf @method('DELETE')<button class="btn btn-danger">حذف المنشور</button></form>@endif<form method="POST" action="{{ route('admin.reports.review', $report) }}">@csrf @method('PUT')<input type="hidden" name="status" value="reviewed"><button class="btn btn-primary">تمت المراجعة</button></form><form method="POST" action="{{ route('admin.reports.review', $report) }}">@csrf @method('PUT')<input type="hidden" name="status" value="dismissed"><button class="btn btn-muted">رفض البلاغ</button></form></div></td></tr>@empty<tr><td colspan="6">لا توجد بلاغات جديدة.</td></tr>@endforelse
        </tbody></table></div></section>

        <section id="settings" class="panel"><h2>إعدادات الموقع</h2>
            <form method="POST" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
                <div class="grid"><div>
                    <div class="field"><label>اسم الموقع</label><input class="input" name="site_name" value="{{ old('site_name', $siteSettings['site_name']) }}" required maxlength="80"></div>
                    <div class="field"><label>وصف الموقع</label><textarea class="input" name="site_description" rows="3" maxlength="300">{{ old('site_description', $siteSettings['site_description']) }}</textarea></div>
                    <div class="field"><label>بريد الدعم</label><input class="input" type="email" name="support_email" value="{{ old('support_email', $siteSettings['support_email']) }}"></div>
                </div><div>
                    <div class="grid"><div class="field"><label>الحد الأقصى للرفع (MB)</label><input class="input" type="number" min="1" max="500" name="max_upload_mb" value="{{ old('max_upload_mb', $siteSettings['max_upload_mb']) }}"></div><div class="field"><label>منشورات كل صفحة</label><select class="input" name="posts_per_page">@foreach([5,10,15,20,30,50] as $count)<option value="{{ $count }}" @selected($siteSettings['posts_per_page'] == $count)>{{ $count }}</option>@endforeach</select></div></div>
                    <label class="check"><span><b>السماح بالتسجيل</b><br><span class="small">تمكين إنشاء حسابات جديدة</span></span><span><input type="hidden" name="registration_enabled" value="0"><input type="checkbox" name="registration_enabled" value="1" @checked($siteSettings['registration_enabled'])></span></label>
                    <label class="check"><span><b>وضع الصيانة</b><br><span class="small">يعرض صفحة صيانة للجميع عدا المدير</span></span><span><input type="hidden" name="maintenance_mode" value="0"><input type="checkbox" name="maintenance_mode" value="1" @checked($siteSettings['maintenance_mode'])></span></label>
                </div></div><button class="btn btn-primary">حفظ الإعدادات</button>
            </form>
        </section>

        <section id="appearance" class="panel"><h2>المظهر والهوية</h2>
            <p class="small">اترك حقول الصور فارغة للاحتفاظ بالصور الحالية. الصيغ المدعومة: PNG وJPG وWEBP.</p>
            <form method="POST" action="{{ route('admin.appearance.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
                <div class="grid"><div>
                    <div class="field"><label>الشعار</label>@if(!empty($siteSettings['logo_path']))<img src="{{ asset('storage/'.$siteSettings['logo_path']) }}" alt="" style="display:block;max-width:180px;max-height:70px;margin-bottom:10px">@endif<input class="input" type="file" name="logo" accept=".png,.jpg,.jpeg,.webp"></div>
                    <div class="field"><label>أيقونة المتصفح</label>@if(!empty($siteSettings['favicon_path']))<img src="{{ asset('storage/'.$siteSettings['favicon_path']) }}" alt="" style="display:block;width:40px;height:40px;object-fit:contain;margin-bottom:10px">@endif<input class="input" type="file" name="favicon" accept=".png,.jpg,.jpeg,.webp,.ico"></div>
                    <div class="field"><label>خلفية صفحة تسجيل الدخول</label>@if(!empty($siteSettings['login_background_path']))<img src="{{ asset('storage/'.$siteSettings['login_background_path']) }}" alt="" style="display:block;width:100%;max-width:360px;height:110px;object-fit:cover;border-radius:8px;margin-bottom:10px">@endif<input class="input" type="file" name="login_background" accept=".png,.jpg,.jpeg,.webp"></div>
                </div><div>
                    <div class="field"><label>اللون الأساسي</label><div class="inline"><input type="color" name="primary_color" value="{{ $siteSettings['primary_color'] }}" aria-label="اختيار اللون الأساسي"><input class="input color-value" value="{{ $siteSettings['primary_color'] }}" readonly aria-label="قيمة اللون الأساسي"></div></div>
                    <div class="field"><label>اللون المساعد</label><div class="inline"><input type="color" name="accent_color" value="{{ $siteSettings['accent_color'] }}" aria-label="اختيار اللون المساعد"><input class="input color-value" value="{{ $siteSettings['accent_color'] }}" readonly aria-label="قيمة اللون المساعد"></div></div>
                    <div class="field"><label>لون خلفية الموقع</label><div class="inline"><input type="color" name="page_background" value="{{ $siteSettings['page_background'] }}" aria-label="اختيار لون خلفية الموقع"><input class="input color-value" value="{{ $siteSettings['page_background'] }}" readonly aria-label="قيمة لون الخلفية"></div></div>
                    <div class="field"><label>لون شريط التنقل</label><div class="inline"><input type="color" name="navbar_color" value="{{ $siteSettings['navbar_color'] }}" aria-label="اختيار لون شريط التنقل"><input class="input color-value" value="{{ $siteSettings['navbar_color'] }}" readonly aria-label="قيمة لون شريط التنقل"></div></div>
                    <div class="grid"><div class="field"><label>نوع الخط</label><select class="input" name="font_family">@foreach(['Arial','Tahoma','Cairo','Plus Jakarta Sans'] as $font)<option value="{{ $font }}" @selected($siteSettings['font_family'] === $font)>{{ $font }}</option>@endforeach</select></div><div class="field"><label>استدارة البطاقات</label><input class="input" type="number" name="card_radius" min="0" max="30" value="{{ $siteSettings['card_radius'] }}"></div></div>
                    <div class="field"><label>عنوان صفحة الدخول</label><input class="input" name="login_title" maxlength="160" value="{{ $siteSettings['login_title'] }}"></div>
                    <div class="field"><label>وصف صفحة الدخول</label><textarea class="input" name="login_subtitle" maxlength="300" rows="2">{{ $siteSettings['login_subtitle'] }}</textarea></div>
                    <div class="field"><label>نص التذييل</label><input class="input" name="footer_text" maxlength="120" value="{{ $siteSettings['footer_text'] }}"></div>
                    <button class="btn btn-primary">حفظ المظهر</button>
                </div></div>
            </form>
            <form method="POST" action="{{ route('admin.appearance.reset') }}" style="margin-top:14px" onsubmit="return confirm('استرجاع الألوان والصور الافتراضية؟')">@csrf @method('DELETE')<button class="btn btn-muted">استرجاع التصميم الافتراضي</button></form>
        </section>

        <section id="users" class="panel"><h2>إدارة المستخدمين</h2>
            <form class="search" method="GET" action="{{ route('admin.dashboard') }}"><input class="input" style="max-width:420px" name="q" value="{{ request('q') }}" placeholder="ابحث بالاسم أو البريد"><button class="btn btn-primary">بحث</button></form>
            <div class="table-wrap"><table><thead><tr><th>المستخدم</th><th>البريد</th><th>تاريخ التسجيل</th><th>الحالة</th><th>الإدارة</th><th>إجراء</th></tr></thead><tbody>
            @forelse($users as $user)<tr><td><b>{{ $user->first_name }} {{ $user->last_name }}</b><br><span class="small">#{{ $user->id }}</span></td><td>{{ $user->email }}</td><td>{{ optional($user->created_at)->format('Y-m-d') }}</td><td><span class="badge {{ $user->is_active ? 'on' : 'off' }}">{{ $user->is_active ? 'نشط' : 'موقوف' }}</span></td><td>{{ $user->is_admin ? 'مدير' : 'عضو' }}</td><td>@if($user->is(auth()->user()))<span class="small">حسابك — محمي</span>@else<form class="inline" method="POST" action="{{ route('admin.users.update', $user) }}" onsubmit="return confirm('{{ $user->is_active ? 'إيقاف' : 'تفعيل' }} حساب {{ addslashes($user->first_name.' '.$user->last_name) }}؟')">@csrf @method('PUT')<input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}"><input type="hidden" name="is_admin" value="{{ $user->is_admin ? 1 : 0 }}"><button class="btn {{ $user->is_active ? 'btn-danger' : 'btn-muted' }}">{{ $user->is_active ? 'إيقاف' : 'تفعيل' }}</button></form><form class="inline" style="margin-top:5px" method="POST" action="{{ route('admin.users.update', $user) }}" onsubmit="return confirm('{{ $user->is_admin ? 'إزالة صلاحية الإدارة من' : 'منح صلاحية الإدارة إلى' }} {{ addslashes($user->first_name.' '.$user->last_name) }}؟')">@csrf @method('PUT')<input type="hidden" name="is_active" value="{{ $user->is_active ? 1 : 0 }}"><input type="hidden" name="is_admin" value="{{ $user->is_admin ? 0 : 1 }}"><button class="btn btn-muted">{{ $user->is_admin ? 'إزالة الإدارة' : 'جعله مديرًا' }}</button></form>@endif</td></tr>@empty<tr><td colspan="6">لا توجد نتائج.</td></tr>@endforelse
            </tbody></table></div><div class="pagination">{{ $users->links() }}</div>
        </section>

        <section id="content" class="panel"><h2>أحدث المنشورات</h2><div class="table-wrap"><table><thead><tr><th>الناشر</th><th>المحتوى</th><th>الخصوصية</th><th>التاريخ</th><th>إجراء</th></tr></thead><tbody>
        @forelse($posts as $post)<tr><td>{{ optional($post->user)->first_name }} {{ optional($post->user)->last_name }}</td><td class="post-text">{{ $post->post_text ?: 'منشور وسائط' }}</td><td>{{ $post->visibility }}</td><td>{{ optional($post->created_at)->format('Y-m-d H:i') }}</td><td><form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('حذف هذا المنشور نهائيًا؟')">@csrf @method('DELETE')<button class="btn btn-danger">حذف</button></form></td></tr>@empty<tr><td colspan="5">لا توجد منشورات.</td></tr>@endforelse
        </tbody></table></div><div class="pagination">{{ $posts->links() }}</div></section>

        <section id="activity" class="panel"><h2>سجل نشاط الإدارة</h2><div class="table-wrap"><table><thead><tr><th>المدير</th><th>العملية</th><th>العنصر</th><th>عنوان IP</th><th>التاريخ</th></tr></thead><tbody>@forelse($activities as $activity)<tr><td>{{ $activity->admin?->first_name ?? 'مدير محذوف' }} {{ $activity->admin?->last_name }}</td><td>{{ ['settings.updated' => 'تحديث إعدادات الموقع', 'appearance.updated' => 'تحديث الهوية والمظهر', 'appearance.reset' => 'استرجاع المظهر الافتراضي', 'user.updated' => 'تغيير حالة أو صلاحية مستخدم', 'post.deleted' => 'حذف منشور', 'report.reviewed' => 'اعتماد مراجعة بلاغ', 'report.dismissed' => 'رفض بلاغ'][$activity->action] ?? $activity->action }}</td><td>{{ $activity->subject_type ? class_basename($activity->subject_type).' #'.$activity->subject_id : '—' }}</td><td>{{ $activity->ip_address }}</td><td>{{ $activity->created_at?->format('Y-m-d H:i') }}</td></tr>@empty<tr><td colspan="5">لا يوجد نشاط مسجل بعد.</td></tr>@endforelse</tbody></table></div></section>
    </main>
</div>
<script>
document.querySelectorAll('input[type="color"]').forEach(function (picker) {
    var value = picker.parentElement.querySelector('.color-value');
    if (value) picker.addEventListener('input', function () { value.value = picker.value.toUpperCase(); });
});
document.querySelectorAll('.side a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function () {
        document.querySelectorAll('.side a.active').forEach(function (item) { item.classList.remove('active'); });
        link.classList.add('active');
    });
});
</script>
</body></html>
