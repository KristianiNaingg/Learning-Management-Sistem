<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    /**
     * Menampilkan daftar peserta untuk mata kuliah tertentu.
     *
     * @param int $courseId
     * @return \Illuminate\View\View
     */
    public function index($courseId)
    {
        $course = Course::with('users')->findOrFail($courseId);

        $participants = $course->users->map(function ($user) {
            return [
                'id' => $user->pivot->id,
                'user_id' => $user->id, // Opsional, untuk keperluan lain
                'name' => $user->name,
                'participant_role' => $user->pivot->participant_role,
                'course_id' => $user->pivot->course_id,
            ];
        });

        $modalId = 'participantModal_' . $courseId;

        return view('admin.course.participant', compact('course', 'participants', 'modalId'));
    }

    /**
     * Menampilkan form untuk menambah peserta baru ke mata kuliah.
     *
     * @param int $courseId
     * @return \Illuminate\View\View
     */
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);
        $availableUsers = User::whereDoesntHave('courses', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->get();
        return view('admin.course.participant_create', compact('course', 'availableUsers'));
    }

    /**
     * Menyimpan peserta baru ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

   public function store(Request $request, $courseId)
    {

        try {
            $validatedData = $request->validate([
                'participants' => 'required|array|min:1',
                'participants.*.id' => 'required|exists:users,id',
                'participants.*.role' => 'required|in:Student,Teacher,Admin',
            ]);

            $course = Course::findOrFail($courseId);
            $participants = $validatedData['participants'];

            foreach ($participants as $participant) {
                $course->users()->syncWithoutDetaching([
                    $participant['id'] => ['participant_role' => $participant['role']]
                ]);
            }


            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta berhasil ditambahkan' // Pastikan pesan ini benar
                ]);
            }

            return redirect()->route('admin.courses.index')
                            ->with('success', 'Peserta berhasil ditambahkan');
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $e->errors()),
                ], 422);
            }
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan peserta: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Gagal menambahkan peserta: ' . $e->getMessage());
        }
    }
    // public function storeParticipant(Request $request, $courseId)
    // {
    //     $request->validate([
    //         'participants' => 'required|array|min:1',
    //         'participants.*.id' => 'required|exists:users,id',
    //         'participants.*.role' => 'required|in:Student,Teacher,Admin',
    //     ]);

    //     try {
    //         $course = Course::findOrFail($courseId);
    //         $participants = $request->input('participants');

    //         foreach ($participants as $participant) {
    //             $course->users()->syncWithoutDetaching([
    //                 $participant['id'] => ['participant_role' => $participant['role']]
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Participants added successfully'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to add participants: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    /**
     * Memperbarui peran peserta untuk mata kuliah tertentu.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
{

    try {
        $participant = CourseUser::findOrFail($id);

        $validatedData = $request->validate([
            'participant_role' => 'required|in:Student,Teacher,Admin',
        ]);

        $participant->update($validatedData);


        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Peran peserta berhasil diperbarui'
            ]);
        }

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Peran peserta berhasil diperbarui');
    } catch (ValidationException $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()),
            ], 422);
        }
        return redirect()->route('admin.courses.index')
                        ->with('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
    } catch (\Exception $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui peran peserta: ' . $e->getMessage()
            ], 500);
        }
        return redirect()->route('admin.courses.index')
                        ->with('error', 'Gagal memperbarui peran peserta: ' . $e->getMessage());
    }
}

    /**
     * Menghapus peserta dari mata kuliah.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
public function destroy(Request $request, $id)
    {

        try {
            $participant = CourseUser::findOrFail($id);
            $courseId = $participant->course_id;
            $participant->delete();


            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peserta berhasil dihapus',
                    'course_id' => $courseId
                ]);
            }

            return redirect()->route('admin.courses.index')
                            ->with('success', 'Peserta berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.courses.index')
                            ->with('error', 'Gagal menghapus peserta: ' . $e->getMessage());
        }
    }
}
