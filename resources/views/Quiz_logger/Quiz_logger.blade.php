@extends('layout.layout')
@section('content')






              <!-- Hoverable Table rows -->
              <div class="card mt-2">
                <h5 class="card-header">Quiz redirect logs</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Quiz</th>
                        {{-- <th>Client</th> --}}
                        <th>Users</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>Reset</th>
                      </tr>
                    </thead>
                    @foreach ($quiz_loggers as $item)
                    <tbody class="table-border-bottom-0">
                      <tr>



                      <tr>
                        
                        
                            
                        

                        <td>
                          <i class="fab fa-bootstrap fa-lg text-primary me-3"></i> <strong>{{$item->quiz->title}}</strong>
                        </td>
                        {{-- <td>Jerry Milton</td> --}}
                        <td>
                          <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                            <li
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              class="avatar avatar-xs pull-up"
                              title="{{$item->user->name}}"
                            >
                          
                         @if (  $item && $item->user->photo_of_user)
                              <img src="{{ asset('storage/' . $item->user->photo_of_user) }}" alt="Avatar" class="rounded-circle" />
                              @else
                              <img src="{{ asset('storage/profile-photos/user_default.webp') }}" alt="Default Avatar" class="w-px-40 h-auto rounded-circle" />
                          @endif
                           
                            </li>
                            
                           
                          </ul>
                        </td>
                        @if($item->quiz_attempts < 3)
                        <td><span class="badge bg-label-warning me-1">Restrict unlocked</span></td>
                        @else
                        <td><span class="badge bg-label-danger me-1">Restricted</span></td>
                        @endif
                        

                        <form action="{{ route('reset.quiz.attempts', $item->id ) }}" method="post">
                            @csrf
                            @if($item->quiz_attempts < 3)
                            <td><button type="submit" class="btn btn-primary" disabled>Reset Quiz Attempts</button></td>
                            @else
                            <td><button type="submit" class="btn btn-primary"  >Reset Quiz Attempts</button></td>
                            @endif
                        </form>
                    
                        <td>
                          <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);"
                                ><i class="bx bx-edit-alt me-1"></i> Edit</a
                              >
                              <a class="dropdown-item" href="javascript:void(0);"
                                ><i class="bx bx-trash me-1"></i> Delete</a
                              >
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                    @endforeach
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->


@endsection
