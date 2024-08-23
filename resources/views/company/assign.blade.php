@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Assign Company to User</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('company.assign.store') }}">
        @csrf

        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select name="user_id" class="form-control">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="company_id">Select Company:</label>
            <select name="company_id" class="form-control">
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Assign Company</button>
    </form>
</div>
@endsection
