<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $table = 'pmb_exam_attempts';
    protected $guarded = [];
    protected function casts(): array { return ['started_at' => 'datetime', 'submitted_at' => 'datetime', 'score' => 'decimal:2']; }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class, 'exam_id'); }
    public function registration(): BelongsTo { return $this->belongsTo(Registration::class, 'registration_id'); }
    public function answers(): HasMany { return $this->hasMany(ExamAnswer::class, 'attempt_id'); }
}
