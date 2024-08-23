@extends('layout.layout')

@section('content')
<div class="container mt-5">
    

    @role('admin|lecturer')
    <a href="{{ route('add_quiz') }}" target="_blank">
        <button class="btn btn-success mb-3"><b>Create quiz</b></button>
    </a>
    @endrole

    @forelse ($quizzes as $quiz)
        <div class="card quiz-card mb-4" style="background-image: url('{{ asset('storage/' . $quiz->image_path) }}');">
            <div style="position: relative;">
                @role('admin|lecturer')
                <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST" style="position: absolute; top: 0; right: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Quiz</button>
                </form>

                <!-- Hide/Unhide Button -->
                @if($quiz->is_hidden)
                <form action="{{ route('quizzes.unhide', $quiz->id) }}" method="POST" >
                    @csrf
                    <button type="submit" class="btn btn-success">Unhide</button>
                </form>
                @else
                <form action="{{ route('quizzes.hide', $quiz->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">Hide</button>
                </form>
                @endif
                @endrole
            </div>
            @if($quiz->live_stream == 1)
            <div class="d-flex align-items-center">
                <small class="text-danger fs-6 me-1">
                    <strong title="If you see this, it means video is being recorded">Video is recording </strong>
                </small>
                <span class="recording-icon"></span>
            </div>
            @endif

            <style>
                .recording-icon {
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    background: red;
                    animation: pulse 1s infinite;
                }

                @keyframes pulse {
                    0% {
                        transform: scale(1);
                        opacity: 1;
                    }
                    50% {
                        transform: scale(1.2);
                        opacity: 0.7;
                    }
                    100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            </style>

            <h2 class="card-header text-center">
                <div class="d-flex justify-content-center align-items-center">
                    <u><strong title="{{ $quiz->title }}">{{ $quiz->title }}</strong></u>
                </div>
            </h2>
            @if(isset($quizAttempts[$quiz->id]) && $quizAttempts[$quiz->id] > 2)
                    <p class="d-flex justify-content-center align-items-center" style="background-color: red; color:white !important;">You are restricted from accessing this quiz. Please contact your lecturer.</p>
                    @endif
            <div class="card-body" id="quiz-body-{{ $quiz->id }}" style="{{ $quiz->is_hidden ? 'display: none;' : '' }}">
                <div class="d-flex justify-content-center" title="{{ $quiz->description }}">{{ $quiz->description }}</div>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="question-container">
                                <h6 class="card-title text-wrap text-center" style="font-size: 18px;"></h6>
                            </div>
                            <hr>
                            <div class="question">
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary start-quiz-btn"
                                        data-toggle="modal" data-target="#termsModal"
                                        data-quiz-id="{{ $quiz->id }}"
                                        @if(isset($quizAttempts[$quiz->id]) && $quizAttempts[$quiz->id] > 2) disabled @endif>
                                        Start Quiz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p>No quizzes available at the moment.</p>
    @endforelse
</div>

<!-- Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please read and agree to the terms and conditions before starting the quiz.</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="agreeCheckbox">
                    <label class="form-check-label" for="agreeCheckbox">
                        I agree to the terms and conditions
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a id="startQuizLink" href="#" class="btn btn-primary" role="button">Start Quiz</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function toggleVisibility(quizId) {
        var element = document.getElementById('quiz-body-' + quizId);
        var button = document.getElementById('toggle-btn-' + quizId);

        if (element.style.display === 'none') {
            element.style.display = 'block';
            button.textContent = 'Hide';
        } else {
            element.style.display = 'none';
            button.textContent = 'Unhide';
        }
    }

    $(document).ready(function () {
        // Disable Start Quiz link initially
        $('#startQuizLink').addClass('disabled');

        // Set href attribute of Start Quiz link dynamically when modal is opened
        $('#termsModal').on('shown.bs.modal', function () {
            var quizStartUrl = "{{ route('quiz.start', ':quizId') }}".replace(':quizId', $('.start-quiz-btn').data('quiz-id'));
            $('#startQuizLink').attr('href', quizStartUrl);
        });

        // Listen for checkbox change
        $('#agreeCheckbox').change(function () {
            if ($(this).is(':checked')) {
                $('#startQuizLink').removeClass('disabled');
            } else {
                $('#startQuizLink').addClass('disabled');
            }
        });

        // Handle Start Quiz link click
        $('#startQuizLink').click(function (e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault(); // Prevent link from being followed
                $('#termsModal').modal('show'); // Show modal if conditions are not met
            } else {
                // Proceed with quiz start logic
                window.location.href = $(this).attr('href'); // Redirect to quiz start URL
            }
        });

        // Update Start Quiz button href when clicked
        $('.start-quiz-btn').click(function () {
            var quizId = $(this).data('quiz-id');
            var quizStartUrl = "{{ route('quiz.start', ':quizId') }}".replace(':quizId', quizId);
            $('#startQuizLink').attr('href', quizStartUrl);
        });
    });
</script>
@endsection
