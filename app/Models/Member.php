<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Note;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'password', 'birthday', 'note', 'is_active'];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function noteFolders(): HasMany
    {
        return $this->hasMany(NoteFolder::class);
    }
}
