<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodPathSchedule extends Model
{
    protected $table = 'pmb_period_path_schedules';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(Path::class, 'path_id');
    }
}
