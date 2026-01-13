@extends('layout')

@section('content')
<h2 class="mb-4">Short URLs</h2>

@if($shortUrls->count() > 0)
<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Short Code</th>
            <th>Original URL</th>
            <th>Created By</th>
            <th>Company</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shortUrls as $shortUrl)
        <tr>
            <td><a href="{{ route('short-urls.redirect', $shortUrl->short_code) }}" target="_blank">{{ url('/s/' . $shortUrl->short_code) }}</a></td>
            <td>{{ $shortUrl->original_url }}</td>
            <td>{{ $shortUrl->user->name ?? '-' }}</td>
            <td>{{ $shortUrl->company->name ?? '-' }}</td>
            <td>{{ $shortUrl->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No short URLs found.</p>
@endif
@endsection
