<?php

namespace App\Http\Controllers\Api\Authentication;

// Import app nor providers below
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use Illuminate\Support\Facades\Auth;

// Import models below

class AuthenticationController extends Controller
{

    /**
     * Authenticate user and provide an access token upon successful login.
     *
     * @param  \App\Http\Requests\Authentication\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $input = $request->validated();

        if (!Auth::attempt($input)) {
            $errors['login'][] = 'Kombinasi email dan kata sandi tidak cocok';
            return json_error_response(422, 'Kombinasi email dan kata sandi tidak cocok', $errors);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return json_success_response(200, '', [
            'user'        => $user,
            'token'       => $token,
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    /**
     * Revoke the authenticated user's access tokens, logging them out.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return json_success_response();
    }
}
