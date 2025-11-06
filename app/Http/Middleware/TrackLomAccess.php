<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LomUserLog;
use Carbon\Carbon;

class TrackLomAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
 public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Pastikan user sudah login dan role = student
        if (!Auth::check() || Auth::user()->id_role !== 3) {
            return $response;
        }

        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return $response;
        }

        // Deteksi jenis LOM berdasarkan nama route
        $lomType = match (true) {
            str_contains($routeName, 'page') => 'page',
            str_contains($routeName, 'quiz') => 'quiz',
            str_contains($routeName, 'label') => 'label',
            str_contains($routeName, 'file') => 'file',
            str_contains($routeName, 'lesson') => 'lesson',
            str_contains($routeName, 'forum') => 'forum',
            str_contains($routeName, 'url') => 'url',
            str_contains($routeName, 'infographic') => 'infographic',
            default => null,
        };

        if (!$lomType) {
            return $response;
        }

        // Ambil ID dari route parameter (misal: page_id, quiz_id, dll)
        $routeParameters = $request->route()->parameters();
        $lomId = null;

        foreach ($routeParameters as $key => $value) {
            if (str_contains($key, 'id')) {
                $lomId = $value;
                break;
            }
        }

        if (!$lomId) {
            return $response;
        }

        // Simpan log akses
        LomUserLog::create([
            'user_id' => Auth::id(),
            'lom_id' => $lomId,
            'lom_type' => $lomType,
            'action' => 'view',
            'accessed_at' => now(),
        ]);

        return $response;
    }
}