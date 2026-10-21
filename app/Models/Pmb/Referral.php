<?php

namespace App\Models\Pmb;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    protected $table = 'pmb_referrals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'referral_id');
    }

    public function sppRates(): HasMany
    {
        return $this->hasMany(ReferralSppRate::class, 'referral_id');
    }
}
