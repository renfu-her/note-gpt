<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Response;

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
            return response()->json([
                'message' => '帳號或密碼錯誤',
                'error' => 'invalid_credentials'
            ], 401);
        }

        // 刪除舊的 token
        $member->tokens()->delete();

        // 創建新的 token，並包含會員資料
        $token = $member->createToken('auth-token', ['member_id' => $member->id])->plainTextToken;

        return response()->json([
            'token' => $token,
            'member' => $member,
            'message' => '登入成功，Token 永久有效直到登出'
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
                'message' => '已刷新 Token，永久有效直到登出'
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

    public function register(Request $request)
    {
        try {
            // 先檢查 email 是否已存在
            $existingMember = Member::where('email', $request->email)->first();
            if ($existingMember) {
                return response()->json([
                    'message' => '此電子郵件已被註冊',
                    'error' => 'email_exists'
                ], Response::HTTP_CONFLICT);
            }

            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ], [
                'name.required' => '請輸入名稱',
                'name.string' => '名稱必須為文字',
                'name.max' => '名稱不能超過255個字元',
                'email.required' => '請輸入電子郵件',
                'email.email' => '請輸入有效的電子郵件',
                'password.required' => '請輸入密碼',
                'password.string' => '密碼必須為文字',
                'password.min' => '密碼至少需要6個字元'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => '參數錯誤',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $member = Member::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true
            ]);

            $token = $member->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => '註冊成功，Token 永久有效直到登出',
                'token' => $token,
                'member' => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '註冊失敗',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
 