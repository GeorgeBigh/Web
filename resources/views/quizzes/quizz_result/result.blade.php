<!-- resources/views/quizzes/quiz_result/result.blade.php -->

@extends('layout.layout')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Quiz Results
        </div>
        <div class="card-body">
            {{-- <h5 class="card-title">Quiz: {{ $quiz }}</h5> --}}
            <h2><p class="card-text  text-center" > {{Auth::user()->name}} Score is: {{ $score }} point</p></h2>
            <a href="{{ route('quizzes.index') }}" class="btn btn-primary">Back to Quizzes</a>
        </div>
    </div>
</div>
@endsection
