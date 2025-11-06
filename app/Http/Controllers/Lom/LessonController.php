<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomLesson;

use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class LessonController extends Controller
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

        return view('lom.lessons.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi data
            $validated = $request->validate([
                'subtopic_id' => 'required|exists:subtopics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'dimension' => 'nullable|exists:learning_dimensions,id',
                'dimension_options' => 'nullable|exists:learning_style_options,id',
            ], [
                'subtopic_id.required' => 'Subtopik wajib dipilih.',
                'subtopic_id.exists' => 'Subtopik yang dipilih tidak valid.',
                'name.required' => 'Nama lesson wajib diisi.',
                'name.max' => 'Nama lesson tidak boleh lebih dari 255 karakter.',
                'dimension.exists' => 'Dimensi pembelajaran tidak valid.',
                'dimension_options.exists' => 'Opsi gaya belajar tidak valid.',
            ]);

            // Validasi tambahan: jika dimension dicentang, dimension_options wajib diisi
            if ($request->has('dimension') && $request->dimension == 3 && empty($request->dimension_options)) {
                return redirect()->back()
                    ->withErrors(['dimension_options' => 'Pilih opsi gaya belajar untuk dimensi ini.'])
                    ->withInput();
            }

            // Pastikan name tidak null atau kosong
            if (empty($validated['name'])) {
                return redirect()->back()
                    ->withErrors(['name' => 'Nama lesson tidak boleh kosong.'])
                    ->withInput();
            }

            // Buat lesson
            LomLesson::create([
                'subtopic_id' => $validated['subtopic_id'],
                'name' => trim($validated['name']),
                'description' => !empty($validated['description']) ? trim($validated['description']) : null,
                'learning_style_option_id' => $request->dimension_options,
            ]);

            // Redirect ke halaman topic
            $subtopic = Subtopic::findOrFail($validated['subtopic_id']);
            return redirect()->route('topics.show', [
                $subtopic->topic->course_id,
                $subtopic->topic_id
            ])->with('success', 'Lesson berhasil dibuat.');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create lesson: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal membuat lesson: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lesson = LomLesson::findOrFail($id);
        $learningDimensions = LearningDimension::with('options')->where('id', 3)->get();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.lessons.edit', compact('lesson', 'learningDimensions', 'subtopics', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $lesson = LomLesson::findOrFail($id);

            // Validasi data
            $validated = $request->validate([
                'subtopic_id' => 'required|exists:subtopics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'dimension' => 'nullable|exists:learning_dimensions,id',
                'dimension_options' => 'nullable|exists:learning_style_options,id',
            ], [
                'subtopic_id.required' => 'Subtopik wajib dipilih.',
                'subtopic_id.exists' => 'Subtopik yang dipilih tidak valid.',
                'name.required' => 'Nama lesson wajib diisi.',
                'name.max' => 'Nama lesson tidak boleh lebih dari 255 karakter.',
                'dimension.exists' => 'Dimensi pembelajaran tidak valid.',
                'dimension_options.exists' => 'Opsi gaya belajar tidak valid.',
            ]);

            // Validasi tambahan: jika dimension dicentang, dimension_options wajib diisi
            if ($request->has('dimension') && $request->dimension == 3 && empty($request->dimension_options)) {
                return redirect()->back()
                    ->withErrors(['dimension_options' => 'Pilih opsi gaya belajar untuk dimensi ini.'])
                    ->withInput();
            }

            // Update lesson
            $lesson->update([
                'subtopic_id' => $validated['subtopic_id'],
                'name' => trim($validated['name']),
                'description' => !empty($validated['description']) ? trim($validated['description']) : null,
                'learning_style_option_id' => $request->dimension_options,
            ]);

            // Redirect ke halaman topic
            $subtopic = Subtopic::findOrFail($validated['subtopic_id']);
            return redirect()->route('topics.show', [
                $subtopic->topic->course_id,
                $subtopic->topic_id
            ])->with('success', 'Lesson berhasil diperbarui.');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update lesson: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui lesson: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $lesson = LomLesson::findOrFail($id);
            $subtopicId = $lesson->subtopic_id;
            
            $lesson->delete();

            $subtopic = Subtopic::findOrFail($subtopicId);
            return redirect()->route('topics.show', [
                $subtopic->topic->course_id,
                $subtopic->topic_id
            ])->with('success', 'Lesson berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete lesson: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus lesson: ' . $e->getMessage()]);
        }
    }
}
