<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Choice;
use App\Models\QuizResult;
use App\Models\Company;


use App\Models\QuizLogger;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        // Fetch all quizzes
        $quizzes = Quiz::all();
    
        // Fetch all questions and their choices
        $questions = Question::with('choices')->get();
        
        // Get the currently authenticated user
        $user = Auth::user();
    
        // Check if the user is an admin
        if ($user->hasRole('admin')) {
            // Admin can see all quizzes
            $quizzes = Quiz::all();
        } else {
            // Non-admin users see quizzes related to their company
            $quizzes = Quiz::whereHas('company.users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->get();
        }
    
        // Fetch quiz attempts for the current user
        $quizAttempts = QuizLogger::where('user_id', $user->id)
            ->pluck('quiz_attempts', 'quiz_id')
            ->toArray();

            
            
    
        // Return the view with the quizzes, questions, and quiz attempts
        return view('templateoftest.tables', [
            'questions' => $questions,
            'quizzes' => $quizzes,
            'quizAttempts' => $quizAttempts
        ]);
    }
    

    public function destroy($id)
{
    try {
        $quiz = Quiz::findOrFail($id);



        
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('lecturer')) {
            $quiz->delete();
            return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully');
        } else {
            return redirect()->route('quizzes.index')->with('error', 'Unauthorized to delete quiz');
        }
    } catch (\Exception $e) {
        return redirect()->route('quizzes.index')->with('error', 'Quiz not found');
    }
}

public function addquizz()
{
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        // If the user is an admin, show all companies
        $companies = Company::all();
    } else {
        // If the user is not an admin, show only companies the user is assigned to
        $companies = $user->companies; // Fetch only the companies assigned to the user
    }

    return view('templateoftest.create_quiz.add_quizz', compact('companies'));
}

    // use App\Models\Company; // Import the Company model at the top

public function create()
{
    $companies = Company::all(); // Fetch all companies from the database

    return view('quizzes', ['companies' => $companies]);
}


    /**
     * Store a new quiz with questions and choices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            // 'quiz_name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            // 'quizz_time' => 'nullable|numeric',
            // 'company_id' => 'nullable|integer',
            // 'assigned_user_id' => 'nullable|integer',
            // 'quiz_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // 'live_stream' => 'nullable|boolean'
        ]);
    
        $quiz = new Quiz();
        $quiz->title = $request->input('quiz_name');
        $quiz->description = $request->input('description');
        $quiz->quizz_time = $request->input('quizz_time');
        $quiz->company_id = $request->input('company_id');
        // $quiz->assigned_user_id = $request->input('assigned_user_id');
    
        if ($request->hasFile('quiz_image')) {
            $path = $request->file('quiz_image')->store('quiz_images', 'public');
            $quiz->image_path = $path;
        }
        
        // Handle the live_stream input
        $quiz->live_stream = $request->has('live_stream') ? 1 : 0;
    
        $quiz->save();
        
        if ($request->has('questions')) {
            foreach ($request->input('questions') as $questionData) {
                $question = new Question();
                $question->quiz_id = $quiz->id;
                $question->question_text = $questionData['question_text'];
                $question->point = $questionData['point'];
                $question->save();
                
                if (isset($questionData['choices']) && is_array($questionData['choices'])) {
                    foreach ($questionData['choices'] as $choiceData) {
                        $choice = new Choice();
                        $choice->question_id = $question->id;
                        $choice->choice_text = $choiceData['choice_text'] ?? '';
                        $choice->is_correct = isset($choiceData['is_correct']) ? $choiceData['is_correct'] : false;
                        $choice->point = isset($choiceData['point']) ? $choiceData['point'] : 0;
                        
                        $choice->save();
                    }
                }
            }
        }
    
        return redirect()->route('quizzes.index')->with('success', 'Quiz and questions created successfully.');
    }
    

    public function show($id)
    {
        $question = Question::with('choices')->find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        return response()->json($question);
    }

    public function startQuiz(Request $request, $quizId)
    {
        $user = Auth()->user();
        $quiz = Quiz::with('questions.choices')->findOrFail($quizId);

        $quizAttempts = QuizLogger::where('user_id', $user->id)
        ->where('quiz_id', $quizId)
        ->value('quiz_attempts') ?? 0;

    // Check if the user has exceeded the maximum number of attempts
    if ($quizAttempts > 2) {
        // Redirect back with an error message
       
        return redirect()->back()->with('error', 'You have exceeded the maximum number of attempts for this quiz.');
    }

        return view('quizzes.start', compact('quiz', 'quizAttempts'));
    }

    public function submit(Request $request)
    {
        // Validate the request data
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array', // Ensure answers is an array
        ]);
    
        // Fetch quiz ID and answers from request
        $quizId = $request->input('quiz_id');
        $answers = $request->input('answers');
        $point = $request->input('point');
        $videostream = $request->input('video_stream');
    
        // Initialize score
        $score = 0;
    
        // Iterate through each question's selected choices and calculate score
        foreach ($answers as $questionId => $selectedChoices) {
            // Fetch the question to get its point value
            $question = Question::find($questionId);
            if (!$question) continue;
    
            // Fetch all correct choices for the question
            $correctChoices = Choice::where('question_id', $questionId)->where('is_correct', true)->pluck('id')->toArray();
    
            // Check if the selected choices are the same as the correct choices
            $selectedCorrectChoices = array_intersect($correctChoices, $selectedChoices);
            $selectedIncorrectChoices = array_diff($selectedChoices, $correctChoices);
    
            // Add points only if all correct choices are selected and no incorrect choices are selected
            if (count($selectedCorrectChoices) === count($correctChoices) && count($selectedIncorrectChoices) === 0) {
                $score += $question->point ?? 0; // Ensure 'points' is defined on Question model
            }
        }
    
        // Save quiz result
        $quizResult = QuizResult::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quizId,
            'score' => $score,
            'point' => $point,
            'video_stream' => $videostream,
            // Add more fields as needed
        ]);
    
        // Redirect to the result page
        return redirect()->route('quiz.result', ['id' => $quizId, 'score' => $score]);
    }
    
    public function showResult($id, Request $request)
    {
        $quiz = Quiz::find($id);
        $score = $request->query('score');
        return view('quizzes.quizz_result.result', compact('quiz', 'score'));
    }
    
    

    

    public function hide($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->is_hidden = true;
        $quiz->save();
    
        return redirect()->back()->with('success', 'Quiz hidden successfully.');
    }
    
    public function unhide($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->is_hidden = false;
        $quiz->save();
    
        return redirect()->back()->with('success', 'Quiz unhidden successfully.');
    }
    
}
