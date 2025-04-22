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
use App\Http\Resources\NoteResource;

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
                        'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $note->updated_at->format('Y-m-d H:i:s'),
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
                        'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $note->updated_at->format('Y-m-d H:i:s'),
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
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $note->updated_at->format('Y-m-d H:i:s'),
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

    public function show(Request $request, $id)
    {
        $note = $request->user()->notes()->findOrFail($id);
        
        return response()->json([
            'message' => '筆記取得成功',
            'data' => $note
        ], 200);
    }

    public function store(Request $request) 
    {

        dd($request->all());

        try {
            $validator = validator($request->all(), [
                'folder_id' => 'required|exists:note_folders,id',
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'file' => 'nullable|file|mimes:md,txt',
            ], [
                'folder_id.required' => '請選擇資料夾',
                'folder_id.exists' => '所選資料夾不存在',
                'title.required' => '請輸入標題',
                'title.string' => '標題必須為文字',
                'title.max' => '標題不能超過255個字元',
                'content.string' => '內容必須為文字',
                'file.file' => '請上傳檔案',
                'file.mimes' => '只允許上傳 md 或 txt 檔案',
            ]);

            dd($request->all(), $request->allFiles());

            // content 與 file 必須至少有一個
            if (!$request->hasFile('file') && !$request->filled('content')) {
                return response()->json([
                    'message' => '請輸入內容或上傳 md 檔案',
                    'error' => 'content_or_file_required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $data = $request->all();
            $member = Auth::guard('member')->user();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $data['content'] = file_get_contents($file->getRealPath());
            }

            if ($data['folder_id'] === '0' || $data['folder_id'] === 0) {
                $data['folder_id'] = null;
            }

            $note = $member->notes()->create($data);

            return response()->json([
                'message' => '筆記建立成功',
                'data' => $note
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => '筆記建立失敗',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $validator = validator($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'file' => 'nullable|file|mimes:md,txt',
            ], [
                'title.required' => '請輸入標題',
                'title.string' => '標題必須為文字',
                'title.max' => '標題不能超過255個字元',
                'content.string' => '內容必須為文字',
                'file.file' => '請上傳檔案',
                'file.mimes' => '只允許上傳 md 或 txt 檔案',
            ]);

            if (!$request->hasFile('file') && !$request->filled('content')) {
                return response()->json([
                    'message' => '請輸入內容或上傳 md 檔案',
                    'error' => 'content_or_file_required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $member = Auth::guard('member')->user();
            $note = $member->notes()->findOrFail($id);

            $updateData = [
                'title' => $request->title,
                'content' => $request->content
            ];

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $updateData['content'] = file_get_contents($file->getRealPath());
            }

            $note->update($updateData);

            return response()->json([
                'message' => '筆記更新成功',
                'data' => $note
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => '筆記更新失敗',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
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