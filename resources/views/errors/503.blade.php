{{-- Laravel skips the 5xx fallback for 503, so this delegates explicitly. --}}
@include('errors.5xx')
