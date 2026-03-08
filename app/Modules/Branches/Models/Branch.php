<?php

namespace App\Modules\Branches\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Users\Models\User;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'country',
        'state',
        'city',
        'zip',
        'address_line1',
        'address_line2',
        'lat',
        'lng',
        'legal_name',
        'tax_id',
        'tax_regime',
        'invoice_email',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['relation_type', 'is_primary']);
    }

    public function primaryBranchUsers()
    {
        return $this->hasMany(User::class, 'primary_branch_id');
    }
}
