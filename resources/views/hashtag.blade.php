@extends('layout.masterhome')
@section('content')
<main id="content" class="container py-3">
    <div class="card border-0 shadow-sm mb-3"><div class="card-body text-center py-4">
        <div class="hashtag-hero-icon">#</div>
        <h2 class="fw-bold mb-1">#{{ $hashtag->name }}</h2>
        <div class="text-muted">{{ $hashtag->posts_count }} منشور</div>
    </div></div>
    <div id="allpost">
        @forelse($posts as $post)
            @include('partials.hashtag_post', ['post' => $post])
        @empty
            <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">لا توجد منشورات متاحة بهذا الهاشتاج.</div></div>
        @endforelse
    </div>
    {{ $posts->links() }}
</main>
@endsection
