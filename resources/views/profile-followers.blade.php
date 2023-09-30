<x-profile :sharedData="$sharedData" docTitle="{{$sharedData['username']}}'s followers">
    @include('profile-followers-only')
</x-profile>
