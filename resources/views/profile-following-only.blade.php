<div class="list-group">
    @foreach ($following as $follow)
        <a href="/profile/{{ $follow->userBeingFollowing->username }}" class="list-group-item list-group-item-action">
            <img class="avatar-tiny" src="{{ $follow->userBeingFollowing->avatar }}" />
            {{ $follow->userBeingFollowing->username }}
        </a>
    @endforeach
</div>
