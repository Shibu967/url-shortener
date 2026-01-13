@extends('layout')
@section('content')
<h2 class="mb-4 text-center">Send Invitation</h2>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('invitations.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="user@example.com"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select 
                            name="role" 
                            class="form-select @error('role') is-invalid @enderror"
                            required
                        >
                            <option value="">Select Role</option>
                            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Member" {{ old('role') == 'Member' ? 'selected' : '' }}>Member</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if(Auth::user()->isSuperAdmin())
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <select 
                                name="company_id" 
                                class="form-select @error('company_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option 
                                        value="{{ $company->id }}"
                                        {{ old('company_id') == $company->id ? 'selected' : '' }}
                                    >
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
