<?php

use App\Models\CandidateResume;
use App\Models\JobOpening;
use App\Services\Llm\FakeLlmClient;
use App\Services\Rank\ResumeRanker;

it('uses TF-IDF and detected skills to prioritize a close technical support match', function (): void {
    $job = new JobOpening([
        'title' => 'Support Engineer',
        'description' => 'Technical support for SaaS customers. Troubleshoot Zendesk tickets, API issues, and SQL questions. Write documentation and communicate clearly.',
    ]);

    $bestFit = new CandidateResume([
        'name' => 'Morgan Reed',
        'title' => 'Technical Support Engineer',
        'body' => 'Provided technical support for SaaS customers. Troubleshot Zendesk tickets, API issues, and SQL questions. Wrote documentation and communicated clearly.',
    ]);
    $weakFit = new CandidateResume([
        'name' => 'Jamie Park',
        'title' => 'Landscape Designer',
        'body' => 'Designed residential gardens, selected plants, created site plans, and coordinated outdoor construction projects.',
    ]);

    $ranked = (new ResumeRanker(new FakeLlmClient))->rank($job, collect([$weakFit, $bestFit]));

    expect($ranked)->toHaveCount(2)
        ->and($ranked[0]['resume']->name)->toBe('Morgan Reed')
        ->and($ranked[0]['score'])->toBeGreaterThan($ranked[1]['score'])
        ->and($ranked[0]['matched_skills'])->toContain('technical support', 'zendesk', 'api', 'sql')
        ->and($ranked[0]['reasons'])->toHaveCount(3);
});
