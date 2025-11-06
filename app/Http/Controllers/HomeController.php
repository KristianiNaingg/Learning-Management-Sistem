<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\LomQuiz;

use App\Models\LomForum;
use App\Models\UserLearningStyleOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
{
    $user = Auth::user();

    // Ambil courses dengan topik terkait
    $courses = Course::with('topics')->where('visible', 1)->get();

    $quizCount = LomQuiz::count();

    // Update last login time
    $user->last_login_at = now()->setTimezone(config('app.timezone'));
    $user->save();

    $forums = LomForum::all();

    $lastLogin = $user->last_login_at
        ? $user->last_login_at->format('d M Y H:i')
        : 'Belum Pernah Login';

    // Periksa apakah user sudah menyelesaikan kuesioner ILS
    $hasCompletedILS = UserLearningStyleOption::where('user_id', $user->id)->exists();
    if (!$hasCompletedILS) {
        return redirect()->route('ils.ils_kuesioner_processing');
    }

    // Ambil data gaya belajar
    $learningStyles = UserLearningStyleOption::where('user_id', $user->id)->get();
    $studentStyles = [];
    foreach ($learningStyles as $style) {
        preg_match('/[A-Za-z]+$/', $style->final_score, $matches);
        $studentStyles[] = strtolower($matches[0] ?? '');
    }

    $dimensionLabels = [
        'ACT/REF' => ['Active', 'Reflective'],
        'SNS/INT' => ['Sensing', 'Intuitive'],
        'VIS/VRB' => ['Visual', 'Verbal'],
        'SEQ/GLO' => ['Sequential', 'Global'],
    ];

    $scores = [];
    foreach ($learningStyles as $style) {
        $dimension = $style->dimension;
        preg_match('/[A-Za-z]+$/', $style->final_score, $matches);
        $label = $matches[0] ?? $dimensionLabels[$dimension][0];
        $scores[$dimension] = [
            'label' => $label,
            'category' => $style->category,
        ];
    }

    $data = [
        'count_user' => DB::table('users')->count(),
        'menu'       => 'menu.v_menu_admin',
        'content'    => 'student.dashboard',
        'scores'     => $scores,
        'courses'    => $courses,
        'quizCount'  => $quizCount,
        'forums'     => $forums,
        'last_login' => $lastLogin,
        'studentStyles' => $studentStyles,
    ];

    return view('layouts.v_template', $data);
}

}
