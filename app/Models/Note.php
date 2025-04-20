<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['member_id', 'folder_id', 'title', 'content', 'is_active'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class);
    }
}
