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
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.restcountries.key'),
                ])
                ->get('https://api.restcountries.com/countries/v5', [
                    'limit' => 150,
                    'response_fields' => 'names.common,flag.url_png,codes.alpha_2',
                ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Gagal mengambil data dari API. Status: ' . $response->status()], 500);
            }

            $body = $response->json();
            $objects = collect($body['data']['objects'] ?? []);

            $countries = $objects
                ->filter(fn($c) => !empty($c['flag']['url_png']) && !empty($c['names']['common']))
                ->map(fn($c) => [
                    'name' => $c['names']['common'],
                    'flag' => $c['flag']['url_png'],
                ])
                ->values();

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
