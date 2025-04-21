<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NoteFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NoteFolderController extends Controller
{
    public function index(Request $request)
    {
        $folders = NoteFolder::where('member_id', Auth::guard('member')->id())
            ->where('parent_id', null)
            ->with('children.children') // 預加載兩層子資料夾
            ->orderBy('sort_order')
            ->get()
            ->map(function ($folder) {
                return $this->formatFolder($folder);
            });

        return response()->json($folders);
    }

    public function store(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable'
            ], [
                'name.required' => '請輸入資料夾名稱',
                'name.string' => '資料夾名稱必須為文字',
                'name.max' => '資料夾名稱不能超過255個字元'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => '參數錯誤',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $parentId = null;
            
            // 處理 parent_id
            if ($request->has('parent_id')) {
                if ($request->parent_id === '0' || $request->parent_id === 0 || $request->parent_id === null) {
                    $parentId = null; // 最上層資料夾
                } else {
                    // 檢查父資料夾是否存在且屬於當前用戶
                    $parentFolder = NoteFolder::where('id', $request->parent_id)
                        ->where('member_id', Auth::guard('member')->id())
                        ->first();

                    if (!$parentFolder) {
                        return response()->json([
                            'message' => '父資料夾不存在或無權限',
                            'error' => 'parent_folder_not_found'
                        ], Response::HTTP_NOT_FOUND);
                    }
                    
                    $parentId = $parentFolder->id;
                }
            }

            // 取得最大的排序值
            $maxSort = NoteFolder::where('member_id', Auth::guard('member')->id())
                ->where('parent_id', $parentId)
                ->max('sort_order') ?? 0;

            $folder = NoteFolder::create([
                'name' => $request->name,
                'parent_id' => $parentId,
                'member_id' => Auth::guard('member')->id(),
                'sort_order' => $maxSort + 1
            ]);

            return response()->json([
                'message' => '資料夾建立成功',
                'data' => $this->formatFolder($folder)
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '資料夾建立失敗',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255'
            ], [
                'name.required' => '請輸入資料夾名稱',
                'name.string' => '資料夾名稱必須為文字',
                'name.max' => '資料夾名稱不能超過255個字元'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => '參數錯誤',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $folder = NoteFolder::where('id', $id)
                ->where('member_id', Auth::guard('member')->id())
                ->first();

            if (!$folder) {
                return response()->json([
                    'message' => '資料夾不存在或無權限',
                    'error' => 'folder_not_found'
                ], Response::HTTP_NOT_FOUND);
            }

            $folder->update([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => '資料夾更新成功',
                'data' => $this->formatFolder($folder)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '資料夾更新失敗',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        try {
            $folder = NoteFolder::where('id', $id)
                ->where('member_id', Auth::guard('member')->id())
                ->first();

            if (!$folder) {
                return response()->json([
                    'message' => '資料夾不存在或無權限',
                    'error' => 'folder_not_found'
                ], Response::HTTP_NOT_FOUND);
            }

            // 檢查是否有子資料夾
            if ($folder->children()->exists()) {
                return response()->json([
                    'message' => '資料夾內還有子資料夾，無法刪除',
                    'error' => 'has_children'
                ], Response::HTTP_BAD_REQUEST);
            }

            // 檢查是否有筆記
            if ($folder->notes()->exists()) {
                return response()->json([
                    'message' => '資料夾內還有筆記，無法刪除',
                    'error' => 'has_notes'
                ], Response::HTTP_BAD_REQUEST);
            }

            $folder->delete();

            return response()->json([
                'message' => '資料夾刪除成功'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '資料夾刪除失敗',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function formatFolder($folder)
    {
        $formattedFolder = [
            'id' => $folder->id,
            'name' => $folder->name,
            'arrow_path' => $folder->arrow_path,
            'sort_order' => $folder->sort_order,
        ];

        if ($folder->children->isNotEmpty()) {
            $formattedFolder['children'] = $folder->children->map(function ($child) {
                return $this->formatFolder($child);
            });
        }

        return $formattedFolder;
    }
} 