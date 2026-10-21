<?php

namespace App\Models\Pmb;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    protected $table = 'pmb_registrations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['nominal_spp' => 'decimal:2'];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(Path::class, 'path_id');
    }

    public function program1(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'prodi_id_1');
    }

    public function program2(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'prodi_id_2');
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function referralBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referral_by');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'registration_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidateDocument::class, 'registration_id');
    }

    public function rplConversions(): HasMany
    {
        return $this->hasMany(RplConversion::class, 'registration_id');
    }
}
