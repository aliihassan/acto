<?php

namespace App\Http\Controllers;

use App\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function postScore(Request $request)
    {
        $name = $request->name;
        $user_score = $request->user_score;
        $generated_score = $request->generated_score;

        $user_won = $user_score > $generated_score;

        $validatedData = $request->validate([
            'name' => 'required|min:2',
        ]);

        Score::updateOrCreate([
            'name' => $name,
            'user_score' => $user_score,
            'generated_score' => $generated_score,
            'user_won' => $user_won
        ]);

        return response()->json([
            "success" => true
        ]);
    }
}
