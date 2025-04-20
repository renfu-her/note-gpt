<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NoteFolder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $folders = NoteFolder::where('member_id', $request->user()->id)
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', 1)
                    ->orderBy('sort_order')
                    ->with(['children' => function ($query) {
                        $query->where('is_active', 1)
                            ->orderBy('sort_order')
                            ->with(['children' => function ($query) {
                                $query->where('is_active', 1)
                                    ->orderBy('sort_order');
                            }]);
                    }]);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($folder) {
                return $this->formatFolder($folder);
            });

        return response()->json($folders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:note_folders,id',
        ]);

        // 如果有 parent_id，確認是否屬於當前用戶
        if ($request->parent_id) {
            $parentFolder = NoteFolder::where('id', $request->parent_id)
                ->where('member_id', $request->user()->id)
                ->first();

            if (!$parentFolder) {
                return response()->json([
                    'message' => '找不到上層資料夾',
                    'error' => 'parent_folder_not_found'
                ], 404);
            }
        }

        // 取得最大的 sort_order
        $maxSort = NoteFolder::where('member_id', $request->user()->id)
            ->where('parent_id', $request->parent_id)
            ->max('sort_order') ?? 0;

        $folder = NoteFolder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'member_id' => $request->user()->id,
            'is_active' => 1,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json($folder, 201);
    }

    public function update(Request $request, $id)
    {
        $folder = NoteFolder::where('id', $id)
            ->where('member_id', $request->user()->id)
            ->first();

        if (!$folder) {
            return response()->json([
                'message' => '找不到資料夾',
                'error' => 'folder_not_found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder->update([
            'name' => $request->name,
        ]);

        return response()->json($folder);
    }

    public function destroy(Request $request, $id)
    {
        $folder = NoteFolder::where('id', $id)
            ->where('member_id', $request->user()->id)
            ->first();

        if (!$folder) {
            return response()->json([
                'message' => '找不到資料夾',
                'error' => 'folder_not_found'
            ], 404);
        }

        // 檢查是否有子資料夾
        $hasChildren = NoteFolder::where('parent_id', $id)->exists();
        if ($hasChildren) {
            return response()->json([
                'message' => '資料夾內還有子資料夾，無法刪除',
                'error' => 'has_children'
            ], 400);
        }

        // 檢查是否有筆記
        if ($folder->notes()->exists()) {
            return response()->json([
                'message' => '資料夾內還有筆記，無法刪除',
                'error' => 'has_notes'
            ], 400);
        }

        $folder->delete();

        return response()->json(null, 204);
    }

    private function formatFolder($folder)
    {
        $data = [
            'id' => $folder->id,
            'name' => $folder->name,
            'parent_id' => $folder->parent_id,
            'is_active' => $folder->is_active,
        ];

        if ($folder->children && $folder->children->count() > 0) {
            $data['children'] = $folder->children->map(function ($child) {
                return $this->formatFolder($child);
            });
        }

        return $data;
    }
} 