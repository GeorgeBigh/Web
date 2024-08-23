<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizLogger;

class QuizLoggerController extends Controller
{



    public function index()
    {
        $user = Auth::user();
        
        // Check if the user has the admin role
        if ($user->hasRole('admin')) {
            // If the user is an admin, show all quiz loggers
            $quiz_loggers = QuizLogger::all();
        } else {
            // Get the IDs of the companies the user is assigned to
            $assignedCompanyIds = $user->companies->pluck('id')->toArray();
            
            // Get quiz loggers that belong to the companies the user is assigned to
            $quiz_loggers = QuizLogger::whereHas('quiz', function ($query) use ($assignedCompanyIds) {
                $query->whereIn('company_id', $assignedCompanyIds);
            })->get();
        }
        
        return view('Quiz_logger.Quiz_logger', compact('quiz_loggers'));


        
    }
    

    public function resetQuizAttempts($id)
    {
        // Update the specific record to set quiz_attempts to 0
        $quizLogger = QuizLogger::find($id);
        if ($quizLogger) {
            $quizLogger->update(['quiz_attempts' => 0]);
            return redirect()->back()->with('success', 'Quiz attempts have been reset to 0.');
        } else {
            return redirect()->back()->with('error', 'QuizLogger not found.');
        }
    }


    public function logRedirect(Request $request)
    {

        
        $userId = Auth::id();
        $quizId = $request->quiz_id;

        $logger = QuizLogger::firstOrNew([
            'user_id' => $userId,
            'quiz_id' => $quizId,
        ]);

        $logger->quiz_attempts = $logger->quiz_attempts ? $logger->quiz_attempts + 1 : 1;
        $logger->save();

        return redirect()->route('quizz');
    }
}
