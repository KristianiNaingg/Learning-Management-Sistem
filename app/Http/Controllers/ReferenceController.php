<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Models\Topic;
use App\Models\Course;
use App\Models\Subtopic;
use App\Models\TopicReference;

class ReferenceController extends Controller
{
   /**
     * Show the form for creating a new reference.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\Response
     */
    public function create($course_id, $topic_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();
        $topic = Topic::where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        $data = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.reference.create',
            'course' => $course,
            'topic' => $topic,
        ];

        return view('layouts.v_template', $data);
    }

    /**
     * Store a new reference for a topic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $course_id
     * @param  int  $topic_id
     * @return \Illuminate\Http\JsonResponse
     */
   public function store(Request $request, $course_id, $topic_id)
    {
        try {
            $topic = Topic::where('id', $topic_id)
                ->where('course_id', $course_id)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:1000',
                'topic_id' => 'required|exists:topics,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }


            $reference = TopicReference::create([
                'topic_id' => $topic->id,
                'content' => $request->input('content', ''),
            ]);

            $savedReference = TopicReference::find($reference->id);
            Log::debug('Saved Reference Data:', $savedReference->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Referensi berhasil disimpan!',
                'id' => $reference->id,
                'content' => $reference->content,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing reference: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan referensi: ' . $e->getMessage(),
            ], 500);
        }
    }




    /**
     * Show the form for editing the specified reference.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @param  int  $reference_id
     * @return \Illuminate\View\View
     */
    public function edit($course_id, $topic_id, $reference_id)
    {
        $course = Course::where('id', $course_id)->firstOrFail();

        // Cari topic dengan validasi course_id
        $topic = Topic::where('id', $topic_id)
            ->where('course_id', $course_id)
            ->firstOrFail();

        // Cari reference dengan validasi topic_id
        $reference = TopicReference::where('id', $reference_id)
            ->where('topic_id', $topic_id)
            ->firstOrFail();



        $data = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.reference.edit',
            'course' => $course,
            'topic' => $topic,
            'reference' => $reference,
        ];

        return view('layouts.v_template', $data);
    }

    /**
     * Update the specified reference.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $course_id
     * @param  int  $topic_id
     * @param  int  $reference_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $course_id, $topic_id, $reference_id)
    {
        try {
            // Cari topic dengan validasi course_id
            $topic = Topic::where('id', $topic_id)
                ->where('course_id', $course_id)
                ->firstOrFail();

            // Cari reference dengan validasi topic_id
            $reference = TopicReference::where('id', $reference_id)
                ->where('topic_id', $topic->id)
                ->firstOrFail();

            // Validasi data dari request
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:1000',
                'visible' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Debug data yang diterima
            Log::debug('Received Update Request Data:', $request->all());

            // Update reference
            $reference->update([
                'content' => $request->input('content', ''), // Pastikan pengambilan data
                'visible' => $request->has('visible') ? $request->boolean('visible') : $reference->visible,
            ]);

            // Verifikasi data yang disimpan
            $updatedReference = TopicReference::find($reference_id);
            Log::debug('Updated Reference Data:', $updatedReference->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Referensi berhasil diperbarui!',
                'id' => $updatedReference->id,
                'content' => $updatedReference->content,
                'visible' => $updatedReference->visible,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating reference: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui referensi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified reference.
     *
     * @param  int  $course_id
     * @param  int  $topic_id
     * @param  int  $reference_id
     * @return \Illuminate\Http\JsonResponse
     */

}
