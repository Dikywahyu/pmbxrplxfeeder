<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestion extends Model
{
    protected $table = 'pmb_exam_questions';
    protected $guarded = [];
    protected function casts(): array { return ['options' => 'array']; }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class, 'exam_id'); }
}
