<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

use App\Models\CourseUser;

class DashboardController extends Controller

   {


    /**

     * Display the admin dashboard with user and course statistics.
     *
     * @return \Illuminate\View\View
     */
    public function indexAdmin()
{
    $studentCount = User::whereHas('role', function($query) {
        $query->where('name_role', 'Student');
    })->where('status', 'active')->count();

    $instructorCount = User::whereHas('role', function($query) {
        $query->where('name_role', 'Teacher');
    })->count();

    $users = User::with('role')->get();
    $courseCount = Course::count();
    $topicCount = Topic::count();

    // 🔹 Tambahkan ini
    $roles = Role::all();

    $data = [
        'student_count' => $studentCount,
        'instructor_count' => $instructorCount,
        'course_count' => $courseCount,
        'topic_count' => $topicCount,

        'users' => $users,
        'roles' => $roles, // 🔹 kirim ke view
        'count_user' => DB::table('users')->count(),
        'menu' => 'menu.v_menu_admin',
        'content' => 'admin.dashboard'
    ];

    return view('layouts.v_template', $data);
}
    /**
     * Display the dosen (instructor) dashboard with user statistics.
     *
     * @return \Illuminate\View\View
     */

    public function indexDosen()

    {
        $data = [
            'count_user' => DB::table('users')->count(),
            'menu'      => 'menu.v_menu_admin',
            'content'   => 'dosen.dashboard'

        ];
        return view('layouts.v_template', $data);
    }

    public function indexMahasiswa()

    {
        $data = [
            'count_user' => DB::table('users')->count(),
            'menu'      => 'menu.v_menu_admin',
            'content'   => 'student.dashboard'

        ];
        return view('layouts.v_template', $data);
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
