<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

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

        // 創建新的 token，並包含會員資料
        $token = $member->createToken('auth-token', ['member_id' => $member->id])->plainTextToken;

        return response()->json([
            'token' => $token,
            'member' => $member,
            'expires_in' => 3600, // token 有效期（秒）
        ]);
    }

    public function refresh(Request $request)
    {
        try {
            $bearerToken = $request->bearerToken();
            
            if (!$bearerToken) {
                return response()->json([
                    'message' => '未提供 Token',
                    'error' => 'token_missing'
                ], 401);
            }

            // 從 token 中提取 ID
            $tokenParts = explode('|', $bearerToken);
            if (count($tokenParts) !== 2) {
                return response()->json([
                    'message' => 'Token 格式錯誤',
                    'error' => 'invalid_token_format'
                ], 401);
            }

            $tokenId = $tokenParts[0];
            
            // 查找 token 記錄
            $tokenModel = PersonalAccessToken::find($tokenId);
            
            if (!$tokenModel || !$tokenModel->tokenable) {
                return response()->json([
                    'message' => 'Token 無效',
                    'error' => 'invalid_token'
                ], 401);
            }

            $member = $tokenModel->tokenable;
            
            // 刪除所有舊的 token
            $member->tokens()->delete();

            // 創建新的 token，保持相同的會員資料
            $token = $member->createToken('auth-token', ['member_id' => $member->id])->plainTextToken;

            return response()->json([
                'token' => $token,
                'member' => $member,
                'expires_in' => 3600,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token 刷新失敗',
                'error' => 'refresh_failed'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => '已成功登出']);
    }
} 