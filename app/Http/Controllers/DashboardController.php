<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QuizResult;
use App\Models\Favorite;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $totalQuiz = QuizResult::where('user_id', $user->id)->count();
        $bestScore = QuizResult::where('user_id', $user->id)->max('score') ?? 0;
        $totalFav  = Favorite::where('user_id', $user->id)->count();
        $lastQuiz  = QuizResult::where('user_id', $user->id)->latest()->first();

        $totalUsers   = $user->isAdmin() ? User::count() : null;
        $totalResults = $user->isAdmin() ? QuizResult::count() : null;

        return view('dashboard', compact(
            'user', 'totalQuiz', 'bestScore', 'totalFav', 'lastQuiz',
            'totalUsers', 'totalResults'
        ));
    }
}