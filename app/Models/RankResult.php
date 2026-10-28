<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RankResult extends Model
{
    protected $fillable = [
        'rank_run_id',
        'candidate_resume_id',
        'score',
        'reasons',
        'matched_skills',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'reasons' => 'array',
            'matched_skills' => 'array',
        ];
    }

    /** @return BelongsTo<RankRun, $this> */
    public function rankRun(): BelongsTo
    {
        return $this->belongsTo(RankRun::class);
    }

    /** @return BelongsTo<CandidateResume, $this> */
    public function candidateResume(): BelongsTo
    {
        return $this->belongsTo(CandidateResume::class);
    }
}
