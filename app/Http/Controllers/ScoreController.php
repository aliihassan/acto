<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function postScore(Request $request)
    {
        $name = $request->name;
        $user_score = $request->user_score;
        $generated_score = $request->generated_score;

        $validatedData = $request->validate([
            'name' => 'required|min:5',
        ]);
    }
}
