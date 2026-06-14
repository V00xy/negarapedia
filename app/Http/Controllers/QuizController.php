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

    // Ambil soal kuis dari RestCountries API
    public function getQuestions()
    {
        try {
            $response = Http::timeout(10)
                ->get('https://restcountries.com/v3.1/all', [
                    'fields' => 'name,flags,cca3',
                ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Gagal mengambil data dari API.'], 500);
            }

            $countries = collect($response->json())
                ->filter(fn($c) => !empty($c['flags']['png']) && !empty($c['name']['common']))
                ->values();

            if ($countries->count() < 4) {
                return response()->json(['error' => 'Data negara tidak cukup.'], 500);
            }

            $questions = $countries->shuffle()->take(self::QUIZ_COUNT)->map(function ($correct) use ($countries) {
                $wrong = $countries
                    ->where('name.common', '!=', $correct['name']['common'])
                    ->shuffle()
                    ->take(3)
                    ->pluck('name.common')
                    ->toArray();

                $options = collect(array_merge($wrong, [$correct['name']['common']]))->shuffle()->values()->toArray();

                return [
                    'flag'    => $correct['flags']['png'],
                    'answer'  => $correct['name']['common'],
                    'options' => $options,
                ];
            });

            return response()->json([
                'questions' => $questions->values(),
                'total'     => self::QUIZ_COUNT,
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

    // Hapus hasil kuis (admin / pemilik)
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