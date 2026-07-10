<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\QuizResult;

class QuizController extends Controller
{
    const QUIZ_COUNT = 10;

    public function index()
    {
        return view('kuis.index');
    }

    // Ambil soal kuis dari RestCountries API v5
    public function getQuestions(Request $request)
    {
        $level = $request->query('level', 'easy');
        $customCount = min((int) $request->query('count', 10), 30);
        $customTimer = min((int) $request->query('timer', 15), 30);
        $customOptions = min((int) $request->query('options', 4), 8);

        $levels = [
            'easy'   => ['count' => 10, 'timer' => 20, 'options' => 4, 'max_score' => 100, 'multiplier' => 1],
            'hard'   => ['count' => 20, 'timer' => 10, 'options' => 6, 'max_score' => 200, 'multiplier' => 1.5],
            'custom' => ['count' => $customCount, 'timer' => $customTimer, 'options' => max(4, $customOptions), 'max_score' => $customCount * 10, 'multiplier' => 1],
        ];

        $config = $levels[$level] ?? $levels['easy'];
        $quizCount = $config['count'];
        $optionCount = $config['options'];
        $maxScore = $config['max_score'];

        try {
            $countries = $this->fetchCountries();

            if ($countries->isEmpty()) {
                return response()->json(['error' => 'Gagal mengambil data negara dari API.'], 500);
            }

            if ($countries->count() < $optionCount) {
                return response()->json(['error' => 'Data negara tidak cukup.'], 500);
            }

            $questions = $countries->shuffle()->take($quizCount)->map(function ($correct) use ($countries, $optionCount) {
                $wrong = $countries
                    ->where('name', '!=', $correct['name'])
                    ->shuffle()
                    ->take($optionCount - 1)
                    ->pluck('name')
                    ->toArray();

                $options = collect(array_merge($wrong, [$correct['name']]))->shuffle()->values()->toArray();

                return [
                    'flag'    => $correct['flag'],
                    'answer'  => $correct['name'],
                    'options' => $options,
                ];
            });

            return response()->json([
                'questions'   => $questions->values(),
                'total'       => $quizCount,
                'timer'       => $config['timer'],
                'max_score'   => $maxScore,
                'level'       => $level,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Koneksi ke API gagal: ' . $e->getMessage()], 500);
        }
    }

    private function fetchCountries()
    {
        // Coba API berbayar dulu
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.restcountries.key'),
                ])
                ->get('https://api.restcountries.com/countries/v5', [
                    'limit' => 150,
                    'response_fields' => 'names.common,flag.url_png,codes.alpha_2',
                ]);

            if ($response->successful()) {
                $objects = collect($response->json('data.objects', []));
                $countries = $objects
                    ->filter(fn($c) => !empty($c['flag']['url_png']) && !empty($c['names']['common']))
                    ->map(fn($c) => [
                        'name' => $c['names']['common'],
                        'flag' => $c['flag']['url_png'],
                    ])
                    ->values();

                if ($countries->isNotEmpty()) {
                    return $countries;
                }
            }
        } catch (\Exception $e) {
            // lanjut ke fallback
        }

