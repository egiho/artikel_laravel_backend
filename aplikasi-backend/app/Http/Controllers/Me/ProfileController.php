<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User Data Fetched Successfully.',
            ],
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'picture' => $user->picture,
            ]
        ]);
    }
}
