<?php

namespace App\Http\Controllers\Api;

use App\ErrorResponseTrait;
use App\Http\Controllers\Controller;
use App\JsonResponseTrait;
use App\Utils;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use JsonResponseTrait, ErrorResponseTrait;

    public function login(Request $request, $for)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => 'boolean'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard($for)->attempt(
            $credentials,
            $request->filled('remember')
        )) {
            $request->session()->regenerate();
            // redirect or ?
            if ($for === 'member') {
                return redirect()->intended(route('profil'));
            }

            return $this->successResponse(Auth::guard('admin')->user(), 'Login successful');
        }

        return $this->errorResponse('Invalid credentials', 401);
    }

    public function logout(Request $request, $for): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // if ($for === 'member') {
        //     return redirect('api/login');
        // }
    }

    public function forgotPassword(Request $request): void
    {
        $request->validate(['email' => 'required|email']);
        Utils::sendPasswordReset($request->input('email'));
    }
}
