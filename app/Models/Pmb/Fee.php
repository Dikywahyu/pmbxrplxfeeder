<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    protected $table = 'pmb_fees';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'nominal_pendaftaran' => 'decimal:2',
            'nominal_spp' => 'decimal:2',
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'program_id');
    }
}
