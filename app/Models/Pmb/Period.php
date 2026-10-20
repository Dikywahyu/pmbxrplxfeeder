<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    protected $table = 'pmb_periods';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'period_id');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'period_id');
    }
}
