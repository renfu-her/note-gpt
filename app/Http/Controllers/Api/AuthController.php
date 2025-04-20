<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        if (!$member || !Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'email' => ['提供的認證資訊不正確。'],
            ]);
        }

        // 刪除舊的 token
        $member->tokens()->delete();

        // 創建新的 token
        $token = $member->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'member' => $member,
            'expires_in' => 3600, // token 有效期（秒）
        ]);
    }

    public function refresh(Request $request)
    {
        $member = $request->user();
        
        // 刪除舊的 token
        $member->tokens()->delete();

        // 創建新的 token
        $token = $member->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'expires_in' => 3600,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => '已成功登出']);
    }
} 