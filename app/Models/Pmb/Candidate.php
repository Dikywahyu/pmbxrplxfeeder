<?php

namespace App\Models\Pmb;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidate extends Model
{
    protected $table = 'pmb_candidates';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'is_ktp_verified' => 'boolean',
            'penerima_kps' => 'boolean',
            'terdaftar_dtks' => 'boolean',
            'desil' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'candidate_id');
    }

    public function claim(): HasOne
    {
        return $this->hasOne(CandidateClaim::class, 'candidate_id');
    }

    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(
            CandidateDocument::class,
            Registration::class,
            'candidate_id',
            'registration_id'
        );
    }
}
