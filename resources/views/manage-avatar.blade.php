<x-layout docTitle="Change your avatar">
    <div class="container py-md-5 container--narrow">
        <h1 class="mb-5 ">Upload Photo</h1>
        <form action="/manage-avatar" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="avatar" required>
                @error('avatar')
                    <p class=" text-danger">{{ $message }}"</p>
                @enderror
            </div>
            <button class="btn btn-primary">Upload</button>
        </form>
    </div>

</x-layout>
