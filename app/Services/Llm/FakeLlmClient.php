<?php

namespace App\Services\Llm;

class FakeLlmClient implements LlmClient
{
    /** @return array{score: int, reasons: list<string>} */
    public function judge(
        string $jobTitle,
        string $jobDescription,
        string $resumeTitle,
        string $resumeBody,
        int $retrievalScore,
        array $matchedSkills,
    ): array {
        $skillSummary = $matchedSkills === []
            ? 'No direct skills from the job description were found.'
            : 'Relevant evidence: '.implode(', ', array_slice($matchedSkills, 0, 4)).'.';

        return [
            'score' => $retrievalScore,
            'reasons' => [
                $skillSummary,
                sprintf('The resume has a %d%% lexical and skills match to the role.', $retrievalScore),
                'This local demo judge is deterministic; connect Gemini for a language-model assessment.',
            ],
        ];
    }
}
