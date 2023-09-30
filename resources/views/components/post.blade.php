<a href="/post/{{ $post->id }}" class="list-group-item list-group-item-action">
    <img class="avatar-tiny" src="{{ $post->user->avatar }}" />
    <strong>{{ $post->title }}</strong> <small>
        @if (!isset($hideUsername))
            by {{ $post->user->username }}
        @endif
        on
        {{ $post->created_at->format('d M Y') }}
    </small>
</a>
