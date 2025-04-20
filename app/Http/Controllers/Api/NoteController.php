<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Models\Note;
use App\Models\NoteFolder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->segment(3) === 'folders' && $request->segment(4)) {
            // 獲取資料夾資訊
            $folder = NoteFolder::where('member_id', $request->user()->id)
                ->where('is_active', 1)
                ->where('id', $request->segment(4))
                ->first();

            if (!$folder) {
                return response()->json([
                    'message' => '找不到資料夾',
                    'error' => 'folder_not_found'
                ], 404);
            }

            // 獲取該資料夾的筆記
            $notes = Note::where('member_id', $request->user()->id)
                ->where('is_active', 1)
                ->where('folder_id', $folder->id)
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'title' => $note->title,
                        'content' => $note->content,
                        'is_active' => $note->is_active,
                        'created_at' => $note->created_at,
                        'updated_at' => $note->updated_at,
                    ];
                });

            $response = [
                'id' => $folder->id,
                'name' => $folder->name,
                'parent_id' => $folder->parent_id,
                'is_active' => $folder->is_active,
                'notes' => $notes
            ];

            return response()->json($response, $notes->isEmpty() ? 404 : 200);
        }

        // 獲取所有資料夾
        $folders = NoteFolder::where('member_id', $request->user()->id)
            ->where('is_active', 1)
            ->get();

        // 獲取所有筆記並按資料夾分組
        $result = $folders->map(function ($folder) use ($request) {
            $notes = Note::where('member_id', $request->user()->id)
                ->where('is_active', 1)
                ->where('folder_id', $folder->id)
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'title' => $note->title,
                        'content' => $note->content,
                        'is_active' => $note->is_active,
                        'created_at' => $note->created_at,
                        'updated_at' => $note->updated_at,
                    ];
                });

            return [
                'id' => $folder->id,
                'name' => $folder->name,
                'parent_id' => $folder->parent_id,
                'is_active' => $folder->is_active,
                'notes' => $notes
            ];
        })->filter(function ($folder) {
            return $folder['notes']->isNotEmpty();
        });

        // 獲取未分類的筆記（沒有資料夾的）
        $unclassifiedNotes = Note::where('member_id', $request->user()->id)
            ->where('is_active', 1)
            ->whereNull('folder_id')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'is_active' => $note->is_active,
                    'created_at' => $note->created_at,
                    'updated_at' => $note->updated_at,
                ];
            });

        // 如果有未分類的筆記，加入結果中
        if ($unclassifiedNotes->isNotEmpty()) {
            $result->push([
                'id' => null,
                'name' => '未分類',
                'parent_id' => null,
                'is_active' => 1,
                'notes' => $unclassifiedNotes
            ]);
        }

        // 如果沒有任何筆記，返回 404
        if ($result->isEmpty() && $unclassifiedNotes->isEmpty()) {
            return response()->json([
                'message' => '找不到任何筆記',
                'error' => 'notes_not_found'
            ], 404);
        }

        return response()->json($result, 200);
    }

    public function store(NoteRequest $request): JsonResponse
    {
        $member = Auth::guard('member')->user();
        
        $data = $request->validated();
        if (isset($data['folder_id']) && $data['folder_id'] === 0) {
            $data['folder_id'] = null;
        }
        
        $note = $member->notes()->create($data);

        return response()->json([
            'message' => '筆記建立成功',
            'data' => $note
        ], Response::HTTP_CREATED);
    }

    public function update(NoteRequest $request, int $id): JsonResponse
    {
        $member = Auth::guard('member')->user();
        
        $data = $request->validated();
        if (isset($data['folder_id']) && $data['folder_id'] === 0) {
            $data['folder_id'] = null;
        }
        
        $note = $member->notes()->findOrFail($id);
        $note->update($data);

        return response()->json([
            'message' => '筆記更新成功',
            'data' => $note
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $member = Auth::guard('member')->user();
        
        $note = $member->notes()->findOrFail($id);
        $note->delete();

        return response()->json([
            'message' => '筆記刪除成功'
        ]);
    }
} 