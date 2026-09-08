@extends('layout.masterhome')

@section('content')
<div class="container py-4" style="max-width: 1100px; margin-top: 20px;">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="fa fa-user-friends text-primary me-2"></i> الأصدقاء
            </h3>
            <p class="text-muted small mb-0">إدارة طلبات الصداقة، اكتشاف أشخاص قد تعرفهم، واستعراض قائمة أصدقائك.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                {{ $friends->count() }} صديق
            </span>
            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="fa fa-arrow-right me-1"></i> العودة للرئيسية
            </a>
        </div>
    </div>

    <!-- Section 1: Incoming Friend Requests -->
    @if($incomingRequests->isNotEmpty())
        <div class="mb-5" id="sectionIncomingRequests">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa fa-user-plus text-primary me-2"></i> طلبات الصداقة المعلقة
                    <span class="badge bg-danger rounded-pill ms-2" id="reqBadgeCount">{{ $incomingRequests->count() }}</span>
                </h5>
            </div>

            <div class="row g-3">
                @foreach($incomingRequests as $req)
                    @php $sender = $req->user; @endphp
                    @if($sender)
                        <div class="col-6 col-md-4 col-lg-3 friend-card-req-{{ $sender->id }}">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                                <a href="{{ url('/profile/'.$sender->id) }}">
                                    <img src="{{ $sender->avatar_url }}" class="card-img-top" style="height: 190px; object-fit: cover;" alt="{{ $sender->first_name }}">
                                </a>
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <a href="{{ url('/profile/'.$sender->id) }}" class="text-dark fw-bold text-decoration-none text-truncate d-block mb-2">
                                        {{ $sender->first_name }} {{ $sender->last_name }}
                                    </a>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold btn-friend-action" data-user-id="{{ $sender->id }}" data-action="accept">
                                            تأكيد الطلب
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm rounded-pill text-muted fw-bold btn-friend-action" data-user-id="{{ $sender->id }}" data-action="cancel_r">
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Section 2: Friend Suggestions (People You May Know) -->
    @if($suggestions->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa fa-user-tag text-success me-2"></i> أشخاص قد تعرفهم
                </h5>
            </div>

            <div class="row g-3">
                @foreach($suggestions as $sug)
                    <div class="col-6 col-md-4 col-lg-3 friend-card-sug-{{ $sug->id }}">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <a href="{{ url('/profile/'.$sug->id) }}">
                                <img src="{{ $sug->avatar_url }}" class="card-img-top" style="height: 190px; object-fit: cover;" alt="{{ $sug->first_name }}">
                            </a>
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <a href="{{ url('/profile/'.$sug->id) }}" class="text-dark fw-bold text-decoration-none text-truncate d-block mb-2">
                                    {{ $sug->first_name }} {{ $sug->last_name }}
                                </a>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold btn-friend-action" data-user-id="{{ $sug->id }}" data-action="add">
                                    <i class="fa fa-user-plus me-1"></i> إضافة صديق
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Section 3: All Confirmed Friends -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa fa-users text-primary me-2"></i> كل الأصدقاء
                <span class="text-muted fs-6 fw-normal">({{ $friends->count() }})</span>
            </h5>
        </div>

        @if($friends->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 text-center p-5 bg-white">
                <div class="mb-3 text-muted">
                    <i class="fa fa-user-friends" style="font-size: 54px; opacity: 0.4;"></i>
                </div>
                <h6 class="fw-bold">ليس لديك أصدقاء بعد</h6>
                <p class="text-muted small mb-0">تصفح قائمة "أشخاص قد تعرفهم" أعلاه وأرسل طلبات صداقة لتوسيع شبكتك.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($friends as $fr)
                    <div class="col-6 col-md-4 col-lg-3 friend-card-fr-{{ $fr->id }}">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <a href="{{ url('/profile/'.$fr->id) }}">
                                <img src="{{ $fr->avatar_url }}" class="card-img-top" style="height: 190px; object-fit: cover;" alt="{{ $fr->first_name }}">
                            </a>
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <a href="{{ url('/profile/'.$fr->id) }}" class="text-dark fw-bold text-decoration-none text-truncate d-block mb-2">
                                    {{ $fr->first_name }} {{ $fr->last_name }}
                                </a>
                                <div class="d-grid gap-2">
                                    <a href="{{ url('/messanger/'.$fr->id) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
                                        <i class="fab fa-facebook-messenger me-1"></i> مراسلة
                                    </a>
                                    <button type="button" class="btn btn-light btn-sm rounded-pill text-danger fw-bold btn-friend-action" data-user-id="{{ $fr->id }}" data-action="remove" data-user-name="{{ $fr->first_name }}">
                                        إلغاء الصداقة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.btn-friend-action', function() {
        let $btn = $(this);
        let userId = $btn.data('user-id');
        let action = $btn.data('action');
        let userName = $btn.data('user-name') || 'هذا الصديق';

        if (action === 'remove') {
            if (!confirm('هل أنت متأكد من رغبتك في إلغاء صداقة ' + userName + '؟')) {
                return;
            }
        }

        $btn.prop('disabled', true);

        $.ajax({
            url: '/f_action',
            type: 'POST',
            data: {
                user: userId,
                type: action,
                _token: '{{ csrf_token() }}'
            },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                if (action === 'accept') {
                    $btn.closest('.card-body').html('<div class="text-success fw-bold text-center py-2"><i class="fa fa-check-circle me-1"></i> تم قبول الطلب</div>');
                } else if (action === 'cancel_r') {
                    $('.friend-card-req-' + userId).fadeOut(300, function() { $(this).remove(); });
                } else if (action === 'add') {
                    $btn.removeClass('btn-primary').addClass('btn-secondary').html('<i class="fa fa-check me-1"></i> تم إرسال الطلب');
                } else if (action === 'remove') {
                    $('.friend-card-fr-' + userId).fadeOut(300, function() { $(this).remove(); });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false);
                alert(xhr.responseJSON?.message || 'حدث خطأ أثناء تنفيذ الإجراء.');
            }
        });
    });
});
</script>
@endsection
