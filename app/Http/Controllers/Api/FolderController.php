<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NoteFolder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $folders = NoteFolder::where('member_id', $request->user()->id)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with('children');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json($folders);
    }
} 