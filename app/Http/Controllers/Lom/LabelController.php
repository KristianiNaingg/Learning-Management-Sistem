<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomLabel;

use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class LabelController extends Controller
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

        return view('lom.labels.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
    }
public function store(Request $request)
{
    try {
        // Validasi data
        $validated = $request->validate([
            'subtopic_id' => 'required|exists:subtopics,id',
            'content' => 'required|string|max:255',
            'dimension' => 'nullable|exists:learning_dimensions,id',
            'dimension_options' => 'nullable|exists:learning_style_options,id',
        ], [
            'subtopic_id.required' => 'Subtopik wajib dipilih.',
            'subtopic_id.exists' => 'Subtopik yang dipilih tidak valid.',
            'content.required' => 'Konten label wajib diisi.',
            'content.max' => 'Konten label tidak boleh lebih dari 255 karakter.',
            'dimension.exists' => 'Dimensi pembelajaran tidak valid.',
            'dimension_options.exists' => 'Opsi gaya belajar tidak valid.',
        ]);

        // Validasi tambahan: jika dimension dicentang, dimension_options wajib diisi
        if ($request->has('dimension') && $request->dimension == 3 && empty($request->dimension_options)) {
            return redirect()->back()
                ->withErrors(['dimension_options' => 'Pilih opsi gaya belajar untuk dimensi ini.'])
                ->withInput();
        }

        // Pastikan content tidak null atau kosong
        if (empty($validated['content'])) {
            return redirect()->back()
                ->withErrors(['content' => 'Konten label tidak boleh kosong.'])
                ->withInput();
        }

        // Debug: lihat data yang akan disimpan
        Log::info('Creating label with data:', [
            'subtopic_id' => $validated['subtopic_id'],
            'content' => $validated['content'],
            'learning_style_option_id' => $request->dimension_options,
        ]);

        // Buat label
        LomLabel::create([
            'subtopic_id' => $validated['subtopic_id'],
            'content' => trim($validated['content']), // Gunakan trim untuk menghapus spasi kosong
            'learning_style_option_id' => $request->dimension_options,
        ]);

        // Redirect ke halaman topic
        $subtopic = Subtopic::findOrFail($validated['subtopic_id']);
        return redirect()->route('topics.show', [
            $subtopic->topic->course_id,
            $subtopic->topic_id
        ])->with('success', 'Label berhasil dibuat.');

    } catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        Log::error('Failed to create label: ' . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Gagal membuat label: ' . $e->getMessage()])->withInput();
    }
}
}
