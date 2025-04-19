<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoteFolder extends Model
{
    protected $fillable = [
        'member_id',
        'parent_id',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(NoteFolder::class, 'parent_id')->orderBy('sort_order');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'folder_id');
    }
}
