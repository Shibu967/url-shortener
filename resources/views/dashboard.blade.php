@extends('layout')

@section('content')
<h1>Dashboard</h1>

<p>Welcome, {{ $user->name }}!</p>
<p>Role: {{ $user->role }}</p>
@if($user->company)
<p>Company: {{ $user->company->name }}</p>
@endif

@if($user->isSuperAdmin())
    <p>You are a SuperAdmin. You can invite Admins to new companies and view all short URLs.</p>
@elseif($user->isAdmin())
    <p>You are an Admin. You can invite Admins and Members to your company and create short URLs.</p>
@elseif($user->isMember())
    <p>You are a Member. You can create short URLs.</p>
@endif
@endsection
