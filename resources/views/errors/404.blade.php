{{-- Laravel skips the 4xx fallback for 404, so this delegates explicitly. --}}
@include('errors.4xx')
