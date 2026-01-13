@extends('layout')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow-sm mt-4">
            <div class="card-body p-4">

                <h2 class="text-center mb-3">Accept Invitation</h2>

                <p class="text-center text-muted mb-1">
                    You have been invited to join as 
                    <span class="fw-semibold">{{ $invitation->role }}</span>
                </p>

                @if($invitation->company)
                    <p class="text-center text-muted mb-4">
                        Company: <strong>{{ $invitation->company->name }}</strong>
                    </p>
                @endif

                <form method="POST" action="{{ route('invitations.process-accept', $invitation->token) }}">
                    @csrf

                   
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter your name"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                   
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input 
                            type="password" 
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Create a password"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                   
                    <div class="mb-4">
                        <label class="form-label">Confirm Password</label>
                        <input 
                            type="password" 
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Confirm password"
                            required
                        >
                    </div>

                   
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            Create Account
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
