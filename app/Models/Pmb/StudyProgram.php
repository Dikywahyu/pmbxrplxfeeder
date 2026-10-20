<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    protected $table = 'pmb_study_programs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function firstChoiceRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'prodi_id_1');
    }

    public function secondChoiceRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'prodi_id_2');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'program_id');
    }
}
