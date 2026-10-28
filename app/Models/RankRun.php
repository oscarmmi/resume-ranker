<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RankRun extends Model
{
    protected $fillable = ['job_opening_id', 'status'];

    /** @return BelongsTo<JobOpening, $this> */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    /** @return HasMany<RankResult, $this> */
    public function results(): HasMany
    {
        return $this->hasMany(RankResult::class);
    }
}
