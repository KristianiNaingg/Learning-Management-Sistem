<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Topic;
use App\Models\CourseUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $count_user = DB::table('users')->count();
        $users = User::all();

        $data = [
            'count_user' => $count_user,
            'users' => $users,
            'courses' => Course::all(),
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.course.index'
        ];

        return view('layouts.v_template', $data);
    }

   /**
     * Menampilkan daftar kursus di mana dosen yang login (user_id) terdaftar sebagai pengajar
     * dengan participant_role = 'Teacher' dan id_role = 2.
     */
  public function indexDosen()
{
    // Check if user is authenticated and a lecturer
    if (!Auth::check() || Auth::user()->id_role != 2) {
        return redirect()->route('home')->with('error', 'Access for lecturers only.');
    }

    $currentUserId = Auth::id();

    // Fetch courses where the user is a Teacher
    $courses = Course::whereHas('users', function ($query) use ($currentUserId) {
        $query->where('course_users.user_id', $currentUserId)
              ->where('course_users.participant_role', 'Teacher');
    })
    ->with(['category']) // Load category relationship untuk filter
    ->withCount(['users as student_count' => function ($query) {
        $query->where('course_users.participant_role', 'Student');
    }])
    ->get();

    // Log untuk debugging
    Log::info('Dosen Courses Data', [
        'user_id' => $currentUserId,
        'courses_count' => $courses->count(),
        'course_names' => $courses->pluck('full_name')
    ]);

    $data = [
        'count_user' => User::count(),
        'count_student' => CourseUser::where('participant_role', 'Student')
            ->whereIn('course_id', $courses->pluck('id'))
            ->count(),
        'courses_count' => $courses->count(),
        'users' => User::all(),
        'courses' => $courses, // Pastikan menggunakan 'courses' bukan 'lecturerCourses'
        'menu' => 'menu.v_menu_admin',
        'content' => 'dosen.course.index'
    ];

    return view('layouts.v_template', $data);
}

    public function indexStudent()
{
    $user = auth()->user();

    $allCourses = Course::with('users')->get();

    $joinedCourses = $allCourses->filter(function ($course) use ($user) {
        return $course->users->contains($user->id);
    });

    $notJoinedCourses = $allCourses->filter(function ($course) use ($user) {
        return !$course->users->contains($user->id);
    });

    $data = [
        'count_user' => User::count(),
        'users' => User::all(),
        'joinedCourses' => $joinedCourses,
        'notJoinedCourses' => $notJoinedCourses,
        'menu' => 'menu.v_menu_admin',
        'content' => 'student.courses'
    ];

    return view('layouts.v_template', $data);
}


   
       /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'count_user' => DB::table('users')->count(),
            'courses' => Course::all(),
           
             'menu' => 'menu.v_menu_admin',
            'content' => 'admin.course.create'
        ];
        return view('layouts.v_template', $data);
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Log::info('Request data received', $request->all());

        $request->validate([
            'full_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'summary' => 'required|string',
            'cpmk' => 'nullable|string',
            'semester' => 'required|in:1,2,3',
            'visible' => 'required|boolean',
            'course_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'required|string',
            'start_day' => 'required|integer|between:1,31',
            'start_month' => 'required|string',
            'start_year' => 'required|integer|between:2023,2030',
            'start_time' => 'required',
            'enable_end_date' => 'nullable',
            'end_day' => 'required_if:enable_end_date,1|integer|between:1,31',
            'end_month' => 'required_if:enable_end_date,1|string',
            'end_year' => 'required_if:enable_end_date,1|integer|between:2023,2030',
            'end_time' => 'required_if:enable_end_date,1',
            'participants' => 'nullable|string',
        ]);

        // Combine start date and time
        $startDate = \Carbon\Carbon::createFromFormat(
            'Y F d H:i',
            "{$request->start_year} {$request->start_month} {$request->start_day} {$request->start_time}"
        );

        // Combine end date and time if enabled
        $endDate = null;
        if ($request->has('enable_end_date') && $request->end_day && $request->end_month && $request->end_year && $request->end_time) {
            $endDate = \Carbon\Carbon::createFromFormat(
                'Y F d H:i',
                "{$request->end_year} {$request->end_month} {$request->end_day} {$request->end_time}"
            );
        }

        // Upload image if present
        $imagePath = null;
        if ($request->hasFile('course_image')) {
            $imagePath = $request->file('course_image')->store('course_images', 'public');
        }

        // Parse participants
        $participants = $request->participants ? json_decode($request->participants, true) : [];
        Log::info('Parsed participants', ['participants' => $participants]);

        // Validate participants
        $validRoles = CourseUser::PARTICIPANT_ROLES;
        foreach ($participants as $participant) {
            if (!isset($participant['id']) || !isset($participant['name']) || !isset($participant['role'])) {
                Log::error('Invalid participant data', ['participant' => $participant]);
                return redirect()->back()->withErrors(['error' => 'Invalid participant data: Missing id, name, or role.']);
            }
            if (!is_numeric($participant['id']) || (int)$participant['id'] <= 0) {
                Log::error('Invalid participant ID', ['id' => $participant['id']]);
                return redirect()->back()->withErrors(['error' => 'Invalid participant ID: ' . $participant['id']]);
            }
            $participantRole = ucfirst(strtolower($participant['role']));
            if (!in_array($participantRole, $validRoles)) {
                Log::error('Invalid participant role', ['role' => $participant['role']]);
                return redirect()->back()->withErrors(['error' => 'Invalid participant role: ' . $participant['role']]);
            }
            // Verify user exists
            $user = User::find($participant['id']);
            if (!$user) {
                Log::error('User not found', ['user_id' => $participant['id']]);
                return redirect()->back()->withErrors(['error' => "User with ID {$participant['id']} not found."]);
            }
        }


        DB::beginTransaction();
        try {
            // Create the course
            $course = Course::create([
                'full_name' => $request->full_name,
                'short_name' => $request->short_name,
                'summary' => $request->summary,
                'cpmk' => $request->cpmk,
                'semester' => $request->semester,
                'visible' => $request->visible,
                'course_image' => $imagePath,
                'category' => $request->category,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Debug: Ensure course ID is valid
            if (!$course->id || !is_numeric($course->id) || (int)$course->id <= 0) {
                Log::error('Failed to create course: Invalid ID', ['course' => $course]);
                throw new \Exception('Failed to create course: Invalid ID');
            }
            Log::info('Course created', ['course_id' => $course->id]);

            // Create 10 default topics
            for ($i = 1; $i <= 10; $i++) {
                $topic = Topic::create([
                    'course_id' => $course->id,
                    'title' => "Judul Topik $i",
                    'sort_order' => $i,
                ]);
                Log::info("topic created: ID {$topic->id}, Course ID {$course->id}, Title: Judul Topik $i");
            }

            // Add participants to course_user table
            foreach ($participants as $participant) {

                $userId = (int) $participant['id'];
                $courseId = (int) $course->id;
                $participantRole = ucfirst(strtolower($participant['role']));

                if ($userId <= 0 || $courseId <= 0) {
                    Log::error('Invalid user_id or course_id', [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                    ]);
                    throw new \Exception('Invalid user_id or course_id');
                }

                $courseUserData = [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'participant_role' => $participantRole,
                ];
                Log::info('Attempting to create CourseUser entry', ['data' => $courseUserData]);

                try {
                    $courseUser = CourseUser::create($courseUserData);
                    Log::info("Participant added", [
                        'course_user_id' => $courseUser->id,
                        'course_id' => $course->id,
                        'user_id' => $userId,
                        'role' => $participantRole
                    ]);
                } catch (QueryException $e) {
                    Log::error('Failed to create CourseUser entry', [
                        'data' => $courseUserData,
                        'error' => $e->getMessage(),
                        'sql' => $e->getSql(),
                        'bindings' => $e->getBindings()
                    ]);
                    throw new \Exception('Failed to add participant: ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('admin.courses.index')->with('success', 'Course, topics, and participants successfully added');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create course, topics, or participants: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['error' => 'Failed to create course, topics, or participants: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $Course
     * @return \Illuminate\Http\Response
     */
     public function show($id)
    {
        $course = Course::with('topics', 'users')->findOrFail($id);
        $data = [
            'count_user' => DB::table('users')->count(),
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.course.course_detail',
            'course' => $course,
            'topics' => $course->topics ?? collect([]),
            'participants' => $course->users ?? collect([]),
        ];

        return view('layouts.v_template', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::with('users')->findOrFail($id);
        $data = [
            'count_user' => DB::table('users')->count(),
            'course' => $course,
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.course.edit'
        ];
        return view('layouts.v_template', $data);
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

        Log::info('Update request data received', $request->all());

        $request->validate([
            'full_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'summary' => 'required|string',
            'cpmk' => 'nullable|string',
            'semester' => 'required|in:1,2,3',
            'visible' => 'required|boolean',
            'course_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category' => 'required|string',
            'start_day' => 'required|integer|between:1,31',
            'start_month' => 'required|string',
            'start_year' => 'required|integer|between:2023,2030',
            'start_time' => 'required',
            'enable_end_date' => 'nullable',
            'end_day' => 'required_if:enable_end_date,1|integer|between:1,31',
            'end_month' => 'required_if:enable_end_date,1|string',
            'end_year' => 'required_if:enable_end_date,1|integer|between:2023,2030',
            'end_time' => 'required_if:enable_end_date,1',
            'participants' => 'nullable|string',
        ]);


        $startDate = \Carbon\Carbon::createFromFormat(
            'Y F d H:i',
            "{$request->start_year} {$request->start_month} {$request->start_day} {$request->start_time}"
        );


        $endDate = null;
        if ($request->has('enable_end_date') && $request->end_day && $request->end_month && $request->end_year && $request->end_time) {
            $endDate = \Carbon\Carbon::createFromFormat(
                'Y F d H:i',
                "{$request->end_year} {$request->end_month} {$request->end_day} {$request->end_time}"
            );
        }

        // Parse participants
        $participants = $request->participants ? json_decode($request->participants, true) : [];
        Log::info('Parsed participants', ['participants' => $participants]);

        // Validate participants
        $validRoles = CourseUser::PARTICIPANT_ROLES;
        foreach ($participants as $participant) {
            if (!isset($participant['id']) || !isset($participant['name']) || !isset($participant['role'])) {
                Log::error('Invalid participant data', ['participant' => $participant]);
                return redirect()->back()->withErrors(['error' => 'Invalid participant data: Missing id, name, or role.']);
            }
            if (!is_numeric($participant['id']) || (int)$participant['id'] <= 0) {
                Log::error('Invalid participant ID', ['id' => $participant['id']]);
                return redirect()->back()->withErrors(['error' => 'Invalid participant ID: ' . $participant['id']]);
            }
            $participantRole = ucfirst(strtolower($participant['role']));
            if (!in_array($participantRole, $validRoles)) {
                Log::error('Invalid participant role', ['role' => $participant['role']]);
                return redirect()->back()->withErrors(['error' => 'Invalid participant role: ' . $participant['role']]);
            }
            // Verify user exists
            $user = User::find($participant['id']);
            if (!$user) {
                Log::error('User not found', ['user_id' => $participant['id']]);
                return redirect()->back()->withErrors(['error' => "User with ID {$participant['id']} not found."]);
            }
        }


        DB::beginTransaction();
        try {
            // Find the course
            $course = Course::findOrFail($id);
            Log::info('Course found for update', ['course_id' => $course->id]);

            // Handle image upload
            $imagePath = $course->course_image;
            if ($request->hasFile('course_image')) {
                // Delete old image if exists
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                    Log::info('Old course image deleted', ['course_id' => $id, 'image_path' => $imagePath]);
                }
                $imagePath = $request->file('course_image')->store('course_images', 'public');
                Log::info('New course image uploaded', ['course_id' => $id, 'image_path' => $imagePath]);
            }

            // Update the course
            $course->update([
                'full_name' => $request->full_name,
                'short_name' => $request->short_name,
                'summary' => $request->summary,
                'cpmk' => $request->cpmk,
                'semester' => $request->semester,
                'visible' => $request->visible,
                'course_image' => $imagePath,
                'category' => $request->category,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
            Log::info('Course updated', ['course_id' => $course->id]);

            // Update participants
            CourseUser::where('course_id', $course->id)->delete();
            Log::info('Old participants removed', ['course_id' => $course->id]);

            foreach ($participants as $participant) {
                $userId = (int) $participant['id'];
                $courseId = (int) $course->id;
                $participantRole = ucfirst(strtolower($participant['role']));

                if ($userId <= 0 || $courseId <= 0) {
                    Log::error('Invalid user_id or course_id', [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                    ]);
                    throw new \Exception('Invalid user_id or course_id');
                }

                $courseUserData = [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'participant_role' => $participantRole,
                ];
                Log::info('Attempting to create CourseUser entry', ['data' => $courseUserData]);

                try {
                    $courseUser = CourseUser::create($courseUserData);
                    Log::info("Participant added", [
                        'course_user_id' => $courseUser->id,
                        'course_id' => $course->id,
                        'user_id' => $userId,
                        'role' => $participantRole
                    ]);
                } catch (QueryException $e) {
                    Log::error('Failed to create CourseUser entry', [
                        'data' => $courseUserData,
                        'error' => $e->getMessage(),
                        'sql' => $e->getSql(),
                        'bindings' => $e->getBindings()
                    ]);
                    throw new \Exception('Failed to add participant: ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('admin.courses.index')->with('success', 'Course and participants successfully updated');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update course or participants: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['error' => 'Failed to update course or participants: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Cari kursus berdasarkan ID
            $course = Course::findOrFail($id);

            // Hapus gambar kursus dari storage jika ada
            if ($course->course_image) {
                Storage::disk('public')->delete($course->course_image);
            }

            // Hapus relasi di tabel course_user
            $courseUserDeleted = CourseUser::where('course_id', $id)->delete();

            // Hapus topics yang terkait
            $topicsDeleted = Topic::where('course_id', $id)->delete();

            // Hapus kursus
            $course->delete();

            DB::commit();
           // 🔹 Jika permintaan dari AJAX (misal lewat $.ajax di halaman index)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully.'
            ]);
        }

        // 🔹 Jika permintaan bukan AJAX (misal lewat form biasa)
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();

        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete course: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->route('admin.courses.index')->with('error', 'Failed to delete course: ' . $e->getMessage());
    }
    }


   public function management(Request $request)
{
    // Get search and pagination parameters
    $searchValue = $request->input('search', '');
    $perPage = $request->input('per_page', 10);

    // Build the main query for courses
    $query = Course::select('courses.*')
        ->with(['users' => function ($query) {
            $query->where('course_users.participant_role', 'Teacher')
                ->select('users.id', 'users.name');
        }]);

    // Apply search filter 
    if (!empty($searchValue)) {
        $query->where(function ($q) use ($searchValue) {
            $q->where('short_name', 'like', "%{$searchValue}%")
                ->orWhere('full_name', 'like', "%{$searchValue}%")
                ->orWhere('semester', 'like', "%{$searchValue}%");
        });
    }

    // Apply pagination
    $courses = $query->paginate($perPage);

    // Verify pagination result
    if (!($courses instanceof \Illuminate\Pagination\LengthAwarePaginator)) {
        Log::error('Courses is not a LengthAwarePaginator', ['courses' => $courses]);
        throw new \Exception('Failed to paginate courses');
    }

    // Prepare data for view
    $data = [
        'courses' => $courses,
        'menu' => 'menu.v_menu_admin',
        'content' => 'admin.course.courses_management'
    ];

    return view('layouts.v_template', $data);
}

}


