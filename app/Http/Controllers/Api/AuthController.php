<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (!Auth::attempt($credentials)) return response()->json(['message' => 'Las credenciales no son válidas.'], 422);
        return response()->json(['token' => $request->user()->createToken('admin-panel')->plainTextToken, 'user' => $request->user()]);
    }
    public function logout(Request $request) { $request->user()->currentAccessToken()?->delete(); return response()->noContent(); }
    public function user(Request $request) { return response()->json(['user' => $request->user()]); }
}
