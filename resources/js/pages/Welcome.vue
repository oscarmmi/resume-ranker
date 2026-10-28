<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Resume = {
    id: number;
    name: string;
    title: string;
    body: string;
};

type Result = {
    id: number;
    score: number;
    reasons: string[];
    matched_skills: string[];
    resume: Resume;
};

const props = defineProps<{
    job: { id: number; title: string; description: string } | null;
    results: Result[];
    hasRun: boolean;
    lastRunAt: string | null;
    statusMessage: string | null;
    errorMessage: string | null;
}>();

const selectedResume = ref<Resume | null>(null);
const isRanking = ref(false);
const sortedResults = computed(() => props.results);
const topScore = computed(() => sortedResults.value[0]?.score ?? 0);

function runRanking() {
    if (!props.job || isRanking.value) return;
    isRanking.value = true;
    router.post(
        '/rank',
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isRanking.value = false;
            },
        },
    );
}

function initials(name: string) {
    return name
        .split(' ')
        .map((part) => part[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

function scoreTone(score: number) {
    if (score >= 75) return 'score-high';
    if (score >= 48) return 'score-mid';
    return 'score-low';
}
</script>

<template>
    <Head title="Resume Ranker — Harbor Desk" />
    <div
        class="min-h-screen bg-[#f6f7f5] text-[#202722] selection:bg-[#c9e9d7]"
    >
        <header class="border-b border-[#e5e8e3] bg-white">
            <div
                class="mx-auto flex max-w-[1440px] items-center justify-between px-5 py-4 sm:px-8 lg:px-12"
            >
                <a
                    href="/"
                    class="flex items-center gap-3"
                    aria-label="Resume Ranker home"
                >
                    <span
                        class="grid h-10 w-10 place-items-center rounded-xl bg-[#153b2a] text-white"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            class="h-5 w-5"
                            aria-hidden="true"
                        >
                            <path
                                d="M7 4.75h7.5L19 9.25v10A1.75 1.75 0 0 1 17.25 21h-10A1.75 1.75 0 0 1 5.5 19.25v-12A2.5 2.5 0 0 1 8 4.75Z"
                                stroke="currentColor"
                                stroke-width="1.6"
                            />
                            <path
                                d="M14 5v5h5M8.5 14h7M8.5 17h5"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-bold tracking-tight"
                            >Shortlist</span
                        >
                        <span
                            class="block text-[11px] font-medium tracking-[0.13em] text-[#7c857e] uppercase"
                            >Resume intelligence</span
                        >
                    </span>
                </a>
                <div class="flex items-center gap-3">
                    <span
                        class="hidden rounded-full border border-[#e5e8e3] px-3 py-1.5 text-xs font-medium text-[#68736b] sm:inline-flex sm:items-center sm:gap-2"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-[#45a66f]"
                        ></span>
                        Fictional demo workspace
                    </span>
                    <span
                        class="grid h-9 w-9 place-items-center rounded-full bg-[#e8eee8] text-xs font-bold text-[#365744]"
                        >HD</span
                    >
                </div>
            </div>
        </header>

        <main
            class="mx-auto max-w-[1440px] px-5 py-8 sm:px-8 sm:py-10 lg:px-12"
        >
            <div
                class="mb-8 flex flex-col justify-between gap-5 md:flex-row md:items-end"
            >
                <div>
                    <div
                        class="mb-3 flex items-center gap-2 text-xs font-semibold tracking-[0.14em] text-[#688071] uppercase"
                    >
                        <span>Talent workspace</span
                        ><span class="text-[#b5beb7]">/</span
                        ><span>Open role</span>
                    </div>
                    <h1
                        class="text-3xl font-semibold tracking-[-0.04em] text-[#202722] sm:text-[40px]"
                    >
                        Find the signal in every resume.
                    </h1>
                    <p
                        class="mt-2 max-w-2xl text-sm leading-6 text-[#68736b] sm:text-base"
                    >
                        A transparent, evidence-led shortlist. Retrieval finds
                        relevant experience; an optional AI judge adds context.
                    </p>
                </div>
                <button
                    type="button"
                    :disabled="!job || isRanking"
                    class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#153b2a] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1e5139] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#153b2a] disabled:cursor-wait disabled:opacity-70"
                    @click="runRanking"
                >
                    <svg
                        v-if="isRanking"
                        class="h-4 w-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-opacity=".25"
                            stroke-width="3"
                        />
                        <path
                            d="M21 12a9 9 0 0 0-9-9"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3ZM19 15l1.1 2.9L23 19l-2.9 1.1L19 23l-1.1-2.9L15 19l2.9-1.1L19 15Z"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linejoin="round"
                        />
                    </svg>
                    {{
                        isRanking
                            ? 'Ranking resumes…'
                            : hasRun
                              ? 'Run ranking again'
                              : 'Rank 6 candidates'
                    }}
                </button>
            </div>

            <div
                v-if="statusMessage"
                role="status"
                class="mb-5 rounded-xl border border-[#c7e5d1] bg-[#eff9f2] px-4 py-3 text-sm text-[#285e3b]"
            >
                {{ statusMessage }}
            </div>
            <div
                v-if="errorMessage"
                role="alert"
                class="mb-5 rounded-xl border border-[#f0c9bd] bg-[#fff5f1] px-4 py-3 text-sm text-[#8f442b]"
            >
                {{ errorMessage }}
            </div>

            <section
                v-if="job"
                class="grid gap-5 xl:grid-cols-[minmax(0,1.05fr)_minmax(400px,.95fr)]"
            >
                <article
                    class="overflow-hidden rounded-2xl border border-[#e2e7e1] bg-white shadow-[0_2px_10px_rgba(30,52,39,.025)]"
                >
                    <div
                        class="flex items-start justify-between gap-4 border-b border-[#edf0ec] px-6 py-5 sm:px-7"
                    >
                        <div>
                            <div
                                class="mb-2 flex items-center gap-2 text-[11px] font-bold tracking-[0.14em] text-[#7b867e] uppercase"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-[#62a878]"
                                ></span>
                                Active position
                            </div>
                            <h2 class="text-xl font-semibold tracking-tight">
                                {{ job.title }}
                            </h2>
                            <p class="mt-1 text-sm text-[#768078]">
                                Harbor Desk
                                <span class="px-1 text-[#bdc4bd]">·</span>
                                Customer Experience
                            </p>
                        </div>
                        <span
                            class="rounded-lg border border-[#e7ebe6] bg-[#fafbf9] px-2.5 py-1.5 text-xs font-semibold text-[#667169]"
                            >Full-time</span
                        >
                    </div>
                    <div class="px-6 py-5 sm:px-7">
                        <div class="mb-3 flex items-center justify-between">
                            <h3
                                class="text-xs font-bold tracking-[0.12em] text-[#7b867e] uppercase"
                            >
                                Role overview
                            </h3>
                            <span class="text-xs text-[#98a099]"
                                >Fictional company</span
                            >
                        </div>
                        <p
                            class="text-[13px] leading-[1.85] whitespace-pre-line text-[#5e6961]"
                        >
                            {{ job.description }}
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <span
                                v-for="skill in [
                                    'Technical support',
                                    'SaaS',
                                    'API troubleshooting',
                                    'Customer communication',
                                    'Documentation',
                                ]"
                                :key="skill"
                                class="rounded-lg bg-[#f2f5f1] px-2.5 py-1.5 text-[11px] font-medium text-[#597061]"
                                >{{ skill }}</span
                            >
                        </div>
                    </div>
                    <div
                        class="grid grid-cols-3 border-t border-[#edf0ec] bg-[#fbfcfa]"
                    >
                        <div
                            class="border-r border-[#edf0ec] px-5 py-4 sm:px-7"
                        >
                            <p class="text-[11px] font-medium text-[#879189]">
                                Candidates
                            </p>
                            <p class="mt-1 text-lg font-semibold">
                                06
                                <span class="text-xs font-normal text-[#929a93]"
                                    >resumes</span
                                >
                            </p>
                        </div>
                        <div
                            class="border-r border-[#edf0ec] px-5 py-4 sm:px-7"
                        >
                            <p class="text-[11px] font-medium text-[#879189]">
                                Scoring
                            </p>
                            <p class="mt-1 text-sm font-semibold">
                                TF-IDF + judge
                            </p>
                        </div>
                        <div class="px-5 py-4 sm:px-7">
                            <p class="text-[11px] font-medium text-[#879189]">
                                Last run
                            </p>
                            <p class="mt-1 text-sm font-semibold">
                                {{
                                    lastRunAt
                                        ? new Date(
                                              lastRunAt,
                                          ).toLocaleTimeString([], {
                                              hour: 'numeric',
                                              minute: '2-digit',
                                          })
                                        : 'Not run yet'
                                }}
                            </p>
                        </div>
                    </div>
                </article>

                <aside
                    class="flex flex-col rounded-2xl border border-[#e2e7e1] bg-white p-6 shadow-[0_2px_10px_rgba(30,52,39,.025)] sm:p-7"
                >
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <div
                                class="mb-2 text-[11px] font-bold tracking-[0.14em] text-[#7b867e] uppercase"
                            >
                                Ranking methodology
                            </div>
                            <h2 class="text-xl font-semibold tracking-tight">
                                A clear path to the shortlist.
                            </h2>
                        </div>
                        <span
                            class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#edf5ef] text-[#427455]"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M4 19V5m0 14h16M8 15l3-4 3 2 5-7"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>
                    </div>
                    <div class="relative space-y-0">
                        <div
                            class="absolute top-5 bottom-5 left-[15px] w-px bg-[#e5ebe5]"
                        ></div>
                        <div class="relative flex gap-4 pb-5">
                            <span
                                class="z-10 grid h-[31px] w-[31px] shrink-0 place-items-center rounded-full border border-[#dce8dd] bg-white text-xs font-bold text-[#50815e]"
                                >01</span
                            >
                            <div class="pt-0.5">
                                <h3 class="text-sm font-semibold">
                                    Job + resume text
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-[#7a857d]"
                                >
                                    The role description is compared with each
                                    fictional candidate profile.
                                </p>
                            </div>
                        </div>
                        <div class="relative flex gap-4 pb-5">
                            <span
                                class="z-10 grid h-[31px] w-[31px] shrink-0 place-items-center rounded-full border border-[#dce8dd] bg-white text-xs font-bold text-[#50815e]"
                                >02</span
                            >
                            <div class="pt-0.5">
                                <h3 class="text-sm font-semibold">
                                    TF-IDF retrieval
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-[#7a857d]"
                                >
                                    Term relevance and detected job skills
                                    establish a measurable baseline.
                                </p>
                            </div>
                        </div>
                        <div class="relative flex gap-4">
                            <span
                                class="z-10 grid h-[31px] w-[31px] shrink-0 place-items-center rounded-full border border-[#dce8dd] bg-white text-xs font-bold text-[#50815e]"
                                >03</span
                            >
                            <div class="pt-0.5">
                                <h3 class="text-sm font-semibold">
                                    Optional LLM judge
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-[#7a857d]"
                                >
                                    Gemini can weigh context and explain
                                    evidence. Without a key—or on
                                    timeout—retrieval still ranks the
                                    candidates.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto border-t border-[#edf0ec] pt-5">
                        <div
                            class="flex items-center gap-2 text-xs font-semibold text-[#526a59]"
                        >
                            <svg
                                class="h-4 w-4 text-[#57906a]"
                                viewBox="0 0 20 20"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="m5 10 3.2 3.2L15.5 6"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <circle
                                    cx="10"
                                    cy="10"
                                    r="8"
                                    stroke="currentColor"
                                    stroke-width="1.4"
                                />
                            </svg>
                            Recommendations support review — they don't make
                            hiring decisions.
                        </div>
                    </div>
                </aside>

                <section
                    class="overflow-hidden rounded-2xl border border-[#e2e7e1] bg-white shadow-[0_2px_10px_rgba(30,52,39,.025)] xl:col-span-2"
                    aria-labelledby="ranked-candidates-title"
                >
                    <div
                        class="flex flex-col justify-between gap-3 border-b border-[#edf0ec] px-6 py-5 sm:flex-row sm:items-center sm:px-7"
                    >
                        <div>
                            <div class="flex items-center gap-3">
                                <h2
                                    id="ranked-candidates-title"
                                    class="text-lg font-semibold tracking-tight"
                                >
                                    Ranked candidates
                                </h2>
                                <span
                                    class="rounded-full bg-[#eff4ef] px-2.5 py-1 text-[11px] font-semibold text-[#5a7561]"
                                    >{{
                                        hasRun ? results.length : 6
                                    }}
                                    profiles</span
                                >
                            </div>
                            <p class="mt-1 text-xs text-[#818b83]">
                                Sorted by overall match score
                                <span v-if="hasRun"
                                    >· highest match {{ topScore }}%</span
                                >
                            </p>
                        </div>
                        <div
                            class="flex items-center gap-2 text-[11px] text-[#7b867e]"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-[#67a879]"
                            ></span
                            >Click a candidate to inspect their resume
                        </div>
                    </div>

                    <div
                        v-if="!hasRun"
                        class="flex min-h-56 flex-col items-center justify-center px-6 py-12 text-center"
                    >
                        <span
                            class="grid h-12 w-12 place-items-center rounded-2xl bg-[#f1f5f1] text-[#67816e]"
                            ><svg
                                class="h-6 w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M7 4.75h7.5L19 9.25v10A1.75 1.75 0 0 1 17.25 21h-10A1.75 1.75 0 0 1 5.5 19.25v-12A2.5 2.5 0 0 1 8 4.75Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    d="M14 5v5h5M8.5 14h7M8.5 17h4"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                /></svg
                        ></span>
                        <h3 class="mt-4 text-sm font-semibold">
                            Your shortlist is ready to build
                        </h3>
                        <p
                            class="mt-1 max-w-sm text-xs leading-5 text-[#818b83]"
                        >
                            Run the ranker to compare six fictional resumes and
                            see the evidence behind every score.
                        </p>
                        <button
                            type="button"
                            class="mt-5 rounded-lg border border-[#dfe6df] px-4 py-2 text-xs font-semibold text-[#355b42] transition hover:bg-[#f5f8f4]"
                            :disabled="isRanking"
                            @click="runRanking"
                        >
                            {{ isRanking ? 'Ranking…' : 'Generate shortlist' }}
                        </button>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table
                            class="w-full min-w-[760px] border-collapse text-left"
                        >
                            <thead>
                                <tr
                                    class="border-b border-[#edf0ec] bg-[#fafbf9] text-[10px] font-bold tracking-[0.12em] text-[#889189] uppercase"
                                >
                                    <th class="px-6 py-3.5 sm:px-7">
                                        Candidate
                                    </th>
                                    <th class="px-4 py-3.5">Match</th>
                                    <th class="px-4 py-3.5">Relevant skills</th>
                                    <th class="px-6 py-3.5 sm:px-7">
                                        Evidence summary
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(result, index) in sortedResults"
                                    :key="result.id"
                                    class="group cursor-pointer border-b border-[#f0f2ef] transition last:border-b-0 focus-within:bg-[#fafcf9] hover:bg-[#fafcf9]"
                                    tabindex="0"
                                    @click="selectedResume = result.resume"
                                    @keydown.enter="
                                        selectedResume = result.resume
                                    "
                                    @keydown.space.prevent="
                                        selectedResume = result.resume
                                    "
                                >
                                    <td class="px-6 py-4 sm:px-7">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="relative grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#edf2ed] text-xs font-bold text-[#54715d]"
                                            >
                                                {{ initials(result.resume.name)
                                                }}<span
                                                    v-if="index === 0"
                                                    class="absolute -right-0.5 -bottom-0.5 grid h-4 w-4 place-items-center rounded-full border-2 border-white bg-[#d6ad5c] text-[8px] text-white"
                                                    >★</span
                                                >
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-semibold text-[#28332b]"
                                                >
                                                    {{ result.resume.name }}
                                                </div>
                                                <div
                                                    class="mt-0.5 text-xs text-[#7d877f]"
                                                >
                                                    {{ result.resume.title }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <span
                                                class="min-w-10 text-sm font-bold"
                                                :class="scoreTone(result.score)"
                                                >{{ result.score }}%</span
                                            ><span
                                                class="h-1.5 w-16 overflow-hidden rounded-full bg-[#edf0ec]"
                                                ><span
                                                    class="block h-full rounded-full bg-current"
                                                    :class="
                                                        scoreTone(result.score)
                                                    "
                                                    :style="{
                                                        width: `${result.score}%`,
                                                    }"
                                                ></span
                                            ></span>
                                        </div>
                                        <div
                                            class="mt-1 text-[10px] text-[#939b94]"
                                        >
                                            overall match
                                        </div>
                                    </td>
                                    <td class="max-w-[220px] px-4 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            <span
                                                v-for="skill in result.matched_skills.slice(
                                                    0,
                                                    3,
                                                )"
                                                :key="skill"
                                                class="rounded-md bg-[#eff5ef] px-2 py-1 text-[10px] font-medium text-[#52735b]"
                                                >{{ skill }}</span
                                            ><span
                                                v-if="
                                                    !result.matched_skills
                                                        .length
                                                "
                                                class="text-xs text-[#9ba29b]"
                                                >No direct skills detected</span
                                            ><span
                                                v-if="
                                                    result.matched_skills
                                                        .length > 3
                                                "
                                                class="self-center text-[10px] text-[#879189]"
                                                >+{{
                                                    result.matched_skills
                                                        .length - 3
                                                }}</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 sm:px-7">
                                        <p
                                            class="line-clamp-2 max-w-[410px] text-xs leading-5 text-[#6c776f]"
                                        >
                                            {{ result.reasons[0] }}
                                        </p>
                                        <span
                                            class="mt-1 inline-flex items-center gap-1 text-[10px] font-semibold text-[#64816b] opacity-0 transition group-hover:opacity-100 group-focus:opacity-100"
                                            >Read resume
                                            <span aria-hidden="true"
                                                >→</span
                                            ></span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-if="hasRun"
                        class="flex items-center justify-between border-t border-[#edf0ec] bg-[#fbfcfa] px-6 py-3 text-[10px] text-[#89928a] sm:px-7"
                    >
                        <span
                            >Scores combine lexical retrieval with optional
                            judge context.</span
                        ><span class="font-medium"
                            >Demo data · no real candidate information</span
                        >
                    </div>
                </section>
            </section>
            <div
                v-else
                class="rounded-2xl border border-[#e2e7e1] bg-white p-8 text-sm text-[#68736b]"
            >
                No seeded job found. Run
                <code class="rounded bg-[#f1f4f0] px-1.5 py-1"
                    >php artisan db:seed</code
                >
                to load the fictional demo dataset.
            </div>

            <footer
                class="flex flex-col gap-2 py-7 text-[11px] text-[#929a93] sm:flex-row sm:items-center sm:justify-between"
            >
                <span
                    >Shortlist <span class="px-1">·</span> Responsible by
                    design</span
                >
                <span
                    >Every score is explainable, reviewable, and grounded in
                    job-related evidence.</span
                >
            </footer>
        </main>

        <div
            v-if="selectedResume"
            class="fixed inset-0 z-50 flex items-end justify-center bg-[#14251c]/40 p-0 backdrop-blur-[2px] sm:items-center sm:p-6"
            @click.self="selectedResume = null"
            @keydown.esc="selectedResume = null"
        >
            <section
                class="max-h-[88vh] w-full max-w-2xl overflow-hidden rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="resume-dialog-title"
            >
                <div
                    class="flex items-start justify-between border-b border-[#edf0ec] px-6 py-5 sm:px-7"
                >
                    <div class="flex items-center gap-3">
                        <span
                            class="grid h-11 w-11 place-items-center rounded-full bg-[#edf2ed] text-sm font-bold text-[#54715d]"
                            >{{ initials(selectedResume.name) }}</span
                        >
                        <div>
                            <h2 id="resume-dialog-title" class="font-semibold">
                                {{ selectedResume.name }}
                            </h2>
                            <p class="mt-0.5 text-xs text-[#7d877f]">
                                {{ selectedResume.title }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="grid h-9 w-9 place-items-center rounded-lg text-xl text-[#78827a] hover:bg-[#f2f5f1]"
                        aria-label="Close resume"
                        @click="selectedResume = null"
                    >
                        ×
                    </button>
                </div>
                <div class="max-h-[62vh] overflow-y-auto px-6 py-6 sm:px-7">
                    <div
                        class="mb-3 text-[11px] font-bold tracking-[0.13em] text-[#7b867e] uppercase"
                    >
                        Resume profile
                    </div>
                    <p
                        class="text-sm leading-7 whitespace-pre-line text-[#58645b]"
                    >
                        {{ selectedResume.body }}
                    </p>
                </div>
                <div
                    class="border-t border-[#edf0ec] bg-[#fbfcfa] px-6 py-4 text-xs text-[#89928a] sm:px-7"
                >
                    Fictional candidate profile · use the score as decision
                    support only
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
.score-high {
    color: #32714a;
}
.score-mid {
    color: #a27522;
}
.score-low {
    color: #7d8780;
}
</style>
