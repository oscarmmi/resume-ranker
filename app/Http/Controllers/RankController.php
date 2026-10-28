<?php

namespace App\Http\Controllers;

use App\Models\CandidateResume;
use App\Models\JobOpening;
use App\Services\Rank\ResumeRanker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class RankController extends Controller
{
    public function store(ResumeRanker $ranker): RedirectResponse
    {
        $job = JobOpening::query()->firstOrFail();
        $resumes = CandidateResume::query()->get();
        $run = $job->rankRuns()->create(['status' => 'running']);

        try {
            $ranked = $ranker->rank($job, $resumes);

            DB::transaction(function () use ($ranked, $run): void {
                foreach ($ranked as $result) {
                    $run->results()->create([
                        'candidate_resume_id' => $result['resume']->id,
                        'score' => $result['score'],
                        'reasons' => $result['reasons'],
                        'matched_skills' => $result['matched_skills'],
                    ]);
                }

                $run->update(['status' => 'completed']);
            });
        } catch (Throwable $exception) {
            report($exception);
            $run->update(['status' => 'failed']);

            return to_route('home')->with('error', 'The ranking run could not be completed. Please try again.');
        }

        return to_route('home')->with('status', 'Ranking complete. Review the evidence behind each match.');
    }
}
