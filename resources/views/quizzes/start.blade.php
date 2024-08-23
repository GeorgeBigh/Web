@extends('layout.layout')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    /* Existing styles */

    #camera-container {
        pointer-events: none; /* Prevent interactions with the camera container */
    }

    /* Existing styles */

    /* Add the blur class */
    .blur-content {
        filter: blur(5px);
        pointer-events: none; /* Optional: Prevent interactions with the blurred content */
        transition: filter 0.3s ease;
    }

    /* Modal styles */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; /* Ensure modal is on top */
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgb(0,0,0); 
        background-color: rgba(0,0,0,0.4); 
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; 
        padding: 20px;
        border: 1px solid #888;
        width: 80%; 
        position: relative; /* To ensure the close button is positioned correctly */
        z-index: 1001; /* Ensure modal content is on top of the backdrop */
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

@section('content')
<div class="container mt-5 no-select blur-container" id="class">
    <!-- Existing content -->

    <form action="{{ route('quiz.submit', $quiz->id) }}" method="POST">
        @csrf
        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
        <input type="hidden" id="video-stream-input" name="video_stream_data">

        @foreach ($quiz->questions as $index => $question)
        <div id="question{{ $question->id }}" class="card quiz-card mb-4 question-container" style="display: {{ $loop->first ? 'block' : 'none' }};" oncut="return false" onpaste="return false">
            <div id="countdownTimer" class="float-right mr-3"></div>
            <h5 class="card-header text-center">{{ $quiz->title }}</h5>
            <div class="card-body">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="question-container">
                                @if ($quiz->live_stream == 1)
                                    <div id="camera-container" class="draggable">
                                        <video id="camera-stream" autoplay></video>
                                        <button id="toggle-camera" class="btn btn-secondary"></button>
                                    </div>
                                @endif
                                <h6 class="card-title text-wrap text-center" style="font-size: 18px;">
                                    Question: {{ $index + 1 }}
                                </h6>
                            </div>
                            <hr>
                            <div class="question">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-8 offset-md-2">
                                            <div class="text-center">
                                                <strong><h3><p>{{ $question->question_text }}</p></h3></strong>
                                            </div>
                                        </div>                                    
                                    </div>
                                </div>
                                
                                @foreach ($question->choices as $choice)
                                    @if (!empty(trim($choice->choice_text)))
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $choice->id }}" id="q{{ $question->id }}a{{ $choice->id }}">
                                            <label class="form-check-label" for="q{{ $question->id }}a{{ $choice->id }}">
                                                {{ $choice->choice_text }}
                                            </label>
                                        </div>
                                        <hr>
                                    @endif
                                @endforeach

                                @if ($question->choices->isEmpty() || $question->choices->every(function($choice) { return $choice->choice_text === null; }))
                                    <p>No valid choices available for this question.</p>
                                @endif
                                <div class="text-right">
                                    @if ($index > 0)
                                        <button type="button" class="btn btn-link prev-question" data-question-id="{{ $question->id }}">
                                            <i class="fas fa-arrow-left"></i> Previous Question
                                        </button>
                                    @endif
                                    @if ($loop->last)
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Submit
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-link next-question" data-question-id="{{ $question->id }}">
                                            <i class="fas fa-arrow-right"></i> Next Question
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </form>
</div>

<!-- Modal HTML -->
<div id="blur-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>You were inactive. Redirecting in <span id="modal-countdown">5</span> seconds.</p>
    </div>
</div>

