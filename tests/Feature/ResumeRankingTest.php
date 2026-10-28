<?php

use App\Models\RankResult;
use App\Services\Llm\FakeLlmClient;
use App\Services\Llm\LlmClient;
use Illuminate\Http\Client\ConnectionException;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    config(['services.gemini.api_key' => null]);
    $this->seed();
});

it('ranks all six seeded resumes in descending score order with the strongest fit first', function (): void {
    $response = $this->post('/rank');

    $response->assertRedirect('/');

    $results = RankResult::query()
        ->with('candidateResume')
        ->join('rank_runs', 'rank_results.rank_run_id', '=', 'rank_runs.id')
        ->where('rank_runs.status', 'completed')
        ->orderBy('rank_results.id')
        ->select('rank_results.*')
        ->get();

    expect($results)->toHaveCount(6)
        ->and($results->pluck('score')->all())->toBe($results->pluck('score')->sortDesc()->values()->all())
        ->and($results->first()->candidateResume->name)->toBe('Alex Morgan')
        ->and($results->first()->matched_skills)->toContain('technical support', 'zendesk', 'api', 'sql');
});

it('binds the local fake judge when no Gemini key is configured', function (): void {
    expect(app(LlmClient::class))->toBeInstanceOf(FakeLlmClient::class);
});

it('allows a guest to open the ranker and submit a ranking run', function (): void {
    $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->where('job.title', 'Support Engineer')
        ->has('results', 0)
    );

    $this->followingRedirects()->post('/rank')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->where('hasRun', true)
        ->has('results', 6)
    );
});

it('stores retrieval-only scores when the judge times out', function (): void {
    app()->instance(LlmClient::class, new class implements LlmClient
    {
        public function judge(
            string $jobTitle,
            string $jobDescription,
            string $resumeTitle,
            string $resumeBody,
            int $retrievalScore,
            array $matchedSkills,
        ): array {
            throw new ConnectionException('Provider timed out.');
        }
    });

    $this->followingRedirects()->post('/rank')->assertOk();

    $results = RankResult::query()->with('rankRun')->get();
    expect($results)->toHaveCount(6)
        ->and($results->every(fn (RankResult $result): bool => $result->rankRun->status === 'completed'))->toBeTrue()
        ->and($results->every(fn (RankResult $result): bool => str_contains($result->reasons[2], 'retrieval-only fallback')))->toBeTrue()
        ->and($results->every(fn (RankResult $result): bool => $result->score >= 0 && $result->score <= 100))->toBeTrue();
});
