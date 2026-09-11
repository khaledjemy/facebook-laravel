@extends('layout.masterhome')
@section('content')
<main class="live-create-page">
    <div class="live-setup-card">
        <a href="{{ url('/watch') }}" class="live-back" aria-label="رجوع"><i class="fa fa-arrow-right"></i></a>
        <div class="live-setup-icon"><i class="fa fa-video"></i></div>
        <h1>إنشاء بث مباشر</h1>
        <p>جهّز عنوان البث وخصوصيته، وبعدها سنفتح معاينة الكاميرا والميكروفون قبل أن تصبح مباشرًا.</p>
        <form method="POST" action="{{ route('live.store') }}">
            @csrf
            <label>عنوان البث</label>
            <input name="title" value="{{ old('title') }}" maxlength="160" required placeholder="عن ماذا ستتحدث؟">
            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
            <label>وصف مختصر <span>(اختياري)</span></label>
            <textarea name="description" maxlength="2000" rows="4" placeholder="أضف تفاصيل تساعد الناس على معرفة محتوى البث">{{ old('description') }}</textarea>
            <label>من يمكنه المشاهدة؟</label>
            <div class="live-audience-options">
                <label><input type="radio" name="visibility" value="public" checked><i class="fa fa-globe"></i><span><b>العامة</b><small>أي شخص يمكنه المشاهدة</small></span></label>
                <label><input type="radio" name="visibility" value="friends"><i class="fa fa-user-friends"></i><span><b>الأصدقاء</b><small>أصدقاؤك فقط</small></span></label>
                <label><input type="radio" name="visibility" value="only_me"><i class="fa fa-lock"></i><span><b>أنا فقط</b><small>اختبار خاص للبث</small></span></label>
            </div>
            <button type="submit" class="live-next"><i class="fa fa-video me-2"></i>فتح معاينة البث</button>
        </form>
    </div>
</main>
<style>
.live-create-page{min-height:calc(100vh - 60px);background:#f0f2f5;padding:42px 16px}.live-setup-card{position:relative;max-width:650px;margin:auto;background:#fff;border-radius:18px;padding:34px;box-shadow:0 4px 24px #00000016}.live-back{position:absolute;inset-inline-start:24px;top:24px;width:42px;height:42px;border-radius:50%;background:#eef0f3;color:#1c1e21;display:grid;place-items:center}.live-setup-icon{width:72px;height:72px;border-radius:50%;margin:0 auto 16px;background:#fff0f2;color:#f02849;font-size:30px;display:grid;place-items:center}.live-setup-card h1{text-align:center;font-weight:800}.live-setup-card>p{text-align:center;color:#65676b;margin-bottom:28px}.live-setup-card label:not(.live-audience-options label){display:block;font-weight:700;margin:16px 0 7px}.live-setup-card label span{font-weight:400;color:#8a8d91}.live-setup-card input[type=text],.live-setup-card input[name=title],.live-setup-card textarea{width:100%;border:1px solid #ccd0d5;border-radius:10px;padding:13px;outline:none}.live-setup-card input:focus,.live-setup-card textarea:focus{border-color:#1877f2;box-shadow:0 0 0 3px #1877f21b}.live-audience-options{display:grid;gap:10px}.live-audience-options label{display:flex;align-items:center;gap:13px;border:1px solid #d8dadf;border-radius:12px;padding:13px;cursor:pointer}.live-audience-options i{font-size:20px;color:#1877f2;width:24px}.live-audience-options span{display:flex;flex-direction:column}.live-audience-options small{color:#65676b}.live-next{width:100%;border:0;border-radius:10px;background:#e41e3f;color:#fff;font-size:17px;font-weight:800;padding:13px;margin-top:25px}
</style>
@endsection
