<?php

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Users\Models\User;

class LibraryCategory extends Model
{
    protected $fillable = ['name', 'description', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function resources()
    {
        return $this->hasMany(LibraryResource::class);
    }
}
