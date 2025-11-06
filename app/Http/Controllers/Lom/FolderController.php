<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomFolder;
use App\Models\LomFile;
use App\Models\LomFileSave;
use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class FolderController extends Controller
{
    public function create(Request $request)
    {
        $subtopicId = $request->query('sub_topic_id');
        $selectedSubtopic = $subtopicId ? Subtopic::findOrFail($subtopicId) : null;
        $learningDimensions = LearningDimension::with('options')->get();

        $folders = LomFolder::all();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.folders.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu','folders'));
    }

    /**
     * Store a newly created folder in storage.
     */
    public function store(Request $request)
    {
     
        try {
          
            // Define validation rules
            $validated = $request->validate([
                'subtopic_id' => 'required|exists:subtopics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'files.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar|max:5120',
                'dimension' => 'required|exists:learning_dimensions,id|in:2,3',
                'dimension_options' => 'required|array|min:1',
                'dimension_options.*' => 'required|exists:learning_style_options,id',
            ], [
                'subtopic_id.required' => 'Subtopik wajib dipilih.',
                'subtopic_id.exists' => 'Subtopik yang dipilih tidak valid.',
                'name.required' => 'Nama folder wajib diisi.',
                'files.*.file' => 'Konten harus berupa file.',
                'files.*.mimes' => 'File harus berupa PDF, DOC, DOCX, PPT, PPTX, JPG, JPEG, PNG, ZIP, atau RAR.',
                'files.*.max' => 'Ukuran file tidak boleh melebihi 5MB.',
                'dimension.required' => 'Dimensi pembelajaran wajib dipilih.',
                'dimension.in' => 'Dimensi pembelajaran harus memiliki ID 2 atau 3.',
                'dimension_options.required' => 'Pilih setidaknya satu opsi untuk dimensi pembelajaran.',
                'dimension_options.*.required' => 'Opsi gaya belajar wajib dipilih.',
                'dimension_options.*.exists' => 'Opsi gaya belajar yang dipilih tidak valid.',
            ]);

            // Fetch the subtopic
            $subtopic = Subtopic::findOrFail($validated['subtopic_id']);

            // Create folder and files within a transaction
            $folder = DB::transaction(function () use ($validated, $request) {
                // Create the folder
                $folder = LomFolder::create([
                    'subtopic_id' => $validated['subtopic_id'],
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'learning_style_option_id' => $validated['dimension_options'][0],
                    'created_at' => now(),
                ]);

                // Handle multiple file uploads
                if ($request->hasFile('files')) {
                    Log::info('Files received for upload: ' . count($request->file('files')));
                    foreach ($request->file('files') as $index => $file) {
                        try {
                            $path = $file->store('lom_folder_files', 'public');
                            
                            // PERBAIKAN: Menambahkan learning_style_option_id
                            $lomFile = LomFile::create([
                                'name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'subtopic_id' => $validated['subtopic_id'],
                                'learning_style_option_id' => $validated['dimension_options'][0], // Ditambahkan
                            ]);

                            // Link file to folder via pivot table
                            LomFileSave::create([
                                'folder_id' => $folder->id,
                                'file_id' => $lomFile->id,
                            ]);

                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }
                } else {
                    Log::info('No files received for upload');
                }

                return $folder;
            });

            // Redirect to the topic show page or folder index
            $topic = $subtopic->topic;
            $course_id = $topic->course_id;
            $topic_id = $topic->id;

            return redirect()->route('topics.show', [$course_id, $topic_id])
                ->with('success', 'Folder berhasil disimpan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan folder: ' . $e->getMessage()])->withInput();
        }
    }
}