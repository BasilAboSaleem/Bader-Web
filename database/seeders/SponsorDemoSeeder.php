<?php

namespace Database\Seeders;

use App\Models\Orphan;
use App\Models\OrphanFamily;
use App\Models\Sponsor;
use App\Models\User;
use App\Support\SponsorshipManager;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Rich local demo for management walkthroughs: staff orphans list, sponsors module, and a sponsor portal account.
 * Safe to re-run; it refreshes the demo sponsor password and fills any missing showcase files.
 * Run with: php artisan db:seed --class=SponsorDemoSeeder
 */
class SponsorDemoSeeder extends Seeder
{
    public const EMAIL = 'sponsor@bader.test';

    public const PASSWORD = 'Sponsor@2026';

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('The sponsor demo is for local testing only.');
        }

        $team = User::whereIn('role', User::FINANCE_ROLES)->orderBy('id')->firstOrFail();

        $sponsor = $this->demoSponsor($team);
        $otherSponsor = Sponsor::query()->firstOrCreate(
            ['name' => 'عائلة العتيبي'],
            ['country' => 'الكويت', 'preferred_currency' => 'USD', 'created_by' => $team->id],
        );

        $widowFamily = $this->widowFamily($team);
        $this->orphan($widowFamily, 'يوسف محمد سالم النجار', 9, 'primary', 'healthy');
        $this->orphan($widowFamily, 'مريم محمد سالم النجار', 6, 'kindergarten', 'chronic_illness');

        if ($widowFamily->activeSponsorships()->count() === 0) {
            SponsorshipManager::applyToWidowFamily($widowFamily, [
                'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
                'sponsor_id' => $sponsor->id,
                'currency' => 'ILS',
                'started_at' => now()->subMonths(3)->startOfMonth()->toDateString(),
                'sponsorship_amount' => 800,
            ], $team);
        }

        $orphanFile = $this->orphanFile($team, 'سامي عبد الله أبو عيد', 'عبد الله سالم أبو عيد');
        $omar = $this->orphan($orphanFile, 'عمر سامي عبد الله أبو عيد', 12, 'preparatory', 'healthy');
        $salma = $this->orphan($orphanFile, 'سلمى سامي عبد الله أبو عيد', 15, 'secondary', 'injured');

        if (! $omar->activeSponsorship()->exists()) {
            SponsorshipManager::applyToOrphans($orphanFile, [
                $omar->id => $this->orphanTerms($sponsor, 50, now()->subMonths(8)->startOfMonth()->toDateString()),
                $salma->id => $this->orphanTerms($otherSponsor, 60, now()->subMonths(5)->startOfMonth()->toDateString()),
            ], $team);
            SponsorshipManager::applyToOrphans($orphanFile, [
                $omar->id => $this->orphanTerms($sponsor, 75, now()->subMonths(2)->startOfMonth()->toDateString()),
            ], $team);
        }

        $available = $this->orphanFile($team, 'خليل محمود أبو قاسم', 'محمود خليل أبو قاسم', 'demo-available');
        $this->orphan($available, 'لينا خليل محمود أبو قاسم', 8, 'primary', 'healthy');
        $this->orphan($available, 'كريم خليل محمود أبو قاسم', 11, 'preparatory', 'disability');

        if (! OrphanFamily::query()->where('father_full_name', 'إبراهيم حسن الشوا')->where('review_status', OrphanFamily::REVIEW_PENDING)->exists()) {
            $pending = OrphanFamily::factory()->widowFamily()->create([
                'source' => OrphanFamily::SOURCE_WEBSITE,
                'review_status' => OrphanFamily::REVIEW_PENDING,
                'mother_full_name' => 'سناء إبراهيم حسن الشوا',
                'father_full_name' => 'إبراهيم حسن الشوا',
                'documents' => [],
            ]);
            $this->orphan($pending, 'تالا إبراهيم حسن الشوا', 7, 'primary', 'healthy');
        }

        $secondWidow = $this->namedWidow($team, 'نادية سعيد البرغوثي', 'سعيد أحمد البرغوثي');
        $this->orphan($secondWidow, 'هادي سعيد أحمد البرغوثي', 10, 'primary', 'healthy');
        $this->orphan($secondWidow, 'لما سعيد أحمد البرغوثي', 5, 'kindergarten', 'healthy');

        if ($secondWidow->activeSponsorships()->count() === 0) {
            SponsorshipManager::applyToWidowFamily($secondWidow, [
                'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
                'sponsor_id' => $sponsor->id,
                'currency' => 'SAR',
                'started_at' => now()->subMonths(1)->startOfMonth()->toDateString(),
                'sponsorship_amount' => 1000,
            ], $team);
        }

        $this->command?->newLine();
        $this->command?->info('Local demo ready for management walkthrough.');
        $this->command?->table(
            ['Role', 'URL', 'Email', 'Password'],
            [
                ['Admin / team', '/login → /dashboard', $team->email, '(your local admin password)'],
                ['Sponsor portal', '/login → /my-sponsorships', self::EMAIL, self::PASSWORD],
            ],
        );
        $this->command?->line('Sponsors: '.$sponsor->name.' (account) + '.$otherSponsor->name.' (no account)');
        $this->command?->line('Orphan files: sponsored widow + mixed orphan file + available file + pending website registration + SAR widow.');
    }

    private function demoSponsor(User $team): Sponsor
    {
        $sponsor = Sponsor::query()->firstOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'أبو خالد الحارثي',
                'phone' => '+966500000001',
                'country' => 'السعودية',
                'preferred_currency' => 'ILS',
                'notes' => 'كافل تجريبي للعرض المحلي أمام الإدارة.',
                'created_by' => $team->id,
            ],
        );

        $account = User::query()->updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => $sponsor->name,
                'password' => self::PASSWORD,
                'role' => User::ROLE_SPONSOR,
            ],
        );

        $sponsor->forceFill([
            'name' => 'أبو خالد الحارثي',
            'phone' => '+966500000001',
            'country' => 'السعودية',
            'preferred_currency' => 'ILS',
            'notes' => 'كافل تجريبي للعرض المحلي أمام الإدارة.',
            'user_id' => $account->id,
        ])->save();

        return $sponsor->refresh();
    }

    private function widowFamily(User $team): OrphanFamily
    {
        return OrphanFamily::query()->firstOrCreate(
            ['father_full_name' => 'محمد سالم خليل النجار', 'type' => OrphanFamily::TYPE_WIDOW_FAMILY],
            [
                'source' => OrphanFamily::SOURCE_DASHBOARD,
                'review_status' => OrphanFamily::REVIEW_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $team->id,
                'mother_full_name' => 'فاطمة أحمد حسن النجار',
                'mother_national_id' => '401112223',
                'mother_birth_date' => '1990-04-12',
                'mother_residence_area' => 'nuseirat_camp',
                'mother_neighborhood' => 'بلوك 5',
                'mother_nearest_landmark' => 'مسجد القسام',
                'mother_birth_country' => 'فلسطين',
                'mother_birth_place' => 'غزة',
                'mother_nationality' => 'فلسطينية',
                'mother_phone' => '0597111222',
                'father_death_date' => now()->subYears(2)->toDateString(),
                'father_death_cause' => 'استشهد في قصف',
                'documents' => [],
            ],
        );
    }

    private function namedWidow(User $team, string $mother, string $father): OrphanFamily
    {
        return OrphanFamily::query()->firstOrCreate(
            ['father_full_name' => $father, 'type' => OrphanFamily::TYPE_WIDOW_FAMILY],
            [
                'source' => OrphanFamily::SOURCE_DASHBOARD,
                'review_status' => OrphanFamily::REVIEW_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $team->id,
                'mother_full_name' => $mother,
                'mother_national_id' => fake()->unique()->numerify('4########'),
                'mother_birth_date' => '1988-08-20',
                'mother_residence_area' => 'khan_younis',
                'mother_neighborhood' => 'المواصي',
                'mother_nearest_landmark' => 'عيادة الهلال',
                'mother_birth_country' => 'فلسطين',
                'mother_birth_place' => 'خان يونس',
                'mother_nationality' => 'فلسطينية',
                'mother_phone' => '0597'.fake()->numerify('######'),
                'father_death_date' => now()->subYear()->toDateString(),
                'father_death_cause' => 'استشهد في قصف',
                'documents' => [],
            ],
        );
    }

    private function orphanFile(User $team, string $father, string $contact, string $marker = 'demo-orphan'): OrphanFamily
    {
        return OrphanFamily::query()->firstOrCreate(
            ['father_full_name' => $father, 'type' => OrphanFamily::TYPE_ORPHAN],
            [
                'source' => OrphanFamily::SOURCE_DASHBOARD,
                'review_status' => OrphanFamily::REVIEW_APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $team->id,
                'contact_name' => $contact,
                'contact_relation' => 'العم',
                'contact_phone' => '0599'.fake()->numerify('######'),
                'father_death_date' => now()->subYears(3)->toDateString(),
                'father_death_cause' => 'استشهد في قصف',
                'documents' => [],
                'review_notes' => $marker,
            ],
        );
    }

    private function orphan(OrphanFamily $family, string $name, int $age, string $education, string $health): Orphan
    {
        return Orphan::query()->firstOrCreate(
            ['orphan_family_id' => $family->id, 'full_name' => $name],
            [
                'birth_date' => now()->subYears($age)->subMonths(3)->toDateString(),
                'national_id' => fake()->unique()->numerify('4########'),
                'education_stage' => $education,
                'health_status' => $health,
            ],
        );
    }

    /**
     * @return array{sponsorship_status: string, sponsor_id: int, monthly_amount: int, currency: string, started_at: string}
     */
    private function orphanTerms(Sponsor $sponsor, int $amount, string $startedAt): array
    {
        return [
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
            'sponsor_id' => $sponsor->id,
            'monthly_amount' => $amount,
            'currency' => 'USD',
            'started_at' => $startedAt,
        ];
    }
}
