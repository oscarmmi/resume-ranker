<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $fillable = ['title', 'description'];

    /** @return HasMany<RankRun, $this> */
    public function rankRuns(): HasMany
    {
        return $this->hasMany(RankRun::class);
    }
}
