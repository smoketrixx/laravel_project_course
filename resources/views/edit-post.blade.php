<x-layout docTitle="Editing: {{ $post->title }}">
    <div class="container py-md-5 container--narrow">
        <p><strong><a href="/post/{{ $post->id }}">&laquo; Back</a></strong></p>
        <form action="/post/{{ $post->id }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="post-title" class="text-muted mb-1"><small>Title</small></label>
                <input value='{{ old('title', $post->title) }}' name="title" id="post-title"
                    class="form-control form-control-lg form-control-title" type="text" placeholder=""
                    autocomplete="off" />
                @error('title')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="post-body" class="text-muted mb-1"><small>Body Content</small></label>
                <textarea name="content" id="post-body" class="body-content tall-textarea form-control" type="text">{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Save changes</button>
        </form>
    </div>

</x-layout>
