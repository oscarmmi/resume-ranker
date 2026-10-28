<?php

namespace App\Services\Rank;

use App\Models\CandidateResume;
use App\Models\JobOpening;
use App\Services\Llm\LlmClient;
use Illuminate\Support\Collection;
use Throwable;

class ResumeRanker
{
    /** @var list<string> */
    private const SKILLS = [
        'customer support', 'technical support', 'troubleshooting', 'ticketing',
        'zendesk', 'intercom', 'sql', 'api', 'saas', 'incident management',
        'documentation', 'customer success', 'python', 'linux', 'networking',
        'communication', 'knowledge base', 'bug reports', 'on-call', 'analytics',
    ];

    public function __construct(private readonly LlmClient $llm) {}

    /**
     * @param  Collection<int, CandidateResume>  $resumes
     * @return list<array{resume: CandidateResume, score: int, retrieval_score: int, reasons: list<string>, matched_skills: list<string>, llm_used: bool}>
     */
    public function rank(JobOpening $job, Collection $resumes): array
    {
        $documents = [$job->description];
        foreach ($resumes as $resume) {
            $documents[] = $resume->title.' '.$resume->body;
        }

        $tokens = array_map(fn (string $document): array => $this->tokenize($document), $documents);
        $documentFrequency = [];
        foreach ($tokens as $documentTokens) {
            foreach (array_unique($documentTokens) as $token) {
                $documentFrequency[$token] = ($documentFrequency[$token] ?? 0) + 1;
            }
        }

        $jobSkills = $this->findSkills($job->description);
        $jobVector = $this->vectorize($tokens[0], $documentFrequency, count($documents));
        $ranked = [];

        foreach ($resumes as $index => $resume) {
            $resumeSkills = $this->findSkills($resume->title.' '.$resume->body);
            $matchedSkills = array_values(array_intersect($jobSkills, $resumeSkills));
            $coverage = $jobSkills === [] ? 0.0 : count($matchedSkills) / count($jobSkills);
            $resumeVector = $this->vectorize($tokens[$index + 1], $documentFrequency, count($documents));
            $cosine = $this->cosineSimilarity($jobVector, $resumeVector);
            $retrievalScore = (int) round(max(0, min(100, ($cosine * 0.7 + $coverage * 0.3) * 100)));

            try {
                $assessment = $this->llm->judge(
                    $job->title,
                    $job->description,
                    $resume->title,
                    $resume->body,
                    $retrievalScore,
                    $matchedSkills,
                );
                $judgeScore = max(0, min(100, $assessment['score']));
                $score = (int) round($retrievalScore * 0.7 + $judgeScore * 0.3);
                $reasons = array_slice(array_values(array_filter($assessment['reasons'], 'is_string')), 0, 3);
                $llmUsed = true;
            } catch (Throwable) {
                $score = $retrievalScore;
                $reasons = $this->retrievalReasons($retrievalScore, $matchedSkills);
                $llmUsed = false;
            }

            while (count($reasons) < 3) {
                $reasons[] = 'The score is grounded in job-description term overlap and detected skills.';
            }

            $ranked[] = [
                'resume' => $resume,
                'score' => $score,
                'retrieval_score' => $retrievalScore,
                'reasons' => array_slice($reasons, 0, 3),
                'matched_skills' => $matchedSkills,
                'llm_used' => $llmUsed,
            ];
        }

        usort($ranked, fn (array $left, array $right): int => $right['score'] <=> $left['score']);

        return $ranked;
    }

    /** @return list<string> */
    private function tokenize(string $text): array
    {
        preg_match_all('/[a-z0-9+#.]+/i', mb_strtolower($text), $matches);
        $stopWords = ['a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from', 'in', 'is', 'of', 'on', 'or', 'our', 'the', 'to', 'with', 'you', 'your'];

        return array_values(array_filter($matches[0], fn (string $token): bool => strlen($token) > 1 && ! in_array($token, $stopWords, true)));
    }

    /** @param list<string> $tokens
     * @param  array<string, int>  $documentFrequency
     * @return array<string, float>
     */
    private function vectorize(array $tokens, array $documentFrequency, int $documentCount): array
    {
        $frequencies = array_count_values($tokens);
        $vector = [];
        foreach ($frequencies as $token => $frequency) {
            $termFrequency = 1 + log((float) $frequency);
            $inverseDocumentFrequency = log(($documentCount + 1) / (($documentFrequency[$token] ?? 0) + 1)) + 1;
            $vector[$token] = $termFrequency * $inverseDocumentFrequency;
        }

        return $vector;
    }

    /** @param array<string, float> $left
     * @param  array<string, float>  $right
     */
    private function cosineSimilarity(array $left, array $right): float
    {
        if ($left === [] || $right === []) {
            return 0.0;
        }

        $dotProduct = 0.0;
        foreach ($left as $token => $weight) {
            $dotProduct += $weight * ($right[$token] ?? 0.0);
        }

        $leftMagnitude = sqrt(array_sum(array_map(fn (float $weight): float => $weight ** 2, $left)));
        $rightMagnitude = sqrt(array_sum(array_map(fn (float $weight): float => $weight ** 2, $right)));

        return $leftMagnitude * $rightMagnitude === 0.0 ? 0.0 : $dotProduct / ($leftMagnitude * $rightMagnitude);
    }

    /** @return list<string> */
    private function findSkills(string $text): array
    {
        $normalized = mb_strtolower($text);

        return array_values(array_filter(self::SKILLS, fn (string $skill): bool => str_contains($normalized, $skill)));
    }

    /** @param list<string> $matchedSkills
     * @return list<string>
     */
    private function retrievalReasons(int $score, array $matchedSkills): array
    {
        $skills = $matchedSkills === [] ? 'No listed job skills were found in the resume.' : 'Matching skills: '.implode(', ', array_slice($matchedSkills, 0, 5)).'.';

        return [
            $skills,
            sprintf('TF-IDF retrieval found a %d%% match against the job description.', $score),
            'This result uses the retrieval-only fallback because the optional judge was unavailable.',
        ];
    }
}
