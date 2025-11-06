<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Topic;
use App\Models\Course;
use App\Models\Subtopic;
use App\Models\TopicReference;
use App\Models\LomAssign;
use App\Models\LomFile;
use App\Models\LomFolder;
use App\Models\LomForum;
use App\Models\LomInfographic;
use App\Models\LomLabel;
use App\Models\LomLesson;
use App\Models\LomPage;
use App\Models\LomQuiz;
use App\Models\LomUrl;
use App\Models\UserLearningStyleOption;

class TopicController extends Controller
{
    
    /**
     * Tampilkan daftar topik untuk student
     */
    public function indexStudent($course_id)
    {
        // Ambil course beserta topik yang visible
        $course = Course::with(['topics' => function($query) {
            $query->where('visible', 1)->orderBy('sort_order', 'asc');
        }])->find($course_id);

        if (!$course) {
            abort(404, "Course dengan ID $course_id tidak ditemukan.");
        }

        $data = [
            'menu'   => 'menu.v_menu_admin',
            'content'=> 'student.topics',
            'title'  => $course->full_name,
            'course' => $course,
        ];

        return view('layouts.v_template', $data);
    }



 public function showStudent($course_id, Request $request)
{
    $topic_id = $request->query('topic_id'); // Ambil dari query string

    // Ambil data pengguna dan gaya belajar
    $user = Auth::user();
    $userId = $user->id;
    Log::info('Memulai showStudent untuk user_id: ' . $userId . ', course_id: ' . $course_id . ', topic_id: ' . $topic_id . ' pada ' . now()->format('d-m-Y H:i:s'));

    // Validasi gaya belajar pengguna
    $userLearningStyles = UserLearningStyleOption::where('user_id', $userId)->get();
    if ($userLearningStyles->isEmpty()) {
        Log::error('User ' . $userId . ' tidak memiliki gaya belajar.');
        return redirect()->back()->with('error', 'User belum memiliki gaya belajar.');
    }

    $oppositeStyles = [
        1 => 2, 2 => 1,
        3 => 4, 4 => 3,
        5 => 6, 6 => 5,
        7 => 8, 8 => 7
    ];

    $strongStyles = $userLearningStyles->where('category', 'Strong')->pluck('learning_style_option_id')->toArray();
    $moderateStyles = $userLearningStyles->where('category', 'Moderate')->pluck('learning_style_option_id')->toArray();
    $balancedStyles = $userLearningStyles->where('category', 'Balanced')->pluck('learning_style_option_id')->toArray();

    $allowedStyles = array_merge($strongStyles, $moderateStyles, $balancedStyles);
    foreach ($balancedStyles as $id) {
        if (isset($oppositeStyles[$id])) $allowedStyles[] = $oppositeStyles[$id];
    }
    $allowedStyles = array_unique($allowedStyles);

    // Validasi course
    $course = Course::find($course_id);
    if (!$course) return redirect()->back()->with('error', 'Course tidak ditemukan.');

    // Ambil topic dengan relasi subtopics dan resources
    $topic = Topic::with([
        'subtopics' => function ($q) {
            $q->orderBy('sortorder', 'asc')->with([
                'labels.learningStyleOption',
                'forums.learningStyleOption',
                'pages.learningStyleOption',
                'assigns.learningStyleOption',
                'quizs',
                'files.learningStyleOption',
                'folders.learningStyleOption',
                'lessons.learningStyleOption',
                'urls.learningStyleOption',
                'infographics.learningStyleOption',
            ]);
        },
        'references'
    ])->where('id', $topic_id)->where('course_id', $course_id)->first();

    if (!$topic) return redirect()->back()->with('error', 'Topic tidak ditemukan.');

    $previousModerateStyles = null;
    $previousFailedAllQuizzes = false;

    foreach ($topic->subtopics as $sub) {
        $newAllowedStyles = $allowedStyles;
        $isFirst = $topic->subtopics->first()->id == $sub->id;

        if (!$isFirst && !empty($moderateStyles)) {
            $prevSub = $topic->subtopics->where('sortorder', '<', $sub->sortorder)->sortByDesc('sortorder')->first();
            if ($prevSub) {
                $prevQuizzes = LomQuiz::where('subtopic_id', $prevSub->id)->get();
                $failedDims = [];
                foreach ($prevQuizzes as $quiz) {
                    $attempt = $quiz->attempts()->where('user_id', $userId)->orderBy('score', 'desc')->first();
                    $dim = $quiz->learning_style_option_id ?? null;
                    if ($dim && $attempt && $attempt->score < ($quiz->grade_to_pass ?? 0)) $failedDims[] = $dim;
                }
                $previousFailedAllQuizzes = count($failedDims) >= 3;

                if ($previousFailedAllQuizzes && $previousModerateStyles) {
                    $newModerate = [];
                    foreach ($previousModerateStyles as $id) $newModerate[] = $oppositeStyles[$id] ?? $id;
                    $newAllowedStyles = array_merge($strongStyles, $newModerate, $balancedStyles);
                    foreach ($balancedStyles as $id) if (isset($oppositeStyles[$id])) $newAllowedStyles[] = $oppositeStyles[$id];
                    $newAllowedStyles = array_unique($newAllowedStyles);
                }
            }
        }

        $previousModerateStyles = array_intersect($newAllowedStyles, $moderateStyles);

        // Filter resources sesuai allowed styles
        $sub->labels = $sub->labels->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->forums = $sub->forums->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->pages = $sub->pages->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->assigns = $sub->assigns->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->files = $sub->files->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->folders = $sub->folders->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->lessons = $sub->lessons->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->urls = $sub->urls->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));
        $sub->infographics = $sub->infographics->filter(fn($item) => in_array($item->learningStyleOption->id ?? 0, $newAllowedStyles));

