<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Path extends Model
{
    protected $table = 'pmb_paths';

    protected $guarded = [];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'path_id');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'path_id');
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(DocumentRequirement::class, 'path_id');
    }
}
