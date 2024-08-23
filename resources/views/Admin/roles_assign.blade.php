@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Assign Role to User</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.assign-role') }}">
        @csrf

        <div class="form-group">
            <label for="user_id">Select User:</label>
            <select class="form-control" id="user_id" name="user_id">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="role">Select Role:</label>
            <select class="form-control" id="role" name="role">
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Assign Role</button>
    </form>
</div>
@endsection
