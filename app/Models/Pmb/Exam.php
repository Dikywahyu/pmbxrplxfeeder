<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    protected $table = 'pmb_exams';
    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_published' => 'boolean'];
    }

    public function questions(): HasMany { return $this->hasMany(ExamQuestion::class, 'exam_id')->orderBy('position'); }
    public function attempts(): HasMany { return $this->hasMany(ExamAttempt::class, 'exam_id'); }
    public function paths(): BelongsToMany { return $this->belongsToMany(Path::class, 'pmb_exam_path', 'exam_id', 'path_id'); }
}
