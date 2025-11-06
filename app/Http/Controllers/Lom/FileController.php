<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\LomFile;
use App\Models\LomFolder;

use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
   public function create(Request $request)
    {
        $subtopicId = $request->query('sub_topic_id');
        $selectedSubtopic = $subtopicId ? Subtopic::findOrFail($subtopicId) : null;
        $learningDimensions = LearningDimension::with('options')->get();

        $folders = LomFolder::all(); // Fetch all folders for selection

        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.files.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu','folders'));
    }

   public function store(Request $request)
    {
        try {
            Log::info('Request data:', $request->all());

            // Validasi input
            $request->validate([
                'sub_topic_id' => 'required|exists:subtopics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file_path.*' => 'required|file|mimes:pdf,doc, newly addeddocx,jpg,jpeg,png,zip,rar|max:10240',
                'dimension' => 'nullable|exists:learning_dimensions,id',
                'dimension_options' => 'required_if:dimension,!=,null|array',
                'dimension_options.*' => 'exists:learning_style_option,id',
                'folders' => 'nullable|array',
                'folders.*' => 'exists:lom_folders,id',
            ]);

            // Validasi subtopic_id
            $subtopic = Subtopic::find($request->sub_topic_id);
            if (!$subtopic) {
                Log::error('Invalid sub_topic_id: ' . $request->sub_topic_id);
                return back()->withErrors(['error' => 'Invalid subtopic ID']);
            }

            // Validasi dimension
            if ($request->dimension && !LearningDimension::find($request->dimension)) {
                Log::error('Invalid dimension ID: ' . $request->dimension);
                return back()->withErrors(['error' => 'Invalid dimension ID']);
            }

            // Validasi dimension_options
            if ($request->dimension && $request->dimension_options) {
                foreach ($request->dimension_options as $optionId) {
                    // Menggunakan learning_dimensions_id sesuai model
                    if (!LearningStyleOption::where('id', $optionId)->where('learning_dimensions_id', $request->dimension)->exists()) {
                        Log::error('Invalid dimension_options ID: ' . $optionId);
                        return back()->withErrors(['error' => 'Invalid dimension option']);
                    }
                }
            }

            // Siapkan data untuk penyimpanan
            $data = $request->only(['name', 'description']);
            $data['subtopic_id'] = $request->sub_topic_id; // Sesuai dengan model LomFile
            $data['learning_style_option_id'] = $request->dimension_options ? $request->dimension_options[0] : null;

            DB::beginTransaction();

            // Handle multiple file uploads
            if ($request->hasFile('file_path')) {
                foreach ($request->file('file_path') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('lom_files', $fileName, 'public');
                    Log::info('File stored at: ' . $filePath);
                    $data['file_path'] = $filePath;

                    $lomFile = LomFile::create($data);
                    Log::info('LomFile created with ID: ' . $lomFile->id);

                    if ($request->has('folders')) {
                        $lomFile->folders()->sync($request->folders);
                        Log::info('Folders synced: ', $request->folders);
                    }
                }
            } else {
                Log::warning('No file uploaded');
                return back()->withErrors(['error' => 'No file uploaded']);
            }

            DB::commit();
            return redirect()->route('topics.show', [$lomFile->subtopic->topic->course_id, $lomFile->subtopic->topic_id])
                           ->with('success', 'LOM File created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing file: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to store file: ' . $e->getMessage()]);
        }
    }
}
