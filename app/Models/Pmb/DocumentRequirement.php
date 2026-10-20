<?php

namespace App\Models\Pmb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRequirement extends Model
{
    protected $table = 'pmb_document_requirements';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_required' => 'boolean'];
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(Path::class, 'path_id');
    }

    public function candidateDocuments(): HasMany
    {
        return $this->hasMany(CandidateDocument::class, 'requirement_id');
    }
}
