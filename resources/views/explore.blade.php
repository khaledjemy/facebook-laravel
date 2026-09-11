@extends('layout.masterhome')
@section('content')
<main class="fb-explore container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="m-0">{{ $title }}</h2><a href="{{ url('/') }}" class="btn btn-light rounded-circle">×</a></div>
    @if($kind === 'friends')
        <div class="row g-3">@foreach($items as $item)<div class="col-6 col-md-4 col-lg-3"><div class="card h-100 shadow-sm border-0"><img src="{{ $item->photopro ? asset($item->photopro->path.$item->photopro->id.$item->photopro->type) : asset('img/Default_avatar_profile.jpg') }}" class="card-img-top explore-avatar" alt=""><div class="card-body"><strong>{{ $item->first_name }} {{ $item->last_name }}</strong><a href="{{ url('/profile/'.$item->id) }}" class="btn btn-primary w-100 mt-3">View profile</a></div></div></div>@endforeach</div>
    @elseif($kind === 'watch')
        <section class="watch-live-head">
            <div><span class="watch-live-icon"><i class="fa fa-video"></i></span><span><h3>البث المباشر</h3><small>شاهد ما يحدث الآن أو ابدأ بثك الخاص</small></span></div>
            <a href="{{ route('live.create') }}" class="watch-go-live"><i class="fa fa-video me-2"></i>بدء بث مباشر</a>
        </section>
        @if($liveStreams->count())
            <div class="watch-live-grid mb-4">
                @foreach($liveStreams as $live)
                    @php($avatar = $live->user->photopro ? asset($live->user->photopro->path.$live->user->photopro->id.$live->user->photopro->type) : asset('img/Default_avatar_profile.jpg'))
                    <a href="{{ route('live.show', $live) }}" class="watch-live-card">
                        <div class="watch-live-preview"><img src="{{ $avatar }}" alt=""><span class="watch-on-air"><i class="fa fa-circle"></i> مباشر</span><span class="watch-viewers"><i class="fa fa-eye"></i> {{ $live->viewer_count }}</span><i class="fa fa-play watch-play"></i></div>
                        <div class="watch-live-meta"><img src="{{ $avatar }}" alt=""><span><b>{{ $live->title }}</b><small>{{ $live->user->first_name }} {{ $live->user->last_name }}</small></span></div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="watch-empty-live"><i class="fa fa-broadcast-tower"></i><span><b>لا يوجد بث مباشر الآن</b><small>يمكنك أن تكون أول من يبدأ بثًا</small></span></div>
        @endif
        <h3 class="mt-4 mb-3">أحدث الفيديوهات</h3>
        @if($items->count())
            <div class="watch-video-grid">@foreach($items as $item)<a href="{{ url('/video/'.$item->id) }}" class="watch-video-card"><div class="watch-video-thumb">@if($item->thumbnail_path)<img src="{{ asset($item->thumbnail_path) }}" alt="">@else<i class="fa fa-play-circle"></i>@endif<span><i class="fa fa-play"></i></span></div><div><b>{{ $item->title ?: ('فيديو #'.$item->id) }}</b><small>{{ optional($item->user)->first_name }} {{ optional($item->user)->last_name }} · {{ $item->created_at?->diffForHumans() }}</small></div></a>@endforeach</div>
        @else
            <div class="card border-0 shadow-sm text-center p-5"><div class="fs-1 mb-2">🎬</div><h4>{{ __('ui.no_items') }}</h4></div>
        @endif
    @elseif($items->count())
        <div class="row g-3">@foreach($items as $item)<div class="col-md-6"><div class="card shadow-sm border-0 p-3"><strong>{{ $item->post_text ?? ('Video #'.$item->id) }}</strong><small class="text-muted mt-2">{{ $item->created_at?->diffForHumans() }}</small></div></div>@endforeach</div>
    @else
        <div class="card border-0 shadow-sm text-center p-5"><div class="fs-1 mb-2">{{ $kind === 'saved' ? '🔖' : ($kind === 'watch' ? '🎬' : '🕒') }}</div><h4>{{ __('ui.no_items') }}</h4></div>
    @endif
    @if(method_exists($items, 'links'))<div class="mt-4">{{ $items->links() }}</div>@endif
</main>
@if($kind === 'watch')
<style>
.watch-live-head{display:flex;align-items:center;justify-content:space-between;gap:15px;background:#fff;border-radius:15px;padding:18px 22px;margin-bottom:20px;box-shadow:0 2px 10px #0000000d}.watch-live-head>div{display:flex;align-items:center;gap:12px}.watch-live-head h3{margin:0;font-weight:800}.watch-live-head small,.watch-live-meta small,.watch-video-card small{display:block;color:#65676b}.watch-live-icon{width:48px;height:48px;border-radius:50%;background:#fff0f2;color:#e41e3f;display:grid;place-items:center;font-size:20px}.watch-go-live{background:#e41e3f;color:#fff!important;padding:11px 18px;border-radius:9px;font-weight:800}.watch-live-grid,.watch-video-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}.watch-live-card,.watch-video-card{background:#fff;color:#1c1e21!important;border-radius:13px;overflow:hidden;box-shadow:0 2px 10px #00000010}.watch-live-preview{height:180px;background:linear-gradient(135deg,#242526,#111);position:relative;overflow:hidden;display:grid;place-items:center}.watch-live-preview>img{width:100%;height:100%;object-fit:cover;filter:blur(12px);opacity:.48;transform:scale(1.15)}.watch-on-air,.watch-viewers{position:absolute;top:12px;color:#fff;border-radius:5px;padding:5px 8px;font-size:12px;font-weight:800}.watch-on-air{right:12px;background:#e41e3f}.watch-on-air i{font-size:7px}.watch-viewers{left:12px;background:#0009}.watch-play{position:absolute;color:#fff;font-size:42px}.watch-live-meta{display:flex;gap:10px;padding:13px}.watch-live-meta>img{width:42px;height:42px;border-radius:50%;object-fit:cover}.watch-live-meta span{min-width:0}.watch-live-meta b{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.watch-empty-live{background:#fff;border:1px dashed #ccd0d5;border-radius:13px;padding:24px;display:flex;align-items:center;justify-content:center;gap:13px;color:#65676b}.watch-empty-live>i{font-size:31px;color:#e41e3f}.watch-empty-live span{display:flex;flex-direction:column}.watch-video-card{display:block}.watch-video-thumb{height:180px;background:#18191a;position:relative;display:grid;place-items:center;color:#777;font-size:44px}.watch-video-thumb>img{width:100%;height:100%;object-fit:cover}.watch-video-thumb>span{position:absolute;width:50px;height:50px;border-radius:50%;background:#0009;color:#fff;display:grid;place-items:center;font-size:18px}.watch-video-card>div:last-child{padding:13px}.watch-video-card b{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}@media(max-width:600px){.watch-live-head{align-items:flex-start;flex-direction:column}.watch-go-live{width:100%;text-align:center}}
</style>
@endif
@endsection
