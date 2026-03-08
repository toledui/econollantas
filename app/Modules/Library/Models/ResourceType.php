<?php

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
    protected $fillable = ['name', 'description', 'active'];

    public function resources()
    {
        return $this->hasMany(LibraryResource::class);
    }
}
