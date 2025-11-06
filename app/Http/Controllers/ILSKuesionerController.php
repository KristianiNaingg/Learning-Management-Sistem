<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningDimension;
use App\Models\UserLearningStyleOption;
use App\Models\LearningStyleOption;

class ILSKuesionerController extends Controller
{
    /**
     * Menampilkan halaman kuesioner ILS
     */
    public function showAll()
    {
        return view('ils.ils_kuesioner');
    }

    /**
     * Menyimpan hasil seluruh kuesioner ILS untuk user
     */
    public function storeAll(Request $request)
    {
        // Validasi seluruh jawaban wajib diisi
        $validatedData = $request->validate(
            array_fill_keys(array_keys($request->except('_token')), 'required')
        );

        $userId = auth()->id();

        // Daftar dimensi dan mapping pertanyaannya
        $categories = [
            'ACT/REF' => [
                'Active' => [1,2,3,4,5,6,7,8,9,10,11],
                'Reflective' => [1,2,3,4,5,6,7,8,9,10,11],
            ],
            'SNS/INT' => [
                'Sensing' => [12,13,14,15,16,17,18,19,20,21,22],
                'Intuitive' => [12,13,14,15,16,17,18,19,20,21,22],
            ],
            'VIS/VRB' => [
                'Visual' => [23,24,25,26,27,28,29,30,31,32,33],
                'Verbal' => [23,24,25,26,27,28,29,30,31,32,33],
            ],
            'SEQ/GLO' => [
                'Sequential' => [34,35,36,37,38,39,40,41,42,43,44],
                'Global' => [34,35,36,37,38,39,40,41,42,43,44],
            ],
        ];

        $scores = [];

        foreach ($categories as $dimension => $types) {
            [$a_style, $b_style] = array_keys($types);

            // Hitung jumlah jawaban A dan B
            $a_count = collect($types[$a_style])->sum(fn($q) => ($validatedData["q$q"] ?? '') === 'A' ? 1 : 0);
            $b_count = collect($types[$b_style])->sum(fn($q) => ($validatedData["q$q"] ?? '') === 'B' ? 1 : 0);

            // Tentukan gaya dominan (A atau B)
            $dominantCategory = ($a_count >= $b_count) ? 'A' : 'B';
            $scoreValue = abs($a_count - $b_count);
            $styleName = ($dominantCategory === 'A') ? $a_style : $b_style;

            // Tentukan kategori kekuatan preferensi
            $category = match (true) {
                in_array($scoreValue, [5,7]) => 'Moderate',
                in_array($scoreValue, [9,11]) => 'Strong',
                default => 'Balanced'
            };

            // Mapping nama gaya belajar ke ID di tabel learning_style_options
            $learningStyleId = match ($styleName) {
                'Visual' => 1,
                'Verbal' => 2,
                'Active' => 3,
                'Reflective' => 4,
                'Sensing' => 5,
                'Intuitive' => 6,
                'Sequential' => 7,
                'Global' => 8,
                default => null
            };

            // Simpan atau update data gaya belajar pengguna
            UserLearningStyleOption::updateOrCreate(
                [
                    'user_id' => $userId,
                    'dimension' => $dimension,
                ],
                [
                    'learning_style_option_id' => $learningStyleId,
                    'a_count' => $a_count,
                    'b_count' => $b_count,
                    'final_score' => "{$scoreValue}{$styleName}",
                    'category' => $category,
                    'description' => $styleName,
                ]
            );

            $scores[$dimension] = [
                'a_count' => $a_count,
                'b_count' => $b_count,
                'learning_style_option_id' => $learningStyleId,
                'final_score' => "{$scoreValue}{$styleName}",
                'category' => $category,
            ];
        }

        // Gabungkan hasil dominan menjadi satu kombinasi gaya belajar
        $dimensions = ['ACT/REF', 'SNS/INT', 'VIS/VRB', 'SEQ/GLO'];
        $finalStyles = [];

        foreach ($dimensions as $dimension) {
            $style = $scores[$dimension]['final_score']; // contoh: 5Active
            preg_match('/[A-Za-z]+$/', $style, $matches);
            $finalStyles[] = strtolower($matches[0]);
        }

        $learningStyleCombination = implode('_', $finalStyles); // contoh: active_sensing_visual_sequential
        session(['learning_style_combination' => 'learning_styles_combined.' . $learningStyleCombination]);

        return redirect()->route('ils.ils_score')->with('success', 'Kuesioner berhasil disimpan!');
    }

    /**
     * Menampilkan hasil skor ILS user
     */
    public function showScore()
    {
        $userId = auth()->id();
        $userLearningStyles = UserLearningStyleOption::where('user_id', $userId)->get();

        $dimensionNames = [
            'ACT/REF' => ['Active', 'Reflective'],
            'SNS/INT' => ['Sensing', 'Intuitive'],
            'VIS/VRB' => ['Visual', 'Verbal'],
            'SEQ/GLO' => ['Sequential', 'Global'],
        ];

        $scores = [];

        foreach ($userLearningStyles as $style) {
            $dominantCategory = ($style->a_count >= $style->b_count) ? 'A' : 'B';
            $scoreValue = abs($style->a_count - $style->b_count);
            $styleName = ($dominantCategory === 'A')
                ? $dimensionNames[$style->dimension][0]
                : $dimensionNames[$style->dimension][1];

            $category = match (true) {
                in_array($scoreValue, [5,7]) => 'Moderate',
                in_array($scoreValue, [9,11]) => 'Strong',
                default => 'Balanced'
            };

            $scores[$style->dimension] = [
                'a_count' => $style->a_count,
                'b_count' => $style->b_count,
                'final_score' => "{$scoreValue}{$styleName}",
                'category' => $category,
            ];
        }

        return view('ils.ils_score', compact('scores'));
    }
}
