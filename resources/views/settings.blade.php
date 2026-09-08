@extends('layout.masterhome')

@section('content')
<div class="container py-4" style="max-width: 1050px; margin-top: 20px;">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="fa fa-cog text-primary me-2"></i> الإعدادات والخصوصية
            </h3>
            <p class="text-muted small mb-0">إدارة معلومات حسابك، تفضيلات الأمان، خيارات الخصوصية، والحظر والمظهر.</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
            <i class="fa fa-arrow-right me-1"></i> العودة للرئيسية
        </a>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="fa fa-check-circle fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fa fa-exclamation-triangle me-1"></i> يرجى تصحيح الأخطاء التالية:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $activeTab = session('active_tab', request('tab', 'general'));
    @endphp

    <div class="row g-4">
        <!-- Settings Sidebar Navigation -->
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 80px; z-index: 10;">
                <div class="p-3 bg-light border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->avatar_url }}" class="rounded-circle border border-2 border-primary me-2" width="44" height="44" style="object-fit: cover;" alt="">
                        <div class="overflow-hidden">
                            <h6 class="mb-0 fw-bold text-truncate">{{ $user->first_name }} {{ $user->last_name }}</h6>
                            <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                        </div>
                    </div>
                </div>

                <div class="nav flex-column nav-pills p-2 fb-settings-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link text-start rounded-3 mb-1 {{ $activeTab === 'general' ? 'active' : '' }}" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab">
                        <i class="fa fa-id-card fa-fw me-2"></i> الحساب العام
                    </button>
                    <button class="nav-link text-start rounded-3 mb-1 {{ $activeTab === 'security' ? 'active' : '' }}" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab">
                        <i class="fa fa-shield-alt fa-fw me-2"></i> الأمان وتسجيل الدخول
                    </button>
                    <button class="nav-link text-start rounded-3 mb-1 {{ $activeTab === 'privacy' ? 'active' : '' }}" id="v-pills-privacy-tab" data-bs-toggle="pill" data-bs-target="#v-pills-privacy" type="button" role="tab">
                        <i class="fa fa-user-lock fa-fw me-2"></i> الخصوصية
                    </button>
                    <button class="nav-link text-start rounded-3 mb-1 {{ $activeTab === 'blocking' ? 'active' : '' }}" id="v-pills-blocking-tab" data-bs-toggle="pill" data-bs-target="#v-pills-blocking" type="button" role="tab">
                        <i class="fa fa-ban fa-fw me-2"></i> الحظر
                        @if($blockedUsers->count() > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $blockedUsers->count() }}</span>
                        @endif
                    </button>
                    <button class="nav-link text-start rounded-3 mb-1 {{ $activeTab === 'display' ? 'active' : '' }}" id="v-pills-display-tab" data-bs-toggle="pill" data-bs-target="#v-pills-display" type="button" role="tab">
                        <i class="fa fa-moon fa-fw me-2"></i> المظهر واللغة
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings Content Panes -->
        <div class="col-lg-9 col-md-8">
            <div class="tab-content" id="v-pills-tabContent">

                <!-- 1. General Account Tab -->
                <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="v-pills-general" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                            <i class="fa fa-id-card text-primary me-2"></i> إعدادات الحساب العامة
                        </h5>
                        <form method="POST" action="{{ route('settings.account') }}" id="formSettingsAccount">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">الاسم الأول</label>
                                    <input type="text" name="first_name" class="form-control rounded-3" value="{{ old('first_name', $user->first_name) }}" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">اسم العائلة</label>
                                    <input type="text" name="last_name" class="form-control rounded-3" value="{{ old('last_name', $user->last_name) }}" required maxlength="100">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required maxlength="150">
                                    <div class="form-text">يُستخدم هذا البريد لتسجيل الدخول وتلقي الإشعارات الهامة.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">النبذة التعريفية (Bio)</label>
                                    <textarea name="about" class="form-control rounded-3" rows="3" placeholder="اكتب نبذة قصيرة عنك تظهر في صفحتك الشخصية..." maxlength="255">{{ old('about', $user->about) }}</textarea>
                                    <div class="form-text">بحد أقصى 255 حرفاً.</div>
                                </div>
                                <div class="col-12 text-end pt-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                        <i class="fa fa-save me-1"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Account Metadata Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span><i class="fa fa-calendar-alt me-1"></i> تاريخ الانضمام: <strong>{{ $user->created_at ? $user->created_at->format('d M Y') : 'غير متوفر' }}</strong></span>
                            <span><i class="fa fa-fingerprint me-1"></i> معرّف الحساب (ID): <code>#{{ $user->id }}</code></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Security & Login Tab -->
                <div class="tab-pane fade {{ $activeTab === 'security' ? 'show active' : '' }}" id="v-pills-security" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                            <i class="fa fa-shield-alt text-primary me-2"></i> تسجيل الدخول والأمان
                        </h5>
                        <form method="POST" action="{{ route('settings.password') }}" id="formSettingsPassword">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">كلمة المرور الحالية</label>
                                <input type="password" name="current_password" class="form-control rounded-3" required autocomplete="current-password" placeholder="أدخل كلمة المرور الحالية">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">كلمة المرور الجديدة</label>
                                <input type="password" name="password" class="form-control rounded-3" required autocomplete="new-password" minlength="6" placeholder="على الأقل 6 أحرف">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">تأكيد كلمة المرور الجديدة</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-3" required autocomplete="new-password" minlength="6" placeholder="أعد إدخال كلمة المرور الجديدة">
                            </div>
                            <div class="text-end pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                    <i class="fa fa-key me-1"></i> تحديث كلمة المرور
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                        <h6 class="fw-bold mb-2"><i class="fa fa-lock text-success me-1"></i> نصائح الأمان</h6>
                        <ul class="text-muted small mb-0 ps-3">
                            <li>اختر كلمة مرور قوية تحتوي على مزيج من الحروف والأرقام والرموز.</li>
                            <li>لا تشارك كلمة المرور الخاصة بك مع أي شخص آخر.</li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Privacy Settings Tab -->
                <div class="tab-pane fade {{ $activeTab === 'privacy' ? 'show active' : '' }}" id="v-pills-privacy" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                            <i class="fa fa-user-lock text-primary me-2"></i> إعدادات الخصوصية والجمهور
                        </h5>
                        <form method="POST" action="{{ route('settings.privacy') }}" id="formSettingsPrivacy">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2">من يمكنه رؤية منشوراتك القادمة افتراضياً؟</label>
                                <p class="text-muted small mb-3">سيتم تطبيق هذا الخيار كجمهور افتراضي عند كتابة منشور جديد في الـ Feed.</p>

                                @php
                                    $defaultVis = old('default_post_visibility', $user->default_post_visibility ?? 'public');
                                @endphp

                                <div class="list-group rounded-3 shadow-sm border">
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 {{ $defaultVis === 'public' ? 'active-privacy-opt' : '' }}" style="cursor: pointer;">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="default_post_visibility" value="public" {{ $defaultVis === 'public' ? 'checked' : '' }}>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold"><i class="fa fa-globe-americas me-1 text-primary"></i> العامة (Public)</div>
                                            <small class="text-muted d-block">أي شخص داخل أو خارج فيسبوك يمكنه رؤية منشوراتك.</small>
                                        </div>
                                    </label>
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 {{ $defaultVis === 'friends' ? 'active-privacy-opt' : '' }}" style="cursor: pointer;">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="default_post_visibility" value="friends" {{ $defaultVis === 'friends' ? 'checked' : '' }}>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold"><i class="fa fa-user-friends me-1 text-success"></i> الأصدقاء فقط (Friends)</div>
                                            <small class="text-muted d-block">أصدقاؤك فقط على فيسبوك هم من يمكنهم رؤية منشوراتك.</small>
                                        </div>
                                    </label>
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 {{ $defaultVis === 'only_me' ? 'active-privacy-opt' : '' }}" style="cursor: pointer;">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="default_post_visibility" value="only_me" {{ $defaultVis === 'only_me' ? 'checked' : '' }}>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold"><i class="fa fa-lock me-1 text-secondary"></i> أنا فقط (Only Me)</div>
                                            <small class="text-muted d-block">أنت فقط من يرى منشوراتك بشكل سري وخاص تماماً.</small>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="text-end pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                    <i class="fa fa-save me-1"></i> حفظ خيارات الخصوصية
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 4. Blocking Management Tab -->
                <div class="tab-pane fade {{ $activeTab === 'blocking' ? 'show active' : '' }}" id="v-pills-blocking" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                            <i class="fa fa-ban text-danger me-2"></i> إدارة قائمة الحظر (Blocked Users)
                        </h5>
                        <p class="text-muted small mb-4">
                            بمجرد حظر شخص ما، لن يتمكن من رؤية منشوراتك، إرسال رسائل إليك في المحادثات، أو إضافتك كصديق.
                        </p>

                        <div id="blockedUsersListContainer">
                            @if($blockedUsers->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fa fa-user-shield text-secondary" style="font-size: 54px; opacity: 0.5;"></i>
                                    </div>
                                    <h6 class="fw-bold">لا يوجد أي مستخدمين محظورين</h6>
                                    <small>عندما تقوم بحظر أي مستخدم، سيظهر في هذه القائمة مع خيار إلغاء الحظر في أي وقت.</small>
                                </div>
                            @else
                                <div class="list-group rounded-3 shadow-sm border">
                                    @foreach($blockedUsers as $block)
                                        @php
                                            $bUser = $block->blockedUser;
                                        @endphp
                                        @if($bUser)
                                            <div class="list-group-item d-flex align-items-center justify-content-between p-3 blocked-row-{{ $bUser->id }}">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $bUser->avatar_url }}" class="rounded-circle border me-3" width="46" height="46" style="object-fit: cover;" alt="">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark">{{ $bUser->first_name }} {{ $bUser->last_name }}</h6>
                                                        <small class="text-muted">تم الحظر {{ $block->created_at ? $block->created_at->diffForHumans() : '' }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 btn-unblock-settings" data-user-id="{{ $bUser->id }}" data-user-name="{{ $bUser->first_name }} {{ $bUser->last_name }}">
                                                    <i class="fa fa-user-check me-1"></i> إلغاء الحظر
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 5. Display & Language Tab -->
                <div class="tab-pane fade {{ $activeTab === 'display' ? 'show active' : '' }}" id="v-pills-display" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">
                            <i class="fa fa-moon text-primary me-2"></i> المظهر وتفضيلات العرض
                        </h5>

                        <!-- Dark Mode Section -->
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light mb-4">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">
                                    <i class="fa fa-moon text-secondary me-2"></i> الوضع المظلم (Dark Mode)
                                </h6>
                                <p class="text-muted small mb-0">تقليل التوهج وإراحة العينين بألوان فيسبوك الليلية الداكنة.</p>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="settingsDarkModeSwitch" {{ $user->dark_mode ? 'checked' : '' }} style="cursor: pointer;">
                            </div>
                        </div>

                        <!-- Language Section -->
                        <h6 class="fw-bold mb-2 text-dark">
                            <i class="fa fa-globe text-secondary me-2"></i> لغة الواجهة
                        </h6>
                        <p class="text-muted small mb-3">اختر اللغة التي تفضل استخدامها في المنصة:</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('language', 'ar') }}" class="btn {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-4 fw-bold">
                                🇸🇦 العربية
                            </a>
                            <a href="{{ route('language', 'en') }}" class="btn {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-4 fw-bold">
                                🇺🇸 English
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Unblock button handler in settings page
    $(document).on('click', '.btn-unblock-settings', function() {
        let userId = $(this).data('user-id');
        let userName = $(this).data('user-name') || 'هذا المستخدم';

        if (!confirm('هل تريد بالتأكيد إلغاء حظر ' + userName + '؟ سيتمكن من رؤية منشوراتك العامة والتفاعل معك.')) {
            return;
        }

        let $btn = $(this);
        $btn.prop('disabled', true).text('جاري المعالجة...');

        $.ajax({
            url: '/users/' + userId + '/unblock',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                $('.blocked-row-' + userId).fadeOut(300, function() {
                    $(this).remove();
                    if ($('#blockedUsersListContainer .list-group-item').length === 0) {
                        $('#blockedUsersListContainer').html(`
                            <div class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fa fa-user-shield text-secondary" style="font-size: 54px; opacity: 0.5;"></i></div>
                                <h6 class="fw-bold">لا يوجد أي مستخدمين محظورين</h6>
                                <small>تم إلغاء حظر جميع المستخدمين بنجاح.</small>
                            </div>
                        `);
                    }
                });
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-user-check me-1"></i> إلغاء الحظر');
                alert(xhr.responseJSON?.message || 'حدث خطأ أثناء إلغاء الحظر.');
            }
        });
    });

    // Dark mode switch handler in settings page
    $('#settingsDarkModeSwitch').on('change', function() {
        let isDark = $(this).is(':checked');
        toggleDarkMode(isDark);

        // Sync with backend preferences
        $.ajax({
            url: '/settings/preferences',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                dark_mode: isDark ? 1 : 0
            },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
    });
});
</script>
@endsection
