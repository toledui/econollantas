<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'icon', 'description', 'active'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
