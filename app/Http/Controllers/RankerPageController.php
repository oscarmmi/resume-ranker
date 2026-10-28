<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RankerPageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $job = JobOpening::query()->first();
        $latestRun = $job?->rankRuns()
            ->where('status', 'completed')
            ->latest('id')
            ->with(['results' => fn ($query) => $query->orderByDesc('score'), 'results.candidateResume'])
            ->first();

        return Inertia::render('Welcome', [
            'job' => $job ? [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
            ] : null,
            'results' => $latestRun?->results->map(fn ($result): array => [
                'id' => $result->id,
                'score' => $result->score,
                'reasons' => $result->reasons,
                'matched_skills' => $result->matched_skills,
                'resume' => [
                    'id' => $result->candidateResume->id,
                    'name' => $result->candidateResume->name,
                    'title' => $result->candidateResume->title,
                    'body' => $result->candidateResume->body,
                ],
            ])->values()->all() ?? [],
            'hasRun' => $latestRun !== null,
            'lastRunAt' => $latestRun?->created_at?->toIso8601String(),
            'statusMessage' => $request->session()->get('status'),
            'errorMessage' => $request->session()->get('error'),
        ]);
    }
}
