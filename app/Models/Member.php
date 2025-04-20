<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Note;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'phone', 'email', 'password', 'birthday', 'note', 'is_active'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'birthday' => 'datetime:Y-m-d',
    ];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function noteFolders(): HasMany
    {
        return $this->hasMany(NoteFolder::class);
    }
}
