<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizResult;
use App\Models\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaderboard = QuizResult::with('user')
            ->selectRaw('user_id, MAX(score) as best_score, COUNT(*) as total_attempts, MAX(correct_answers) as best_correct, MAX(total_questions) as total_q')
            ->groupBy('user_id')
            ->orderByDesc('best_score')
            ->take(20)
            ->get();

        $myHistory = QuizResult::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $myRank = null;
        foreach ($leaderboard as $idx => $row) {
            if ($row->user_id === auth()->id()) {
                $myRank = $idx + 1;
                break;
            }
        }

        return view('leaderboard.index', compact('leaderboard', 'myHistory', 'myRank'));
    }
}