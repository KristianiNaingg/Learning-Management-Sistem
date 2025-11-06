<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomInfographic;
use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class InfographicController extends Controller
{
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

        return view('lom.infographics.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subtopic_id' => 'required|exists:subtopics,id',
            'file_path' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'dimension' => 'nullable|exists:learning_dimensions,id',
            'dimension_options' => 'required_if:dimension,1|exists:learning_style_options,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $filePath = $request->file('file_path')->store('infographics', 'public');

        LomInfographic::create([
            'subtopic_id' => $request->subtopic_id,
            'file_path' => $filePath,
            'learning_style_option_id' => $request->dimension_options,
        ]);

        $subtopic = Subtopic::findOrFail($request->subtopic_id);
        return redirect()->route('topics.show', [
            $subtopic->topic->course_id,
            $subtopic->topic_id
        ])->with('success', 'Infographic created successfully.');
    }


}
