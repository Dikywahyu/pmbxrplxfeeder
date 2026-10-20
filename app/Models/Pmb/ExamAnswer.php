<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    protected $table = 'pmb_exam_answers';
    protected $guarded = [];
    public function attempt(): BelongsTo { return $this->belongsTo(ExamAttempt::class, 'attempt_id'); }
    public function question(): BelongsTo { return $this->belongsTo(ExamQuestion::class, 'question_id'); }
}
