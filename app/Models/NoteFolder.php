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

    protected $appends = ['full_name', 'arrow_path'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getArrowPathAttribute(): string
    {
        $ancestors = collect();
        $current = $this;
        
        while ($current) {
            $ancestors->push($current->name);
            $current = $current->parent;
        }
        
        return $ancestors->reverse()->join(' -> ');
    }

    public function getFullNameAttribute(): string
    {
        $level = 0;
        $current = $this;
        while ($current->parent) {
            $level++;
            $current = $current->parent;
        }
        $padding = $level * 20; // 20px per level
        return "<span style='padding-left: {$padding}px'>{$this->name}</span>";
    }

    public function getIndentedNameAttribute(): string
    {
        $ancestors = collect();
        $current = $this;
        
        while ($current->parent) {
            $ancestors->push($current->parent);
            $current = $current->parent;
        }
        
        $path = $ancestors->reverse()->map(fn ($folder) => $folder->name)->push($this->name);
        return $path->join(' / ');
    }

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
