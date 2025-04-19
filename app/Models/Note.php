<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Member;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['member_id', 'title', 'content', 'is_active'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
