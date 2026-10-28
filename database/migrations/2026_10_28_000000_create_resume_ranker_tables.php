<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->timestamps();
        });

        Schema::create('candidate_resumes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->longText('body');
            $table->timestamps();
        });

        Schema::create('rank_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_opening_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('rank_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rank_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_resume_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->json('reasons');
            $table->json('matched_skills');
            $table->timestamps();
            $table->unique(['rank_run_id', 'candidate_resume_id']);
            $table->index(['rank_run_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rank_results');
        Schema::dropIfExists('rank_runs');
        Schema::dropIfExists('candidate_resumes');
        Schema::dropIfExists('job_openings');
    }
};
