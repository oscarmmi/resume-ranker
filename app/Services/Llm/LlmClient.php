<?php

namespace App\Services\Llm;

interface LlmClient
{
    /**
     * @param  list<string>  $matchedSkills
     * @return array{score: int, reasons: list<string>}
     */
    public function judge(
        string $jobTitle,
        string $jobDescription,
        string $resumeTitle,
        string $resumeBody,
        int $retrievalScore,
        array $matchedSkills,
    ): array;
}
