<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DefPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QuizLoggerController;
use App\Http\Controllers\RoleController;


use App\Http\Controllers\RatingController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('/');
})->name('main');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
Route::get('/quiz', [QuizController::class, 'index'])->name('quizz');

Route::get('/add_quiz', [QuizController::class, 'addquizz'])->middleware(\App\Http\Middleware\CheckUserRole::class)->name('add_quiz');
Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

Route::get('/quizzes', [QuizController::class, 'index']);
Route::get('/quizzes/add', [QuizController::class, 'addquizz']);
Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
Route::get('/quizzes/{id}', [QuizController::class, 'show']);
Route::post('quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
 Route::get('/quiz/{id}/result', [QuizController::class, 'showResult'])->name('quiz.result');
Route::get('/quiz/{quiz}/result', [QuizController::class, 'showResult'])->name('quiz.resultt');

Route::get('/add_role', [RoleController::class, 'index'])->name('add.role');
Route::resource('quizzes', QuizController::class);

Route::get('/quizzes/{quiz}/start', [QuizController::class, 'startQuiz'])->name('quiz.start');
// Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submitQuiz'])->name('quiz.submit');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
// routes/web.php

Route::get('/quiz/{quiz}/result', [QuizController::class, 'showResult'])->name('quiz.resulttt');





Route::post('/quizzes/{id}/hide', [QuizController::class, 'hide'])->name('quizzes.hide');
Route::post('/quizzes/{id}/unhide', [QuizController::class, 'unhide'])->name('quizzes.unhide');



Route::post('/reset-quiz-attempts/{id}', [QuizLoggerController::class, 'resetQuizAttempts'])->name('reset.quiz.attempts');

Route::get('/quiz_logger_info', [QuizLoggerController::class, 'index'])->name('quiz_logger.info');


Route::post('/rate', [RatingController::class, 'store'])->name('rate.store');


Route::post('/quiz/log-redirect', [QuizLoggerController::class, 'logRedirect'])->name('quiz.logRedirect');
Route::post('add_role/submit', [RoleController::class, 'store'])->name('add_role.submit');




Route::get('/admin/assign-role', [AdminController::class, 'showAssignRoleForm'])->name('admin.assign-role.formm');

Route::post('/admin/assign-role', [AdminController::class, 'assignRole'])->name('admin.assign-role');







Route::get('/company/add', [CompanyController::class, 'showAddCompanyForm'])->name('company.add');
Route::post('/company/add', [CompanyController::class, 'storeCompany'])->name('company.store');






Route::get('/company/assign', [CompanyController::class, 'showAssignCompanyForm'])->name('company.assign');
Route::post('/company/assign', [CompanyController::class, 'assignCompany'])->name('company.assign.store');
Route::get('/my-companies', [CompanyController::class, 'showUserCompanies'])->name('company.user');


Route::delete('/company/{company}/user/{user}', [CompanyController::class, 'deleteUserFromCompany'])->name('company.user.delete');






















Route::get('/company/invite/{companyId}', [CompanyController::class, 'showInviteUserForm'])->name('company.invite_form');
Route::post('/company/invite', [CompanyController::class, 'inviteUser'])->name('company.invite');
Route::get('/test-email', function () {
    $invitation = new \App\Models\Invitation([
        'email' => 'test@example.com',
        'company_id' => 1,
        'token' => \Str::random(32),
    ]);
    $password = 'testpassword'; // Simulated password
    Mail::to('test@example.com')->send(new InviteUserMail($invitation, $password));
    return 'Test email sent!';
});
Route::get('/accept-invitation/{token}', [CompanyController::class, 'acceptInvitation'])->name('company.accept_invitation');






Route::get('/accept-invitation/{token}', [CompanyController::class, 'acceptInvitation'])
    ->name('company.accept_invitation')
    ->middleware('signed');





});
Route::get('/', [DefPageController::class, 'index'])->name('main');
Route::get('/lecturer', [LecturerController::class, 'index'])->name('lecturer');

require __DIR__.'/auth.php';
