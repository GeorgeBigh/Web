@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Add Company</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('company.store') }}">
        @csrf

        <div class="form-group">
            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" class="form-control" placeholder="Add company">
        </div>

        <button type="submit" class="btn btn-primary">Add Company</button>
    </form>
</div>
@endsection
