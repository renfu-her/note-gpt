<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $member = Member::where('email', $request->email)->first();

        if (! $member || ! Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'email' => ['登入資訊不正確'],
            ]);
        }

        return response()->json([
            'token' => $member->createToken('api-token')->plainTextToken,
            'member' => $member,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->user()->currentAccessToken()->delete();
        return response()->json(['message' => '已登出']);
    }
} 