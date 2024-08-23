@extends('layout.layout')

@section('content')
    <!-- Invitation Form -->
    
    <div class="card mt-2">
        <h5 class="card-header">Invite User to Company</h5>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-warning">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('company.invite') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">User Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="company_id" class="form-label">Select Company</label>
                    <select class="form-select" id="company_id" name="company_id" required>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Send Invitation</button>
            </form>
        </div>
    </div>
    
    

    <!-- Hoverable Table rows -->
    <div class="card mt-2">
        <h5 class="card-header">Assigned Companies</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>User Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($companies as $company)
                        @foreach ($company->users as $user)
                            <tr>
                                <td>
                                    <i class="fab fa-bootstrap fa-lg text-primary me-3"></i>
                                    <strong>{{ $company->company_name }}</strong>
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('company.user.delete', ['company' => $company->id, 'user' => $user->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endsection
    