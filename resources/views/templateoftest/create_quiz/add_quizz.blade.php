@extends('layout.layout')

@section('quiz')
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"> Add Quiz /</span> Quiz</h4>

    <style>
      .quiz-card {
        max-height: 80vh; /* Initial maximum height */
        overflow-y: auto;
        margin-bottom: 20px; /* Optional margin for spacing */
        background-size: cover;
        background-position: center;
      }

      @media (max-width: 768px) {
        .quiz-card {
          max-height: 60vh; /* Adjusted maximum height for smaller screens */
        }
      }

      @media (max-width: 576px) {
        .quiz-card {
          max-height: 50vh; /* Further adjusted maximum height for mobile screens */
        }
      }
    </style>

    <div id="quiz-form-container">
      <div class="card quiz-card" style="background-image: url('{{ asset('path_to_your_image.jpg') }}');">
        <h5 class="card-header text-center">Quiz Add</h5>
        <div class="card-body">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-md-8">
                <form id="quiz-form" action="{{ route('quizzes.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group mb-3">
                    <label for="quiz_name" class="form-label">Quiz Name</label>
                    <input type="text" class="form-control" name="quiz_name" id="quiz_name" placeholder="Enter quiz name" required>
                    <textarea name="description" class="form-control mt-2" id="description" cols="5" rows="5" placeholder="Description"></textarea>
                  </div>
                  <div class="form-group mb-3">
                    <label for="quiz_image" class="form-label">Upload Quiz Background</label>
                    <input type="file" class="form-control" name="quiz_image" id="quiz_image" accept="image/*">
                  </div>
                  <div class="form-group mb-3">
                    <label for="quizz_time" class="form-label">Quiz Time</label>
                    <input type="number" name="quizz_time" id="quizz_time" class="form-control">
                  </div>
                  <div class="form-group mb-3">
                    <label for="company_id" class="form-label">Select Company</label>
                    <select name="company_id" id="company_id" class="form-control">
                      @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="live_stream" id="live_stream" value="1">
                      <label class="form-check-label" for="live_stream">
                        Enable Live Stream
                      </label>
                    </div>
                  </div>
                  <div id="questions-container">
                    <!-- Questions will be added here dynamically -->
                  </div>
                  <div class="text-right">
                    <button type="button" class="btn btn-secondary" id="add-question-btn">Add Another Question</button>
                    <button type="submit" class="btn btn-primary">Submit Quiz</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Question Template -->
<template id="question-template">
  <div class="question-container">
    <h6 class="card-title text-wrap text-center" style="font-size: 18px;">
      Question <span class="question-number">__NUMBER__</span>
    </h6>
    <textarea name="questions[__INDEX__][question_text]" cols="5" rows="5" class="form-control" placeholder="Question?"></textarea>
    <div class="form-group mb-3">
      <label for="question_point__INDEX__" class="form-label">Question Point</label>
      <input type="number" class="form-control" name="questions[__INDEX__][point]" value="0" placeholder="Point">
    </div>
    <hr>
    @for ($i = 0; $i < 4; $i++)
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="questions[__INDEX__][choices][{{ $i }}][is_correct]" value="1" id="q__INDEX__a{{ $i + 1 }}">
      <label class="form-check-label" for="q__INDEX__a{{ $i + 1 }}">
        <input type="text" class="form-control" name="questions[__INDEX__][choices][{{ $i }}][choice_text]" placeholder="Answer">
      </label>
    </div>
    <hr>
    @endfor
  </div>
</template>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    let questionIndex = 0;

    // Function to add a new question
    function addQuestion() {
      let template = $('#question-template').html();
      template = template.replace(/__INDEX__/g, questionIndex);
      template = template.replace(/__NUMBER__/g, questionIndex + 1); // Replace placeholder with question number
      $('#questions-container').append(template);
      questionIndex++;
    }

    // Add the first question on page load
    addQuestion();

    // Handle click event to add new question
    $('#add-question-btn').click(function() {
      addQuestion();
    });
  });
</script>

@endsection
