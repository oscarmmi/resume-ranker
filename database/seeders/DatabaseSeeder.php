<?php

namespace Database\Seeders;

use App\Models\CandidateResume;
use App\Models\JobOpening;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $job = JobOpening::query()->updateOrCreate(
            ['title' => 'Support Engineer'],
            ['description' => <<<'DESCRIPTION'
Harbor Desk is a fictional customer-support platform helping growing software teams deliver a thoughtful support experience. We are hiring a Support Engineer to troubleshoot technical issues for SaaS customers, investigate API and SQL questions, and own tickets from first response through resolution.

The role needs hands-on experience with customer support or technical support, ticketing tools such as Zendesk or Intercom, clear written communication, and a habit of creating useful documentation and knowledge-base articles. Strong candidates can read logs, reproduce bugs, explain technical topics in plain language, collaborate with product and engineering, and manage incidents with care. Experience with SQL, APIs, Linux, networking, Python, analytics, or customer success is valuable.
DESCRIPTION]
        );

        $resumes = [
            [
                'name' => 'Alex Morgan',
                'title' => 'Senior Technical Support Engineer',
                'body' => 'Six years in SaaS technical support for a customer communications platform. Owned complex Zendesk and Intercom ticket queues from first response to resolution, investigated REST API requests and SQL data issues, read application logs, and reproduced bugs with engineering. Led incident communication, wrote a searchable knowledge base and troubleshooting documentation, and coached customers through integrations. Known for clear written communication and calm customer support during outages.',
            ],
            [
                'name' => 'Jordan Lee',
                'title' => 'Customer Support Specialist',
                'body' => 'Four years delivering customer support and customer success for subscription software. Managed high-volume Zendesk tickets, wrote friendly customer responses, maintained help-center articles, and tracked satisfaction analytics. Skilled at communication, prioritizing requests, and escalating product feedback. Limited hands-on API or SQL troubleshooting, but comfortable learning new tools and working with technical teams.',
            ],
            [
                'name' => 'Sam Rivera',
                'title' => 'IT Operations Analyst',
                'body' => 'Five years supporting internal users and Linux systems. Troubleshot networking, access, and endpoint incidents, used Python scripts to automate diagnostics, and queried SQL reporting databases. Documented incident management procedures and partnered with engineering on root-cause reviews. Strong technical investigation skills; less experience with external SaaS customers, Zendesk, or customer success workflows.',
            ],
            [
                'name' => 'Taylor Brooks',
                'title' => 'SaaS Implementation Consultant',
                'body' => 'Three years implementing SaaS products for mid-market customers. Configured APIs and data imports, used SQL to validate migrations, and translated technical workflows into training and documentation. Partnered with customer success and support teams to resolve onboarding tickets in Intercom. Clear presenter and writer with occasional incident coordination; enjoys helping customers reach value.',
            ],
            [
                'name' => 'Casey Patel',
                'title' => 'Software Developer',
                'body' => 'Seven years building web applications with Python and SQL. Developed REST APIs, deployed services on Linux, reviewed logs, and fixed production bugs with product teams. Wrote unit tests and developer documentation. Interested in customer-facing roles but has not owned a support ticket queue, worked with Zendesk or Intercom, or led customer incident communications.',
            ],
            [
                'name' => 'Riley Chen',
                'title' => 'Retail Associate',
                'body' => 'Three years helping customers in a busy retail environment. Answered product questions, resolved returns, kept accurate records, and communicated clearly with teammates. Comfortable with basic office software and eager to build a career in technology. No professional SaaS, ticketing, API, SQL, or technical troubleshooting experience yet.',
            ],
        ];

        foreach ($resumes as $resume) {
            CandidateResume::query()->updateOrCreate(['name' => $resume['name']], $resume);
        }
    }
}
