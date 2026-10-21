<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralSppRate extends Model
{
    protected $table = 'pmb_referral_spp_rates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['nominal_spp' => 'decimal:2'];
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class, 'fee_id');
    }
}
