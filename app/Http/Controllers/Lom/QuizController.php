<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomQuiz;

use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class QuizController extends Controller
{
  /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subtopicId = $request->query('sub_topic_id');
        $selectedSubtopic = $subtopicId ? Subtopic::findOrFail($subtopicId) : null;
        $learningDimensions = LearningDimension::with('options')->where('id', 1)->get();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.quizzes.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
    }

   /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate form inputs with custom error messages
            $validated = $request->validate([
                'sub_topic_id' => 'required|integer|exists:subtopics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'learning_style_id' => 'required|integer|exists:learning_dimensions,id',
                'time_open' => 'nullable|date',
                'time_close' => 'nullable|date|after:time_open',
                'time_limit' => 'nullable|integer|min:0', // Input in seconds
                'max_attempts' => 'required|integer|min:1',
                'grade_to_pass' => 'required|numeric|min:0|max:100',
                'action' => 'required|in:save_return,save_display',
            ], [
                'sub_topic_id.exists' => 'The selected sub-topic is invalid.',
                'name.required' => 'Please provide a quiz name.',
                'name.max' => 'The quiz name cannot exceed 255 characters.',
                'learning_style_id.required' => 'Please select a learning dimension.',
                'learning_style_id.exists' => 'The selected dimension is invalid.',
                'time_close.after' => 'The close time must be after the open time.',
                'time_limit.min' => 'The time limit cannot be negative.',
                'max_attempts.required' => 'Please specify the number of attempts.',
                'max_attempts.min' => 'At least one attempt is required.',
                'grade_to_pass.required' => 'Please specify the grade to pass.',
                'grade_to_pass.min' => 'The grade to pass cannot be negative.',
                'grade_to_pass.max' => 'The grade to pass cannot exceed 100.',
                'action.in' => 'Invalid action selected.',
            ]);

           

            // Create quiz
            $quiz = LomQuiz::create([
                'subtopic_id' => $validated['sub_topic_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'time_open' => $validated['time_open'],
                'time_close' => $validated['time_close'],
                'time_limit' => $validated['time_limit'], // Already in seconds
                'max_attempts' => $validated['max_attempts'],
                'grade_to_pass' => $validated['grade_to_pass'],
                'learning_dimension_id' => $validated['learning_style_id'],
            ]);

            Log::info('Quiz created successfully', [
                'quiz_id' => $quiz->id,
                'subtopic_id' => $validated['sub_topic_id'],
                'name' => $validated['name'],
                'learning_dimension_id' => $validated['learning_style_id'],
                'time_limit_seconds' => $validated['time_limit'],
                'action' => $validated['action'],
            ]);

            $subtopic = Subtopic::findOrFail($validated['sub_topic_id']);
            $topic = $subtopic->topic;
            $course_id = $topic->course_id;
            $topic_id = $topic->id;

            $topic = $subtopic->topic;
            $course_id = $topic->course_id;
            $topic_id = $topic->id;

            // Redirect based on action
            if ($validated['action'] === 'save_return') {
                return redirect()->route('topics.show', [$course_id, $topic_id])
                    ->with('success', 'Quiz created successfully.');
            } else {
                return redirect()->route('quizs.show', $quiz->id)
                    ->with('success', 'Quiz created successfully.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for quiz submission', [
                'errors' => $e->errors(),
                'input' => $request->except('_token'),
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Please correct the errors below and try again.')
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during quiz submission', [
                'message' => $e->getMessage(),
                'input' => $request->except('_token'),
            ]);
            return redirect()->back()
                ->with('error', 'An unexpected error occurred while creating the quiz. Please try again or contact support.')
                ->withInput();
        }

    
    }
     public function show($id)
    {} 
}
