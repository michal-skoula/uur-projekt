{{-- Laravel skips the 5xx fallback for 500, so this delegates explicitly. --}}
@include('errors.5xx')
