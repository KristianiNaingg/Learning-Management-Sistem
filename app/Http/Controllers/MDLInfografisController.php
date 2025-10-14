<?php

namespace App\Http\Controllers;

use App\Models\MDLInfografis;
use App\Models\CourseSubtopik;
use App\Models\DimensionOption;
use App\Models\MDLLearningStyles;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class MDLInfografisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $subTopicId = $request->query('sub_topic_id');

        // Ambil sub-topik spesifik berdasarkan sub_topic_id
        $subTopic = CourseSubtopik::findOrFail($subTopicId);
        $dimensions = MDLLearningStyles::with('options')->get();
        $subTopics = CourseSubtopik::all();



//            $data = [
//                'menu' => 'menu.v_menu_admin',
//                'content' => 'labels.create',
//                'subTopics' => $subTopics,
//                'learningStyles' =>  $learningStyles,
//                'count_user' => DB::table('users')->count(),
//            ];

//            return view('layouts.v_template', $data);
        return view('infografis.create', compact('dimensions', 'subTopics','subTopic'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {

        try {
            // Validasi input sesuai form create.blade.php
            $validated = $request->validate([
                'sub_topic_id' => 'required|exists:mdl_course_subtopik,id',
                'file_upload' => 'required|array',
                'file_upload.*' => 'file|mimetypes:video/mp4,image/jpeg,image/png|max:5120', // Max 5MB
                'dimension_options' => 'required|array|min:1',
                'dimension_options.*' => 'exists:opsi_dimensi,id',
            ]);


            // Ambil sub_topic
            $subTopic = CourseSubtopik::findOrFail($validated['sub_topic_id']);

            $uploadedInfografis = [];

            foreach ($request->file('file_upload') as $uploadedFile) {
                // Generate unique filename
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();

                // Store file in storage/app/public/infografis
                $path = $uploadedFile->storeAs('public/infografis', $filename);

                // Simpan data ke database
                $infografisRecord = MDLInfografis::create([
                    'sub_topic_id' => $validated['sub_topic_id'],
                    'file_path' => 'infografis/' . $filename, // Path relatif
                    'created_at' => now(),
                ]);

                // Sync dimension options
                $infografisRecord->options()->sync($validated['dimension_options']);
                Log::info('Synced dimension options to mdl_infografis_style', [
                    'infografis_id' => $infografisRecord->id,
                    'dimension_options' => $validated['dimension_options']
                ]);

                $uploadedInfografis[] = $infografisRecord;
            }

            // Redirect dengan pesan sukses
            $section = $subTopic->section;
            return redirect()->route('sections.show', [$section->course_id, $section->id])
                ->with('success', count($uploadedInfografis) . ' infografis berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('Failed to store infografis: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyimpan infografis: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MDLInfografis  $mDLInfografis
     * @return \Illuminate\Http\Response
     */
    public function show(MDLInfografis $mDLInfografis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MDLInfografis  $mDLInfografis
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $infografis = MDLInfografis::with('options')->findOrFail($id);
        $subTopic = CourseSubtopik::findOrFail($infografis->sub_topic_id);
        $dimensions = MDLLearningStyles::with('options')->get();

        return view('infografis.edit', compact('infografis', 'subTopic', 'dimensions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MDLInfografis  $mDLInfografis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    try {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'sub_topic_id' => 'required|exists:mdl_course_subtopik,id',
            'file_upload' => 'nullable|file|mimes:mp4,jpg,jpeg,png|max:5120',
            'dimension_options' => 'required|array|min:1',
            'dimension_options.*' => 'exists:opsi_dimensi,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return DB::transaction(function () use ($request, $id) {
            // Find the infographic
            $infografis = MDLInfografis::findOrFail($id);

            // Handle file upload
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = 'infografis/' . $filename;

                // Delete the old file if it exists
                if ($infografis->file_path && Storage::exists('public/' . $infografis->file_path)) {
                    Storage::delete('public/' . $infografis->file_path);
                }

                // Store the new file
                $file->storeAs('public/infografis', $filename);
                $infografis->file_path = $path;
            }

            // Update other fields
            $infografis->sub_topic_id = $request->sub_topic_id;
            $infografis->save();

            // Sync dimension options
            if ($request->has('dimension_options')) {
                $infografis->options()->sync($request->dimension_options);
            }

            // Redirect with success message
            return redirect()->route('sections.show', [
                $infografis->sub_topic->section->course_id,
                $infografis->sub_topic->section->id
            ])->with('success', 'Infografis berhasil diperbarui.');
        });

    } catch (\Exception $e) {
        Log::error('Failed to update infografis: ' . $e->getMessage(), ['infografis_id' => $id]);
        return redirect()->back()
            ->withErrors(['error' => 'Gagal memperbarui infografis: ' . $e->getMessage()])
            ->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MDLInfografis  $mDLInfografis
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $infografis = MDLInfografis::findOrFail($id);

            DB::beginTransaction();

            // Hapus file dari storage
            if ($infografis->file_path) {
                Storage::delete('public/' . $infografis->file_path);
            }

            // Hapus entri pivot di mdl_infografis_style
            $infografis->options()->detach();

            // Hapus infografis dari database
            $infografis->delete();

            Log::info('Infografis deleted successfully', ['infografis_id' => $id]);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Infografis berhasil dihapus!'
                ]);
            }

            // Redirect ke halaman kursus jika subTopic null
            if (!$infografis->subTopic) {
                Log::warning('Subtopic not found for infografis', ['infografis_id' => $id]);
                return redirect()->route('courses.topics', [$infografis->subTopic->section->course_id ?? 1])
                    ->with('success', 'Infografis berhasil dihapus, tetapi subtopic tidak ditemukan.');
            }

            return redirect()->route('sections.show', [
                $infografis->subTopic->section->course_id,
                $infografis->subTopic->section->id
            ])->with('success', 'Infografis berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete infografis: ' . $e->getMessage(), ['infografis_id' => $id]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus infografis: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Gagal menghapus infografis. Silakan coba lagi.']);
        }
    }
    /**
 * Menampilkan form untuk menduplikasi page.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function duplicate($id)
    {
        try {
            Log::info('Attempting to load duplicate form for infografis', ['infografis_id' => $id]);
            $infografis = MDLInfografis::with('options')->findOrFail($id);
            Log::info('Infografis found successfully', ['infografis_id' => $id, 'infografis_data' => $infografis->toArray()]);

            if (!$infografis->sub_topic_id) {
                Log::error('Subtopic ID not found for infografis', ['infografis_id' => $id]);
                throw new \Exception('Subtopic ID tidak ditemukan untuk infografis ini.');
            }

            $subTopic = CourseSubtopik::findOrFail($infografis->sub_topic_id);
            Log::info('Subtopic found successfully', ['sub_topic_id' => $subTopic->id, 'sub_topic_title' => $subTopic->title]);

            $dimensions = MDLLearningStyles::with('options')->get();
            Log::info('Dimensions loaded', ['dimensions_count' => $dimensions->count()]);

            $subTopics = CourseSubtopik::all();
            Log::info('Subtopics loaded', ['subtopics_count' => $subTopics->count()]);

            return view('infografis.duplicate', compact('infografis', 'subTopic', 'dimensions', 'subTopics'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat form duplikasi infografis: ' . $e->getMessage(), [
                'infografis_id' => $id,
                'trace' => $e->getTraceAsString(),
                'infografis_data' => isset($infografis) ? $infografis->toArray() : null,
            ]);
            return redirect()->back()->withErrors(['error' => 'Gagal memuat form duplikasi infografis: ' . $e->getMessage()]);
        }
    }
    /**
 * Menyimpan infografis duplikat dengan validasi untuk memastikan konten unik di subtopic yang sama.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function storeDuplicate(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'file_upload' => 'required|file|mimetypes:video/mp4,image/jpeg,image/png|max:5120',
            'sub_topic_id' => 'required|exists:mdl_course_subtopik,id',
            'dimension_options' => 'required|exists:opsi_dimensi,id',
            'dimension' => 'required|exists:mdl_learning_styles,id',
            'original_infografis_id' => 'required|exists:mdl_infografis,id',
        ]);

        $subTopicIdToCheck = $validated['sub_topic_id'];
        $existingInfografis = MDLInfografis::where('sub_topic_id', $subTopicIdToCheck)
            ->where('file_path', 'like', '%' . basename($request->file('file_upload')->getClientOriginalName()))
            ->first();

        if ($existingInfografis) {
            Log::warning('Konten duplikat ditemukan di database', [
                'infografis_id' => $id,
                'sub_topic_id' => $subTopicIdToCheck,
                'file_name' => $request->file('file_upload')->getClientOriginalName(),
                'existing_infografis_id' => $existingInfografis->id,
            ]);
            throw ValidationException::withMessages([
                'file_upload' => 'File infografis dengan nama ini sudah ada untuk subtopic ini. Harap gunakan file yang berbeda.',
            ]);
        }

        $subTopic = CourseSubtopik::findOrFail($validated['sub_topic_id']);
        if (!$subTopic->section || !$subTopic->section->course_id) {
            throw new \Exception('Section atau course tidak ditemukan untuk subtopic ini.');
        }

        $infografis = DB::transaction(function () use ($request, $validated) {
            $file = $request->file('file_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/infografis', $filename);

            $infografis = MDLInfografis::create([
                'file_path' => 'infografis/' . $filename,
                'sub_topic_id' => $validated['sub_topic_id'],
            ]);

            $infografis->options()->sync([$validated['dimension_options']]);
            Log::info('Infografis duplikat berhasil dibuat dan opsi disinkronkan', [
                'infografis_id' => $infografis->id,
                'dimension_option' => $validated['dimension_options'],
                'file_path' => $infografis->file_path,
            ]);

            return $infografis;
        });

        $course_id = $subTopic->section->course_id;
        $section_id = $subTopic->section->id;

        if ($request->action === 'save_display') {
            return redirect()->route('sections.show', [$course_id, $section_id])
            ->with('success', 'Infografis berhasil diduplikasi!');
        }

        return redirect()->route('sections.show', [$course_id, $section_id])
            ->with('success', 'Infografis berhasil diduplikasi!');
    } catch (ValidationException $e) {
        Log::warning('Validasi gagal untuk infografis duplikat', [
            'infografis_id' => $id,
            'errors' => $e->errors(),
        ]);
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        Log::error('Gagal menyimpan infografis duplikat: ' . $e->getMessage(), [
            'infografis_id' => $id,
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);
        return redirect()->back()->withErrors(['error' => 'Gagal menduplikasi infografis: ' . $e->getMessage()])->withInput();
    }
}

}