        // Fallback: flagcdn.com + daftar negara hardcoded
        return $this->getFallbackCountries();
    }

    private function getFallbackCountries()
    {
        $fallback = [
            ['code' => 'ID', 'name' => 'Indonesia'],
            ['code' => 'MY', 'name' => 'Malaysia'],
            ['code' => 'TH', 'name' => 'Thailand'],
            ['code' => 'PH', 'name' => 'Philippines'],
            ['code' => 'SG', 'name' => 'Singapore'],
            ['code' => 'VN', 'name' => 'Vietnam'],
            ['code' => 'MM', 'name' => 'Myanmar'],
            ['code' => 'KH', 'name' => 'Cambodia'],
            ['code' => 'LA', 'name' => 'Laos'],
            ['code' => 'BN', 'name' => 'Brunei'],
            ['code' => 'TL', 'name' => 'Timor-Leste'],
            ['code' => 'JP', 'name' => 'Japan'],
            ['code' => 'KR', 'name' => 'South Korea'],
            ['code' => 'CN', 'name' => 'China'],
            ['code' => 'IN', 'name' => 'India'],
            ['code' => 'PK', 'name' => 'Pakistan'],
            ['code' => 'BD', 'name' => 'Bangladesh'],
            ['code' => 'AU', 'name' => 'Australia'],
            ['code' => 'NZ', 'name' => 'New Zealand'],
            ['code' => 'US', 'name' => 'United States'],
            ['code' => 'CA', 'name' => 'Canada'],
            ['code' => 'GB', 'name' => 'United Kingdom'],
            ['code' => 'FR', 'name' => 'France'],
            ['code' => 'DE', 'name' => 'Germany'],
            ['code' => 'IT', 'name' => 'Italy'],
            ['code' => 'ES', 'name' => 'Spain'],
            ['code' => 'PT', 'name' => 'Portugal'],
            ['code' => 'NL', 'name' => 'Netherlands'],
            ['code' => 'BE', 'name' => 'Belgium'],
            ['code' => 'CH', 'name' => 'Switzerland'],
            ['code' => 'SE', 'name' => 'Sweden'],
            ['code' => 'NO', 'name' => 'Norway'],
            ['code' => 'DK', 'name' => 'Denmark'],
            ['code' => 'FI', 'name' => 'Finland'],
            ['code' => 'PL', 'name' => 'Poland'],
            ['code' => 'CZ', 'name' => 'Czech Republic'],
            ['code' => 'AT', 'name' => 'Austria'],
            ['code' => 'IE', 'name' => 'Ireland'],
            ['code' => 'GR', 'name' => 'Greece'],
            ['code' => 'TR', 'name' => 'Turkey'],
            ['code' => 'SA', 'name' => 'Saudi Arabia'],
            ['code' => 'AE', 'name' => 'United Arab Emirates'],
            ['code' => 'EG', 'name' => 'Egypt'],
            ['code' => 'ZA', 'name' => 'South Africa'],
            ['code' => 'NG', 'name' => 'Nigeria'],
            ['code' => 'KE', 'name' => 'Kenya'],
            ['code' => 'BR', 'name' => 'Brazil'],
            ['code' => 'AR', 'name' => 'Argentina'],
            ['code' => 'MX', 'name' => 'Mexico'],
            ['code' => 'CO', 'name' => 'Colombia'],
            ['code' => 'CL', 'name' => 'Chile'],
            ['code' => 'PE', 'name' => 'Peru'],
            ['code' => 'RU', 'name' => 'Russia'],
            ['code' => 'UA', 'name' => 'Ukraine'],
            ['code' => 'TW', 'name' => 'Taiwan'],
            ['code' => 'HK', 'name' => 'Hong Kong'],
            ['code' => 'MO', 'name' => 'Macau'],
            ['code' => 'NP', 'name' => 'Nepal'],
            ['code' => 'LK', 'name' => 'Sri Lanka'],
            ['code' => 'MN', 'name' => 'Mongolia'],
            ['code' => 'KZ', 'name' => 'Kazakhstan'],
        ];

        return collect($fallback)->map(fn($c) => [
            'name' => $c['name'],
            'flag' => "https://flagcdn.com/w160/" . strtolower($c['code']) . ".png",
        ]);
    }

    // Simpan hasil kuis
    public function saveResult(Request $request)
    {
        $request->validate([
            'score'            => 'required|integer|min:0',
            'total_questions'  => 'required|integer|min:1',
            'correct_answers'  => 'required|integer|min:0',
            'duration_seconds' => 'required|integer|min:0',
        ]);

        $result = QuizResult::create([
            'user_id'          => auth()->id(),
            'score'            => $request->score,
            'total_questions'  => $request->total_questions,
            'correct_answers'  => $request->correct_answers,
            'duration_seconds' => $request->duration_seconds,
        ]);

        return response()->json([
            'message'    => 'Hasil kuis disimpan!',
            'result_id'  => $result->id,
            'percentage' => $result->percentage,
        ]);
    }

    public function destroy($id)
    {
        $result = QuizResult::findOrFail($id);

        if (!auth()->user()->isAdmin() && $result->user_id !== auth()->id()) {
            abort(403, 'Tidak diizinkan.');
        }

        $result->delete();
        return back()->with('success', 'Data kuis dihapus.');
    }
}
