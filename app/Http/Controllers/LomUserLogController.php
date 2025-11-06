<?php

namespace App\Http\Controllers;
use App\Models\LomUserLog;
use App\Models\User;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;



use Illuminate\Http\Request;

class LomUserLogController extends Controller
{
  
      /**
     * Menampilkan daftar mahasiswa dan total durasi belajar mereka
     */
    public function index()
    {
      $students = User::query()
        ->where('id_role', 3)
        ->leftJoin('lom_user_logs', 'users.id', '=', 'lom_user_logs.user_id')
        ->selectRaw('
            users.id,
            users.name,
            COUNT(lom_user_logs.id) as total_access,
            COALESCE(SUM(lom_user_logs.duration), 0) as total_duration
        ')
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_duration')
        ->get();

    return view('layouts.v_template', [
        'menu' => 'menu.v_menu_admin',
        'content' => 'admin.user.users_log',
        'students' => $students,
    ]);
    }
    

    public function create()
    {
        // Form untuk menambahkan log (opsional)
    }

    public function store(Request $request)
    {
        // Simpan log baru
    }

    /**
     * Menampilkan detail aktivitas LOM per mahasiswa
     */
    public function show($id)
    {
        $student = User::findOrFail($id);

        $logs = LomUserLog::where('user_id', $id)
            ->select(
                'lom_id','lom_type',
                DB::raw('COUNT(*) as total_access'),
                DB::raw('SUM(duration) as total_duration'),
                DB::raw('MAX(accessed_at) as last_access')
            )
            ->groupBy('lom_id','lom_type')
            ->orderByDesc('total_access')
            ->get();

        $data = [
            'menu' => 'menu.v_menu_admin',
            'content' => 'admin.user.user_log_detail',
            'student' => $student,
            'logs' => $logs,
        ];

        return view('layouts.v_template', $data);
    }

   public function updateDuration(Request $request)
{
    Log::info('Durasi diterima:', $request->all());


    if (!auth()->check() || auth()->user()->id_role !== 3) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'lom_id' => 'required|integer',
        'lom_type' => 'required|string',
        'duration' => 'required|numeric|min:0',
    ]);

    $userId = auth()->id();

    // Cek apakah user sudah pernah akses lom_id & lom_type ini
    $log = LomUserLog::where('user_id', $userId)
        ->where('lom_id', $validated['lom_id'])
        ->where('lom_type', $validated['lom_type'])
        ->first();

    if ($log) {
        // Jika sudah ada, tambahkan durasi baru ke durasi lama
        $log->duration += $validated['duration'];
        $log->accessed_at = now();
        $log->save();
    } else {
        // Kalau belum ada, buat record baru
        LomUserLog::create([
            'user_id' => $userId,
            'lom_id' => $validated['lom_id'],
            'lom_type' => $validated['lom_type'],
            'duration' => $validated['duration'],
            'accessed_at' => now(),
        ]);
    }

    return response()->json(['message' => 'Duration recorded successfully']);
}



    public function edit(LomUserLog $lomUserLog)
    {
        // Form edit log (opsional)
    }

    public function update(Request $request, LomUserLog $lomUserLog)
    {
        // Update log
    }

    public function destroy(LomUserLog $lomUserLog)
    {
        // Hapus log
    }
}
