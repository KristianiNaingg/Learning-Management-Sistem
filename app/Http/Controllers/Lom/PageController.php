<?php

namespace App\Http\Controllers\Lom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LomPage;
use Illuminate\Support\Facades\Auth;

use App\Models\Subtopic;
use App\Models\LearningDimension;
use App\Models\LearningStyleOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class PageController extends Controller
{
  /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subtopicId = $request->query('sub_topic_id');
        $selectedSubtopic = $subtopicId ? Subtopic::findOrFail($subtopicId) : null;
        $learningDimensions = LearningDimension::with('options')->where('id', 2)->get();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.pages.create', compact('selectedSubtopic', 'learningDimensions', 'subtopics', 'subtopicId', 'menu'));
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
                'content' => 'required|string',
                'dimension' => 'nullable|exists:learning_dimensions,id',
                'dimension_options' => 'nullable|exists:learning_style_options,id',
            ], [
                'subtopic_id.required' => 'Subtopik wajib dipilih.',
                'subtopic_id.exists' => 'Subtopik yang dipilih tidak valid.',
                'name.required' => 'Nama page wajib diisi.',
                'name.max' => 'Nama page tidak boleh lebih dari 255 karakter.',
                'content.required' => 'Konten page wajib diisi.',
                'dimension.exists' => 'Dimensi pembelajaran tidak valid.',
                'dimension_options.exists' => 'Opsi gaya belajar tidak valid.',
            ]);

            // Validasi tambahan: jika dimension dicentang, dimension_options wajib diisi
            if ($request->has('dimension') && $request->dimension == 3 && empty($request->dimension_options)) {
                return redirect()->back()
                    ->withErrors(['dimension_options' => 'Pilih opsi gaya belajar untuk dimensi ini.'])
                    ->withInput();
            }

            // Pastikan name dan content tidak null atau kosong
            if (empty($validated['name'])) {
                return redirect()->back()
                    ->withErrors(['name' => 'Nama page tidak boleh kosong.'])
                    ->withInput();
            }

            if (empty($validated['content'])) {
                return redirect()->back()
                    ->withErrors(['content' => 'Konten page tidak boleh kosong.'])
                    ->withInput();
            }

            // Buat page
            LomPage::create([
                'subtopic_id' => $validated['subtopic_id'],
                'name' => trim($validated['name']),
                'description' => !empty($validated['description']) ? trim($validated['description']) : null,
                'content' => trim($validated['content']),
                'learning_style_option_id' => $request->dimension_options,
            ]);

            // Redirect ke halaman topic
            $subtopic = Subtopic::findOrFail($validated['subtopic_id']);
            return redirect()->route('topics.show', [
                $subtopic->topic->course_id,
                $subtopic->topic_id
            ])->with('success', 'Page berhasil dibuat.');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create page: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal membuat page: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
       
    {
       try {
        $page = LomPage::with(['subtopic.topic.course', 'learningStyleOption'])->findOrFail($id);

        $viewData = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'lom.pages.showpage',
            'page' => $page,
            'subtopic' => $page->subtopic,
            'topic' => $page->subtopic->topic,
            'course' => $page->subtopic->topic->course,
        ];

        return view('layouts.v_template', $viewData);

    } catch (\Exception $e) {
        Log::error('Failed to display page', [
            'page_id' => $id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage(),
        ]);

        return redirect()->back()->withErrors(['error' => 'Gagal menampilkan page.']);
    }
}
}
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $page = LomPage::findOrFail($id);
        $learningDimensions = LearningDimension::with('options')->where('id', 2)->get();
        $subtopics = Subtopic::all();
        $menu = 'menu.v_menu_admin';

        return view('lom.pages.edit', compact('page', 'learningDimensions', 'subtopics', 'menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $page = LomPage::findOrFail($id);

            // Validasi data
            $validated = $request->validate([
                'subtopic_id' => 'required|exists:subtopics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'content' => 'required|string',
                'dimension' => 'nullable|exists:learning_dimensions,id',
                'dimension_options' => 'nullable|exists:learning_style_options,id',
            ], [
                'subtopic_id.required' => 'Subtopik wajib dipilih.',
                'subtopic_id.exists' => 'Subtopik yang dipilih tidak valid.',
                'name.required' => 'Nama page wajib diisi.',
                'name.max' => 'Nama page tidak boleh lebih dari 255 karakter.',
                'content.required' => 'Konten page wajib diisi.',
                'dimension.exists' => 'Dimensi pembelajaran tidak valid.',
                'dimension_options.exists' => 'Opsi gaya belajar tidak valid.',
            ]);

            // Validasi tambahan: jika dimension dicentang, dimension_options wajib diisi
            if ($request->has('dimension') && $request->dimension == 3 && empty($request->dimension_options)) {
                return redirect()->back()
                    ->withErrors(['dimension_options' => 'Pilih opsi gaya belajar untuk dimensi ini.'])
                    ->withInput();
            }

            // Update page
            $page->update([
                'subtopic_id' => $validated['subtopic_id'],
                'name' => trim($validated['name']),
                'description' => !empty($validated['description']) ? trim($validated['description']) : null,
                'content' => trim($validated['content']),
                'learning_style_option_id' => $request->dimension_options,
            ]);

            // Redirect ke halaman topic
            $subtopic = Subtopic::findOrFail($validated['subtopic_id']);
            return redirect()->route('topics.show', [
                $subtopic->topic->course_id,
                $subtopic->topic_id
            ])->with('success', 'Page berhasil diperbarui.');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update page: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui page: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $page = LomPage::findOrFail($id);
            $subtopicId = $page->subtopic_id;
            
            $page->delete();

            $subtopic = Subtopic::findOrFail($subtopicId);
            return redirect()->route('topics.show', [
                $subtopic->topic->course_id,
                $subtopic->topic_id
            ])->with('success', 'Page berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete page: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus page: ' . $e->getMessage()]);
        }
    }
}
