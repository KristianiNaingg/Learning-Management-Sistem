<?php

use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ILSKuesionerController;
use App\Http\Controllers\LomUserLogController;

use App\Http\Controllers\LearningContentController;
use App\Http\Controllers\Lom\AssignController;
use App\Http\Controllers\Lom\FileController;
use App\Http\Controllers\Lom\FolderController;
use App\Http\Controllers\Lom\ForumController;
use App\Http\Controllers\Lom\InfographicController;
use App\Http\Controllers\Lom\LabelController;
use App\Http\Controllers\Lom\LessonController;

use App\Http\Controllers\Lom\PageController;
use App\Http\Controllers\Lom\QuizController;
use App\Http\Controllers\Lom\QuizQuestionController;
use App\Http\Controllers\Lom\UrlController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\SubtopicController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Auth::routes();
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::get('/register/success', function () {
    if (!session()->has('just_registered')) {
        return redirect('/login');
    }
    session()->forget('just_registered');
    return view('auth.register-success');
})->name('register.success');

Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// General Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/upload', [ProfileController::class, 'upload'])->name('profile.upload');
    Route::delete('/profile/remove', [ProfileController::class, 'remove'])->name('profile.remove');
    Route::get('/pages/{id}', [PageController::class, 'show'])->name('pages.show');
    Route::get('/folders/{id}', [FolderController::class, 'show'])->name('folders.show');
    Route::get('/forums/{id}', [ForumController::class, 'show'])->name('forums.show');
});

// Admin Routes (role:1)
Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'indexAdmin'])->name('admin.dashboard');
    Route::get('/users/search', [UsersController::class, 'search'])->name('users.search');
    Route::resource('users', UsersController::class);
    Route::put('/users/approve/{id}', [UsersController::class, 'approveUser'])->name('users.approve');
    Route::get('users/{user}', [UsersController::class, 'show'])->name('users.show');


    Route::get('/users-logs', [LomUserLogController::class, 'index'])->name('admin.lom-logs.index');
    Route::get('/user-logs/{id}', [LomUserLogController::class, 'show'])->name('admin.lom-user-logs.show');   
    Route::post('/update-duration', [LomUserLogController::class, 'updateDuration'])->name('update.duration');


    Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::get('courses/management', [CourseController::class, 'management'])->name('courses.management');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('course.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');



});

