<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'message' => '未提供 Token',
                'error' => 'token_missing'
            ], 401);
        }

        // 從 token 中提取 ID 和 token 部分
        $tokenParts = explode('|', $bearerToken);
        if (count($tokenParts) !== 2) {
            return response()->json([
                'message' => 'Token 格式錯誤',
                'error' => 'invalid_token_format'
            ], 401);
        }

        $id = $tokenParts[0];
        $token = hash('sha256', $tokenParts[1]);
        
        // 檢查 token 是否存在
        $tokenModel = PersonalAccessToken::where('id', $id)
            ->where('token', $token)
            ->first();

        if (!$tokenModel || !$tokenModel->tokenable) {
            return response()->json([
                'message' => 'Token 無效或已過期',
                'error' => 'invalid_token'
            ], 401);
        }

        // 設置當前認證用戶
        $request->setUserResolver(function () use ($tokenModel) {
            return $tokenModel->tokenable;
        });

        return $next($request);
    }
} 