<script>
    $(document).ready(function() {
        var videoElement = document.getElementById('camera-stream');
        var videoStreamInput = document.getElementById('video-stream-input');
        var blurClass = 'blur-content';
        var redirectTimer; // Timer variable to store the setTimeout reference
        var countdownTimer; // Timer variable to update countdown
        var modalElement = $('#blur-modal');
        var countdownElement = $('#modal-countdown');
        var countdownTime = 5; // Countdown time in seconds

        // Prevent right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Prevent copy, cut, and paste
        document.addEventListener('copy', function(e) {
            e.preventDefault();
        });

        document.addEventListener('cut', function(e) {
            e.preventDefault();
        });

        document.addEventListener('paste', function(e) {
            e.preventDefault();
        });

        // Capture the video stream data
        function captureVideoStream() {
            var canvas = document.createElement('canvas');
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            var context = canvas.getContext('2d');
            context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
            var dataUrl = canvas.toDataURL('image/webp'); // Use 'image/jpeg' or 'image/png' if needed
            videoStreamInput.value = dataUrl;
        }

        $('form').on('submit', function(e) {
            captureVideoStream();
        });

        function updateCountdown() {
            if (countdownTime > 0) {
                countdownTime--;
                countdownElement.text(countdownTime);
            } else {
                logRedirectAndRedirect();
            }
        }

        function startModalCountdown() {
            modalElement.show();
            countdownTime = 5; // Reset countdown time
            countdownElement.text(countdownTime);
            countdownTimer = setInterval(updateCountdown, 1000);
        }

        function stopModalCountdown() {
            clearInterval(countdownTimer);
            modalElement.hide();
        }

        function handleVisibilityChange() {
            if (document.hidden) {
                $('.blur-container').addClass(blurClass);
                startModalCountdown();
            } else {
                $('.blur-container').removeClass(blurClass);
                stopModalCountdown();
            }
        }

        function startRedirectTimer() {
            redirectTimer = setTimeout(function() {
                logRedirectAndRedirect();
            }, 5000); // Redirect after 5 seconds
        }

        function clearRedirectTimer() {
            if (redirectTimer) {
                clearTimeout(redirectTimer);
            }
        }

        function logRedirectAndRedirect() {
            // Create a hidden form for logging
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('quiz.logRedirect') }}";
            form.style.display = 'none';

            // Create and append CSRF token input
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Create and append quiz_id input
            var quizIdInput = document.createElement('input');
            quizIdInput.type = 'hidden';
            quizIdInput.name = 'quiz_id';
            quizIdInput.value = $('input[name="quiz_id"]').val();
            form.appendChild(quizIdInput);

            // Create and append user_id input
            var userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = '{{ Auth::id() }}';
            form.appendChild(userIdInput);

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();

            // Redirect after a short delay to ensure form submission
            setTimeout(function() {
                window.location.href = "{{ route('quizz') }}";
            }, 500); // Delay for 500ms to ensure form submission
        }

        document.addEventListener('visibilitychange', handleVisibilityChange);

        // Also handle window blur and focus
        window.addEventListener('blur', function() {
            $('.blur-container').addClass(blurClass);
            startModalCountdown();
        });

        window.addEventListener('focus', function() {
            $('.blur-container').removeClass(blurClass);
            stopModalCountdown();
        });

        // Detecting DevTools and handling visibility change
        (function() {
            var devtoolsOpen = false;
            var threshold = 160;

            function detectDevTools() {
                var widthThreshold = window.outerWidth - window.innerWidth > threshold;
                var heightThreshold = window.outerHeight - window.innerHeight > threshold;

                if (widthThreshold || heightThreshold) {
                    devtoolsOpen = true;
                    startModalCountdown();
                }

                if (window.console && window.console.log) {
                    var oldConsole = console.log;
                    console.log = function() {
                        devtoolsOpen = true;
                        startModalCountdown();
                        oldConsole.apply(console, arguments);
                    };
                }
            }

            setInterval(detectDevTools, 1000);

            document.addEventListener('visibilitychange', function() {
                if (document.hidden && devtoolsOpen) {
                    startModalCountdown();
                }
            });
        })();

        // Modal close button functionality
        $('.close').on('click', function() {
            modalElement.hide();
            stopModalCountdown();
            $('.blur-container').removeClass(blurClass);
            clearRedirectTimer();
        });

        // Handle Next and Previous buttons
        $(document).on('click', '.next-question', function() {
            var currentQuestionId = $(this).data('question-id');
            var currentQuestion = $('#question' + currentQuestionId);
            var nextQuestion = currentQuestion.next('.question-container');

            if (nextQuestion.length > 0) {
                currentQuestion.hide();
                nextQuestion.show();
                $('html, body').animate({ scrollTop: 0 }, 'fast'); // Scroll to top for better UX
            }
        });

        $(document).on('click', '.prev-question', function() {
            var currentQuestionId = $(this).data('question-id');
            var currentQuestion = $('#question' + currentQuestionId);
            var prevQuestion = currentQuestion.prev('.question-container');

            if (prevQuestion.length > 0) {
                currentQuestion.hide();
                prevQuestion.show();
                $('html, body').animate({ scrollTop: 0 }, 'fast'); // Scroll to top for better UX
            }
        });
    });
</script>

@endsection
