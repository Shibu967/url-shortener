@extends('layout')

@section('content')

<h2 class="mb-4">Create Short URL</h2>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow-sm">
            <div class="card-body p-4">

                <form method="POST" action="{{ route('short-urls.store') }}">
                    @csrf

                   
                    <div class="mb-3">
                        <label class="form-label">Original URL</label>
                        <input 
                            type="url" 
                            name="original_url" 
                            value="{{ old('original_url') }}"
                            class="form-control @error('original_url') is-invalid @enderror"
                            placeholder="https://example.com"
                            required
                        >

                        @error('original_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Create Short URL
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
