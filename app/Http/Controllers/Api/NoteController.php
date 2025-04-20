<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::where('member_id', Auth::guard('member')->id());

        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $notes = $query->with('folder')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'folder' => $note->folder ? [
                        'id' => $note->folder->id,
                        'name' => $note->folder->name,
                        'arrow_path' => $note->folder->arrow_path,
                    ] : null,
                    'created_at' => $note->created_at,
                    'updated_at' => $note->updated_at,
                ];
            });

        return response()->json($notes);
    }
} 