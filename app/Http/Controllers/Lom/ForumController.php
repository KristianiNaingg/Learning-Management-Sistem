<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomForum;
use App\Models\LomAssignGrade;
use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = LomAssign::with('subtopic')->get();
        $menu = 'menu.v_menu_admin';
        return view('assignments.index', compact('assignments', 'menu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subtopicId = $request->query('sub_topic_id');
        $selectedSubtopic = $subtopicId ? Subtopic::findOrFail($subtopicId) : null;
        $learningDimensions = LearningDimension::with('options')->where('id', 3)->get();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.forums.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
    }
   
   public function store(Request $request)
    {
      

        $validator = Validator::make($request->all(), [
            'subtopic_id' => 'required|exists:subtopics,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dimension' => 'nullable|exists:learning_dimensions,id',
            'dimension_options' => 'required_if:dimension,1|exists:learning_style_options,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        LomForum::create([
            'subtopic_id' => $request->subtopic_id,
            'name' => $request->name,
            'description' => $request->description,
            'learning_style_option_id' => $request->dimension_options,
        ]);

        $subtopic = Subtopic::findOrFail($request->subtopic_id);
        return redirect()->route('topics.show', [
            $subtopic->topic->course_id,
            $subtopic->topic_id
        ])->with('success', 'Forum created successfully.');
    }

    // Show the form for editing a forum
    public function edit($course_id, $topic_id, $subtopic_id, $id)
    {
        $forum = LomForum::findOrFail($id);
        $selectedSubtopic = Subtopic::findOrFail($subtopic_id);
        $learningDimensions = LearningStyleOption::where('learning_dimension_id', 1)->get();
        return view('forums.edit', compact('forum', 'selectedSubtopic', 'learningDimensions', 'course_id', 'topic_id'));
    }

    // Update the specified forum
    public function update(Request $request, $id)
    {
        $forum = LomForum::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'subtopic_id' => 'required|exists:subtopics,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dimension' => 'nullable|exists:learning_dimensions,id',
            'dimension_options' => 'required_if:dimension,1|exists:learning_style_options,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $forum->update([
            'subtopic_id' => $request->subtopic_id,
            'name' => $request->name,
            'description' => $request->description,
            'learning_style_option_id' => $request->dimension_options,
        ]);

        return redirect()->route('forums.index', [
            $request->course_id,
            $request->topic_id,
            $request->subtopic_id
        ])->with('success', 'Forum updated successfully.');
    }

    // Remove the specified forum
    public function destroy($course_id, $topic_id, $subtopic_id, $id)
    {
        $forum = LomForum::findOrFail($id);
        $forum->delete();
        return redirect()->route('forums.index', [$course_id, $topic_id, $subtopic_id])
            ->with('success', 'Forum deleted successfully.');
    }
}