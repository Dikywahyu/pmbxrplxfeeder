<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RplConversion extends Model
{
    protected $table = 'pmb_rpl_conversions';

    protected $guarded = [];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