        $sub->quizs = $sub->quizs;

        $sub->sorted_items = collect([])
            ->merge($sub->labels)
            ->merge($sub->files)
            ->merge($sub->infographics)
            ->merge($sub->assigns)
            ->merge($sub->forums)
            ->merge($sub->lessons)
            ->merge($sub->urls)
            ->merge($sub->folders)
            ->merge($sub->pages)
            ->merge($sub->quizs)
            ->sortBy('created_at')
            ->values();
    }

    $data = [
        'menu' => 'menu.v_menu_admin',
        'content' => 'student.showTopic',
        'title' => $topic->title,
        'course' => $course,
        'topic' => $topic,
    ];
 

    return view('layouts.v_template', $data);
}






    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $course_id
     * @return \Illuminate\Http\Response
     */
    public function create($course_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();

        $data = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.course.topicCreate', // Fixed view path
            'title' => 'Create Section for ' . $course->full_name,
            'course' => $course,
        ];

        return view('layouts.v_template', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $course_id
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request, $course_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();

        // Validasi input untuk AJAX request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Ambil ID dan sort_order terakhir untuk course_id tertentu
            $lastTopic = Topic::where('course_id', $course_id)
                ->orderBy('id', 'desc')
                ->first();

            // Tentukan ID dan sort_order baru
            $newTopicId = $lastTopic ? $lastTopic->id + 1 : 1;
            $newSortOrder = $lastTopic ? $lastTopic->sort_order + 1 : 1;

            // Buat topik baru
            $topic = Topic::create([
                'id' => $newTopicId,
                'course_id' => $course_id,
                'type' => 'Perkuliahan',
                'title' => 'Judul Topik ' . $newSortOrder,
                'description' => 'Deskripsi untuk Topik ' . $newSortOrder,
                'sub_cpmk' => 'CPMK untuk Topik ' . $newSortOrder,
                'visible' => 1,
                'sort_order' => $newSortOrder,
            ]);

            DB::commit();

            // Kembalikan respons JSON untuk frontend
            return response()->json([
                'success' => true,
                'topic' => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'sort_order' => $topic->sort_order,
                ],
                'message' => 'Topik berhasil ditambahkan',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan topik: ' . $e->getMessage(),
            ], 500);
        }
    }
        /**
     * Display the specified resource.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\Response
     */
    public function show($course_id, $topic_id)
    {

    $course = Course::where('id', $course_id)->firstOrFail();
    $topic = Topic::with([
        'subtopics.labels.learningStyleOption',
        'subtopics.files.learningStyleOption',
        'subtopics.infographics.learningStyleOption',
        'subtopics.assigns.learningStyleOption',
        'subtopics.forums.learningStyleOption',
        'subtopics.lessons.learningStyleOption',
        'subtopics.urls.learningStyleOption',
        'subtopics.folders.learningStyleOption',
        'subtopics.pages.learningStyleOption',
        // 'subtopics.quizs.dimensions',
        'references'
    ])
        ->where('id', $topic_id)
        ->where('course_id', $course_id)
        ->firstOrFail();

    $topics = Topic::where('course_id', $course_id)
        ->where('visible', 1)
        ->orderBy('sort_order', 'asc') // Urutkan berdasarkan sort_order
        ->orderBy('id', 'asc')
        ->get();

    $indeks = $topics->search(function ($item) use ($topic) {
        return $item->id == $topic->id;
    }) + 1;

    // Sort items per subtopic by created_at
    foreach ($topic->subtopics as $subTopic) {
        $items = collect([])
            ->merge($subTopic->labels)
            ->merge($subTopic->files)
            ->merge($subTopic->infographics)
            ->merge($subTopic->assigns)
            ->merge($subTopic->forums)
            ->merge($subTopic->lessons)
            ->merge($subTopic->urls)
            ->merge($subTopic->folders)
            ->merge($subTopic->pages)
            ->merge($subTopic->quizs)
            ->sortBy('created_at')
            ->values();

        $subTopic->sorted_items = $items;
    }

    $data = [
        'menu' => 'menu.v_menu_admin',
        'content' => 'admin.topic.show',
        'indeks' => $indeks,
        'title' => $topic->title,
        'course' => $course,
        'topic' => $topic,
        'topics' => $topics, // Pastikan $topics dikirim ke view
    ];

    return view('layouts.v_template', $data);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\Response
     */
    public function edit($course_id, $topic_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();
        $topic = Topic::with(['subtopics', 'references'])
            ->where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        $data = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.topic.edit',
            'title' => 'Edit Section: ' . $topic->title,
            'course' => $course,
            'topic' => $topic,
        ];

        return view('layouts.v_template', $data);
    }

   /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $course_id, $topic_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();
        $topic = Topic::where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        // Validate input (removed 'type', made sort_order nullable)
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sub_cpmk' => 'required|string',
            'visible' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:1',
            'subtopics.*.title' => 'required|string|max:255',
            'subtopics.*.visible' => 'nullable|boolean',
            'subtopics.*.sortorder' => 'required|integer|min:1',
            'references.*.content' => 'required|string',
        ]);

        if ($validator->fails()) {
            dd($request->all(), $validator->errors());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $topic->update([
                'title' => $request->title,
                'description' => $request->description,
                'sub_cpmk' => $request->sub_cpmk,
                'visible' => $request->visible ?? 1,
                'sort_order' => $request->sort_order ?? $topic->sort_order, // Use existing sort_order if not provided
            ]);

            $inputSubTopicIds = [];
            $inputReferenceIds = [];

            if ($request->has('subtopics')) {
                foreach ($request->subtopics as $index => $subtopic) {
                    $data = [
                        'topic_id' => $topic->id,
                        'title' => $subtopic['title'],
                        'visible' => $subtopic['visible'] ?? 1,
                        'sortorder' => $subtopic['sortorder'] ?? ($index + 1),
                    ];

                    if (!empty($subtopic['id'])) {
                        $existingSubTopic = Subtopic::where('id', $subtopic['id'])
                            ->where('topic_id', $topic->id)
                            ->first();
                        if ($existingSubTopic) {
                            $existingSubTopic->update($data);
                            $inputSubTopicIds[] = $subtopic['id'];
                        }
                    } else {
                        $newSubTopic = Subtopic::create($data);
                        $inputSubTopicIds[] = $newSubTopic->id;
                    }
                }
            }

            // Update or create references
            if ($request->has('references')) {
                foreach ($request->references as $reference) {
                    $data = [
                        'topic_id' => $topic->id,
                        'content' => $reference['content'],
                    ];

                    if (!empty($reference['id'])) {
                        $existingReference = TopicReference::where('id', $reference['id'])
                            ->where('topic_id', $topic->id)
                            ->first();
                        if ($existingReference) {
                            $existingReference->update($data);
                            $inputReferenceIds[] = $reference['id'];
                        }
                    } else {
                        $newReference = TopicReference::create($data);
                        $inputReferenceIds[] = $newReference->id;
                    }
                }
            }

            // Delete subtopics not in input
            Subtopic::where('topic_id', $topic->id)
                ->whereNotIn('id', $inputSubTopicIds)
                ->delete();

            // Delete references not in input
            TopicReference::where('topic_id', $topic->id)
                ->whereNotIn('id', $inputReferenceIds)
                ->delete();

            // Reload topic with relationships
            $topic->load('subtopics', 'references');


            DB::commit();
            return redirect()->route('topics.show', [$course_id, $topic_id])
                ->with('success', 'Topic updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating topic: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update topic: ' . $e->getMessage()])
                ->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($course_id, $topic_id)
{
    $course = Course::findOrFail($course_id);
    $topic = Topic::where('id', $topic_id)
        ->where('course_id', $course_id)
        ->firstOrFail();

    DB::beginTransaction();

    try {
        $topic->delete();
        DB::commit();

        // 🔹 Jika request berasal dari AJAX (misal fetch/SweetAlert)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic deleted successfully.'
            ]);
        }

        // 🔹 Jika request biasa (non-AJAX)
        return redirect()
            ->route('courses.topics', $course_id)
            ->with('success', 'Topic deleted successfully.');

    } catch (\Throwable $e) { // pakai Throwable agar tangkap semua error
        DB::rollBack();
        Log::error('Error deleting topic: ' . $e->getMessage(), [
            'course_id' => $course_id,
            'topic_id' => $topic_id,
        ]);

        // 🔹 Response untuk AJAX
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete topic: ' . $e->getMessage(),
            ], 500);
        }

        // 🔹 Redirect jika bukan AJAX
        return redirect()
            ->back()
            ->with('error', 'Failed to delete topic: ' . $e->getMessage());
    }
}


    /**
     * Store a new subtopic for a topic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * Delete a subtopic.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @param  int  $subtopic_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySubtopic($course_id, $topic_id, $subtopic_id)
    {
        $topic = Topic::where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        $subtopic = Subtopic::where('id', $subtopic_id)
            ->where('topic_id', $topic->id)
            ->firstOrFail();

        $subtopic->delete();

        return response()->json(['message' => 'Subtopic deleted successfully.']);
    }

    /**
     * Store a new reference for a topic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * Delete a reference.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @param  int  $reference_id
     * @return \Illuminate\Http\JsonResponse
     */


     public function destroyReference($course_id, $topic_id, $reference_id)
    {
        $topic = Topic::where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        $reference = TopicReference::where('id', $reference_id)
            ->where('topic_id', $topic->id)
            ->firstOrFail();

        $reference->delete();

        return response()->json(['message' => 'Referensi deleted successfully.']);
    }







    /**
     * Duplicate a resource.
     *
     * @param  string  $type
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicate($type, $id)
    {
        try {
            $modelMap = [
                'label' => LomLabel::class,
                'files' => LomFile::class,
                'infografis' => LomInfographic::class,
                'assign' => LomAssign::class,
                'forum' => LomForum::class,
                'lesson' => LomLesson::class,
                'url' => LomUrl::class,
                'folder' => LomFolder::class,
                'page' => LomPage::class,
            ];

            if (!isset($modelMap[$type])) {
                return response()->json(['success' => false, 'message' => 'Invalid resource type'], 400);
            }

            $model = $modelMap[$type];
            $item = $model::findOrFail($id);
            $newItem = $item->replicate();
            $newItem->title = $newItem->title . ' (Copy)'; 
            $newItem->save();

            return response()->json(['success' => true, 'message' => 'Resource duplicated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Duplication failed: ' . $e->getMessage()], 500);
        }
    }






}
