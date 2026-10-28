<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateResume extends Model
{
    protected $fillable = ['name', 'title', 'body'];

    /** @return HasMany<RankResult, $this> */
    public function rankResults(): HasMany
    {
        return $this->hasMany(RankResult::class);
    }
}
