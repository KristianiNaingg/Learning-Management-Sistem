<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Topic;
use App\Models\Course;
use App\Models\Subtopic;

class SubtopicController extends Controller
{
    /**
     * Display a listing of subtopics for a specific topic.
     *
     * @param int $course_id The ID of the course
     * @param int $topic_id The ID of the topic
     * @return \Illuminate\View\View
     */
    // public function index($course_id, $topic_id)
    // {
    //     $course = Course::findOrFail($course_id);
    //     $topic = Topic::where('id', $topic_id)->where('course_id', $course_id)->firstOrFail();
    //     $subtopics = Subtopic::where('topic_id', $topic_id)->orderBy('sortorder')->get();

    //     return view('sections.index', compact('course', 'topic', 'subtopics'));
    // }

    /**
     * Show the form for creating a new subtopic.
     *
     * @param \App\Models\Course $course The course instance
     * @param int $topic_id The ID of the topic
     * @return \Illuminate\View\View
     */
    public function create(Course $course, $topic_id)
    {
        $topic = Topic::where('id', $topic_id)
                      ->where('course_id', $course->id)
                      ->firstOrFail();

        return view('layouts.v_template', [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.subtopic.create',
            'course' => $course,
            'topic' => $topic,
        ]);
    }

    /**
     * Store a newly created subtopic in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $course_id The ID of the course
     * @param int $topic_id The ID of the topic
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $course_id, $topic_id)
    {
        $topic = Topic::where('id', $topic_id)
                      ->where('course_id', $course_id)
                      ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'sortorder' => 'required|integer|min:1',
            'visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $subtopic = Subtopic::create([
            'topic_id' => $topic->id,
            'title' => $request->title,
            'visible' => $request->visible ?? 1,
            'sortorder' => $request->sortorder,
        ]);

        return response()->json([
            'id' => $subtopic->id,
            'title' => $subtopic->title,
            'sortorder' => $subtopic->sortorder,
            'visible' => $subtopic->visible,
        ], 201);
    }

    /**
     * Display the form for editing a specific subtopic.
     *
     * @param int $course_id The ID of the course
     * @param int $topic_id The ID of the topic
     * @param int $subtopic_id The ID of the subtopic
     * @return \Illuminate\View\View
     */
   public function edit($course_id, $topic_id, $subtopic_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();

        // Cari topic dengan validasi course_id
        $topic = Topic::where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        // Cari subtopic dengan validasi topic_id
        $subtopic = Subtopic::where('id', $subtopic_id)
            ->where('topic_id', $topic_id)
            ->firstOrFail();



        $data = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.subtopic.edit',
            'course' => $course,
            'topic' => $topic,
            'subtopic' => $subtopic,
        ];

        return view('layouts.v_template', $data);
    }

    /**
     * Update the specified subtopic in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $course_id The ID of the course
     * @param int $topic_id The ID of the topic
     * @param int $subtopic_id The ID of the subtopic
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $course_id, $topic_id, $subtopic_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'sortorder' => 'required|integer|min:1',
            'visible' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $request->expectsJson()
                ? response()->json(['message' => $validator->errors()->first()], 422)
                : back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $topic = Topic::where('id', $topic_id)
                          ->where('course_id', $course_id)
                          ->firstOrFail();

            $subtopic = Subtopic::where('id', $subtopic_id)
                               ->where('topic_id', $topic_id)
                               ->firstOrFail();

            $subtopic->update([
                'title' => $request->title,
                'visible' => $request->visible ?? 1,
                'sortorder' => $request->sortorder,
            ]);

            DB::commit();

            Log::info('Subtopic updated successfully', [
                'subtopic_id' => $subtopic->id,
                'topic_id' => $topic_id,
                'course_id' => $course_id,
                'title' => $subtopic->title,
            ]);

            return $request->expectsJson()
                ? response()->json([
                    'success' => true,
                    'subtopic' => [
                        'id' => $subtopic->id,
                        'title' => $subtopic->title,
                        'visible' => $subtopic->visible,
                        'sortorder' => $subtopic->sortorder,
                    ],
                ])
                : redirect()->route('topics.show', [$course_id, $topic_id])
                           ->with('success', 'Subtopic updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update subtopic', [
                'subtopic_id' => $subtopic_id,
                'topic_id' => $topic_id,
                'course_id' => $course_id,
                'error' => $e->getMessage(),
            ]);

            return $request->expectsJson()
                ? response()->json(['message' => 'Failed to update subtopic'], 500)
                : back()->with('error', 'Failed to update subtopic')->withInput();
        }
    }
}
