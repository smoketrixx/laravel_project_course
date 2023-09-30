<x-profile :sharedData="$sharedData" docTitle="{{ $sharedData['username'] }}'s following">
    @include('profile-following-only')
</x-profile>