// Admin and Dosen Routes (role:1,2)
Route::middleware(['auth', 'role:1,2'])->group(function () {
    Route::get('/courses/{id}/topics', [CourseController::class, 'show'])->name('courses.topics');
    Route::get('/participants/by-course/{courseId}', [ParticipantController::class, 'getParticipantsByCourse'])->name('participants.by-course');
    Route::post('/courses/{course}/participants', [ParticipantController::class, 'store'])->name('participants.store');
    Route::get('/courses/{courseId}/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/courses/{courseId}/participants/create', [ParticipantController::class, 'create'])->name('participants.create');
    Route::put('participants/{id}', [ParticipantController::class, 'update'])->name('participants.update');
    Route::delete('/participants/{id}', [ParticipantController::class, 'destroy'])->name('participants.destroy');

    Route::prefix('courses/{course}')->group(function () {
       // Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
        Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
        Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
        Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
        Route::patch('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
        Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');

        Route::prefix('topics/{topic_id}')->group(function () {
            Route::delete('/subtopics/{subtopic_id}', [TopicController::class, 'destroySubtopic'])->name('topics.subtopics.destroy');
            Route::delete('/references/{reference_id}', [TopicController::class, 'destroyReference'])->name('topics.references.destroy');
            Route::get('/reference/create', [ReferenceController::class, 'create'])->name('topics.reference.create');
            Route::post('/reference', [ReferenceController::class, 'store'])->name('topics.reference.store');
            Route::get('/reference/{id}/edit', [ReferenceController::class, 'edit'])->name('topics.reference.edit');
            Route::put('/reference/{id}', [ReferenceController::class, 'update'])->name('topics.reference.update');
            Route::get('/subtopic/create', [SubtopicController::class, 'create'])->name('topics.subtopic.create');
            Route::post('/subtopic', [SubtopicController::class, 'store'])->name('topics.subtopic.store');
            Route::get('/subtopic/{id}/edit', [SubtopicController::class, 'edit'])->name('topics.subtopic.edit');
            Route::put('/subtopics/{id}', [SubtopicController::class, 'update'])->name('topics.subtopic.update');
        });
    });
});

// Dosen Routes (role:2)
Route::middleware(['auth', 'role:2'])->group(function () {
    Route::get('/dashboarddosen', [DashboardController::class, 'indexDosen'])->name('dosen.dashboard');
    Route::get('/mycourses', [CourseController::class, 'indexDosen'])->name('dosen.courses.index');

    // LOM Assignment
    Route::get('/lom-assignment/create', [AssignController::class, 'create'])->name('assignments.create');
    Route::post('/lom-assignment/store', [AssignController::class, 'store'])->name('assignments.store');
    Route::get('/lom-assignment/assignment-submission/{assignment_id}', [AssignController::class, 'show'])->name('assignments.showDosen');
    Route::get('/lom-assignment/{assignment_id}/edit', [AssignController::class, 'edit'])->name('assignments.edit');
    Route::put('/lom-assignment/{assignment_id}', [AssignController::class, 'update'])->name('assignments.update');
    Route::delete('/lom-assignment/{assignment_id}', [AssignController::class, 'destroy'])->name('assignments.destroy');
    Route::get('/lom-assignment/{assignment_id}/duplicate', [AssignController::class, 'duplicate'])->name('assignments.duplicate');
    Route::post('/lom-assignment/{assignment_id}/duplicated', [AssignController::class, 'storeDuplicate'])->name('assignments.storeDuplicate');

    // LOM File
    Route::get('/lom-file/create', [FileController::class, 'create'])->name('files.create');
    Route::post('/lom-file/store', [FileController::class, 'store'])->name('files.store');
    Route::get('/lom-file/{file_id}/edit', [FileController::class, 'edit'])->name('files.edit');
    Route::put('/lom-file/{file_id}', [FileController::class, 'update'])->name('files.update');
    Route::delete('/lom-file/{file_id}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::get('/lom-file/{file_id}/duplicate', [FileController::class, 'duplicate'])->name('files.duplicate');
    Route::post('/lom-file/{file_id}/duplicated', [FileController::class, 'storeDuplicate'])->name('files.storeDuplicate');

    // LOM Folder (sisa kode dapat dilanjutkan dengan pola serupa

    // Lom Folder
    Route::get('/lom-folder/create', [FolderController::class, 'create'])->name('folders.create');
    Route::post('/lom-folder/store', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/lom-folder/{folder_id}/edit', [FolderController::class, 'edit'])->name('folders.edit');
    Route::put('/lom-folder/{folder_id}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/lom-folder/{folder_id}', [FolderController::class, 'destroy'])->name('folders.destroy');

    Route::get('lom-folder/{folder_id}/duplicate', [FolderController::class, 'duplicate'])->name('folders.duplicate');
    Route::post('/lom-folder/{folder_id}/duplicated', [FolderController::class, 'storeDuplicate'])->name('folders.storeDuplicate');
    //Route::get('/lom-folder/{folder}/filessave', [FileSaveController::class, 'show'])->name('filesave.show');

 // Lom Forum
    Route::get('/lom-forum/create', [ForumController::class, 'create'])->name('forums.create');
    Route::post('/lom-forum/store', [ForumController::class, 'store'])->name('forums.store');
    Route::get('/lom-forum/{forum_id}/edit', [ForumController::class, 'edit'])->name('forums.edit');
    Route::put('/lom-forum/{forum_id}', [ForumController::class, 'update'])->name('forums.update');
    Route::delete('/lom-forum/{forum_id}', [ForumController::class, 'destroy'])->name('forums.destroy');
    
    Route::get('/lom-forum/{forum_id}/duplicate', [ForumController::class, 'duplicate'])->name('forums.duplicate');
    Route::post('/lom-forum/{forum_id}/duplicated', [ForumController::class, 'storeDuplicate'])->name('forums.storeDuplicate');

 // Lom Infographic
    Route::get('/lom-infographic/create', [InfographicController::class, 'create'])->name('infographics.create');
    Route::post('/lom-infographic/store', [InfographicController::class, 'store'])->name('infographics.store');
    Route::get('/lom-infographic/{infographic_id}/edit', [InfographicController::class, 'edit'])->name('infographics.edit');
    Route::put('/lom-infographic/{infographic_id}', [InfographicController::class, 'update'])->name('infographics.update');
    Route::delete('/lom-infographic/{infographic_id}', [InfographicController::class, 'destroy'])->name('infographics.destroy');
    
    Route::get('/lom-infographic/{infographic_id}/duplicate', [InfographicController::class, 'duplicate'])->name('infographics.duplicate');
    Route::post('/lom-infographic/{infographic_id}/duplicated', [InfographicController::class, 'storeDuplicate'])->name('infographics.storeDuplicate');

 // Lom Label
    Route::get('/lom-label/create', [LabelController::class, 'create'])->name('labels.create');
    Route::post('/lom-label/store', [LabelController::class, 'store'])->name('labels.store');
    Route::get('/lom-label/{label_id}/edit', [LabelController::class, 'edit'])->name('labels.edit');
    Route::put('/lom-label/{label_id}', [LabelController::class, 'update'])->name('labels.update');
    Route::delete('/lom-label/{label_id}', [LabelController::class, 'destroy'])->name('labels.destroy');
   
    Route::get('/lom-label/{ilabel_id}/duplicate', [LabelController::class, 'duplicate'])->name('labels.duplicate');
    Route::post('/lom-label/{label_id}/duplicated', [LabelController::class, 'storeDuplicate'])->name('labels.storeDuplicate');

 // Lom Lesson
    Route::get('/lom-lesson/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lom-lesson/store', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lom-lesson/{lesson_id}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lom-lesson/{lesson_id}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lom-lesson/{lesson_id}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    
    Route::get('/lom-lesson/{lesson_id}/duplicate', [LessonController::class, 'duplicate'])->name('lessons.duplicate');
    Route::post('/lom-lesson/{lesson_id}/duplicated', [LessonController::class, 'storeDuplicate'])->name('lessons.storeDuplicate');

 // Lom Page
    Route::get('/lom-page/create', [PageController::class, 'create'])->name('pages.create');
    Route::post('/lom-page/store', [PageController::class, 'store'])->name('pages.store');
    Route::get('/lom-page/{page_id}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/lom-page/{page_id}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/lom-page/{page_id}', [PageController::class, 'destroy'])->name('pages.destroy');
    
    Route::get('/lom-page/{page_id}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::post('/lom-page/{page_id}/duplicated', [PageController::class, 'storeDuplicate'])->name('pages.storeDuplicate');

 // Lom URL
    Route::get('/lom-url/create', [UrlController::class, 'create'])->name('urls.create');
    Route::post('/lom-url/store', [UrlController::class, 'store'])->name('urls.store');
    Route::get('/lom-url/{url_id}/edit', [UrlController::class, 'edit'])->name('urls.edit');
    Route::put('/lom-url/{url_id}', [UrlController::class, 'update'])->name('urls.update');
    Route::delete('/lom-url/{url_id}', [UrlController::class, 'destroy'])->name('urls.destroy');
   
    Route::get('/lom-url/{url_id}/duplicate', [UrlController::class, 'duplicate'])->name('urls.duplicate');
    Route::post('/lom-url/{url_id}/duplicated', [UrlController::class, 'storeDuplicate'])->name('urls.storeDuplicate');

 // Lom Quiz
    Route::get('/lom-quiz/create', [QuizController::class, 'create'])->name('quizs.create');
    Route::post('/lom-quiz/store', [QuizController::class, 'store'])->name('quizs.store');
    Route::get('/lom-quiz/{iquiz_id}', [QuizController::class, 'show'])->name('quizs.show');

    Route::get('/lom-quiz/{quiz_id}/edit', [QuizController::class, 'edit'])->name('quizs.edit');
    Route::put('/lom-quiz/{quiz_id}', [QuizController::class, 'update'])->name('quizs.update');
    Route::delete('/lom-quiz/{quiz_id}', [QuizController::class, 'destroy'])->name('quizs.destroy');


    Route::get('/lom-quiz/{quiz_id}/question/create', [QuizQuestionController::class, 'create'])->name('questions.create');
    Route::post('/lom-quiz/{quiz_id}/question/store', [QuizQuestionController::class, 'store'])->name('questions.store');
    Route::get('/lom-quiz/{quiz_id}/question{question_id}/edit', [QuizQuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/lom-quiz/{quiz_id}/question/{question_id}', [QuizQuestionController::class, 'update'])->name('questions.update');
    Route::delete('/lom-quiz/{quiz_id}/question/{question_id}', [QuizQuestionController::class, 'destroy'])->name('questions.destroy');

 


});

// Siswa Routes (role:3)
Route::middleware(['auth', 'role:3'])->group(function () {
    Route::prefix('ils')->group(function () {
        Route::get('/kuesioner', [ILSKuesionerController::class, 'showAll'])->name('ils.kuesioner');
        Route::post('/submit-all', [ILSKuesionerController::class, 'storeAll'])->name('ils.submit_all');
        Route::get('/hasil', [ILSKuesionerController::class, 'showScore'])->name('ils.ils_score');
    });
});

// Authenticated Routes with ILS Check
Route::middleware(['auth', 'ensure.ils'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');
    Route::prefix('student')->group(function () {
        Route::get('/my-courses', [CourseController::class, 'indexStudent'])->name('student.course.index');
        Route::get('/course/{course_id}/topics', [TopicController::class, 'indexStudent'])->name('student.topic.index');
        Route::get('/course/{course_id}/topic', [TopicController::class, 'showStudent'])->name('student.topic.show');
    
   
    });

    Route::prefix('lom')->group(function () {
        Route::get('/quizsmahasiswa/{id}', [QuizController::class, 'showMahasiswa'])->name('quiz.showMahasiswa');
        Route::post('quiz/{id}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
        Route::get('quiz/{quizId}/result/{attemptId}', [QuizController::class, 'result'])->name('quiz.result');

    Route::post('/assignment/submit', [AssignController::class, 'submit'])->name('student.assignment.submit');
    Route::get('/assignment/{id}', [AssignController::class, 'showStudent'])->name('student.assignment.show');
});

    // Route::get('/page/{page_id}', [PageController::class, 'show'])->name('student.page.show');

   
    // });

    Route::prefix('lom')->middleware(['auth', 'track.lom'])->group(function () {

    // LOM Page
    Route::get('/page/{page_id}', [PageController::class, 'show'])
        ->name('student.page.show');

    
    
});




    //Lom For Student
 
    
   

});