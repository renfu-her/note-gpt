<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NoteFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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