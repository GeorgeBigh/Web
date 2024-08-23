@extends('layout.layout')

@section('content')
<div class="container">
    <h1>Assign Role to User</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{route('add_role.submit')}}">
        @csrf

        <div class="form-group">
            <label for="name">Role:</label>
            <input type="text " class="form-control" name="name">
            
        </div>

       
       
       
       
       
        <div class="form-group">
            {{-- <label for="guard">Select Role:</label> --}}
            <input type="text" class="form-control" name="guard_name" value="web" hidden readonly>
           
        </div>

        <button type="submit" class="btn btn-primary mt-2">Assign Role</button>
    </form>
</div>
@endsection
