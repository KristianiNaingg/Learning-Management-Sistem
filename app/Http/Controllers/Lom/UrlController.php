<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomUrl;
use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UrlController extends Controller{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subtopicId = $request->query('sub_topic_id');
        $selectedSubtopic = $subtopicId ? Subtopic::findOrFail($subtopicId) : null;
        $learningDimensions = LearningDimension::with('options')->get();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.urls.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
    }

    /**
     * Store newly created URLs in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'subtopic_id' => 'required|exists:subtopics,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'urls.*.url_link' => 'required|url|max:255',
            'urls.*.name' => 'required|string|max:255',
            'dimension' => 'required|exists:learning_dimensions,id|in:2,3',
            'dimension_options' => 'required|array|min:1',
            'dimension_options.*' => 'required|exists:learning_style_options,id',
        ];

        // Custom validation messages
        $messages = [
            'subtopic_id.required' => 'The subtopic is required.',
            'subtopic_id.exists' => 'The selected subtopic is invalid.',
            'name.required' => 'The URL group name is required.',
            'name.max' => 'The URL group name may not be longer than 255 characters.',
            'urls.*.url_link.required' => 'Each URL link is required.',
            'urls.*.url_link.url' => 'Each URL must be a valid URL (e.g., https://example.com).',
            'urls.*.url_link.max' => 'Each URL link may not be longer than 255 characters.',
            'urls.*.name.required' => 'Each URL name is required.',
            'urls.*.name.max' => 'Each URL name may not be longer than 255 characters.',
            'dimension.required' => 'The learning dimension is required.',
            'dimension.in' => 'The learning dimension must have ID 2 or 3.',
            'dimension_options.required' => 'At least one learning style option must be selected.',
            'dimension_options.*.required' => 'Each learning style option is required.',
            'dimension_options.*.exists' => 'The selected learning style option is invalid.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Fetch the subtopic
        $subtopic = Subtopic::findOrFail($request->subtopic_id);

        // Create URLs within a transaction
        $urlGroup = DB::transaction(function () use ($request, $subtopic) {
            // Create a parent record to group URLs (mimicking folder)
            $urlGroup = LomUrl::create([
                'subtopic_id' => $request->subtopic_id,
                'name' => $request->name,
                'description' => $request->description,
                'learning_style_option_id' => $request->dimension_options[0],
                'is_group' => true, // Flag to indicate this is a group/parent record
                'created_at' => now(),
            ]);

            // Handle multiple URL submissions
            if ($request->has('urls')) {
                foreach ($request->urls as $urlData) {
                    LomUrl::create([
                        'subtopic_id' => $request->subtopic_id,
                        'name' => $urlData['name'],
                        'url_link' => $urlData['url_link'],
                        'learning_style_option_id' => $request->dimension_options[0],
                        'parent_id' => $urlGroup->id, // Link to the parent group
                        'created_at' => now(),
                    ]);
                }
            }

            return $urlGroup;
        });

        // Redirect to the topic show page with success mess   0age
        return redirect()->route('topics.show', [$subtopic->topic->course_id, $subtopic->topic_id])
            ->with('success', 'URLs created successfully.');
    }
}