@php
    $reactionEmojis = [
        1 => '👍',
        2 => '❤️',
        3 => '🤗',
        4 => '😂',
        5 => '😮',
        6 => '😢',
        7 => '😡',
    ];
    $reactionCounts = collect($post['react'])
        ->countBy(fn ($reaction) => (int) ($reaction->type ?? 1))
        ->sortDesc();
@endphp

<div class="post-engagement-summary m-2" data-post-id="{{ $post->id }}">
    <div class="post-engagement-totals">
        @if($reactionCounts->sum() > 0)
            <a href="#" class="reaction-count text-dark text-decoration-none" data-post-id="{{ $post->id }}">
                {{ $reactionCounts->sum() }}
            </a>
        @endif

        @if(count($post->commentes) > 0)
            <span class="post-comment-count">
                {{ count($post->commentes) }} <span>{{ __('ui.comments') }}</span>
            </span>
        @endif
    </div>

    <button type="button"
            class="post-reaction-icons reaction-count{{ $reactionCounts->isEmpty() ? ' d-none' : '' }}"
            data-post-id="{{ $post->id }}"
            aria-label="{{ __('ui.reactions') }}">
        @foreach($reactionCounts->take(3) as $type => $count)
            <span class="post-reaction-icon post-reaction-type-{{ $type }}" title="{{ $count }}">
                {{ $reactionEmojis[$type] ?? '👍' }}
            </span>
        @endforeach
    </button>
</div>
