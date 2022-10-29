<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Requests\Auth\SignOut;
use App\Http\Requests\Auth\Request;
use GrahamCampbell\ResultType\Success;

class AuthController extends Controller
{
    public function signUp(SignUpRequest $request)
    {
        $validated = $request->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'picture' => env('AVATAR_GENERATOR_URL') . $validated['name']
        ]);

        $token = auth()->login($user);

        if (!$token)
        {
            return response()->json([
                'meta' => [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Cannot Add User.'
                ],
                'data' => [],
            ], 500);
        }

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
                'message' => 'User Created Successfully.'
            ],
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'picture' => $user->picture,
                ],
                'access token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ]
            ],
        ]);
    }

    public function signIn(SignInRequest $request)
    {
        // request body
        // email
        // password
        // hit api
        // cocokkan credential
        // kalau nggak cocok return 401 error
        // kalau cocok generate token dan kembalikan data user untuk disimpan di front end

        $token = auth()->attempt($request->validated());

        if (!$token)
        {
            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Incorrect Email Or Password',
                ],
                'data' [],
            ], 401);
        }
        $user = auth()->user();


        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
                'message' => 'Signed In Successfully',
            ],
            'data' [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'picture' => $user->picture,
                ],
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ]
            ],
        ]);
    }

    public function SignOut()
    {
        auth()->logout();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
                'message' => 'Signed Out Successfully',

            ],
            'data' => [],

        ]);
    }

    public function refresh()
    {
        $user = auth()->user();
        $token = auth()->fromUser($user);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
                'message' => 'Token Refreshed Successfully',

            ],
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'picture' => $user->picture,
                ],
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => strtotime('+' . auth()->factory()->getTTL() . ' minutes'),
                ]
            ],
        ]);
    }
}
