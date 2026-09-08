@extends('layout.masterhome')
@section('content')
<main class="fb-explore container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="m-0">{{ $title }}</h2><a href="{{ url('/') }}" class="btn btn-light rounded-circle">×</a></div>
    @if($kind === 'friends')
        <div class="row g-3">@foreach($items as $item)<div class="col-6 col-md-4 col-lg-3"><div class="card h-100 shadow-sm border-0"><img src="{{ $item->photopro ? asset($item->photopro->path.$item->photopro->id.$item->photopro->type) : asset('img/Default_avatar_profile.jpg') }}" class="card-img-top explore-avatar" alt=""><div class="card-body"><strong>{{ $item->first_name }} {{ $item->last_name }}</strong><a href="{{ url('/profile/'.$item->id) }}" class="btn btn-primary w-100 mt-3">View profile</a></div></div></div>@endforeach</div>
    @elseif($items->count())
        <div class="row g-3">@foreach($items as $item)<div class="col-md-6"><div class="card shadow-sm border-0 p-3"><strong>{{ $item->post_text ?? ('Video #'.$item->id) }}</strong><small class="text-muted mt-2">{{ $item->created_at?->diffForHumans() }}</small></div></div>@endforeach</div>
    @else
        <div class="card border-0 shadow-sm text-center p-5"><div class="fs-1 mb-2">{{ $kind === 'saved' ? '🔖' : ($kind === 'watch' ? '🎬' : '🕒') }}</div><h4>{{ __('ui.no_items') }}</h4></div>
    @endif
    @if(method_exists($items, 'links'))<div class="mt-4">{{ $items->links() }}</div>@endif
</main>
@endsection
