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
 * Local demo of the sponsors system: a sponsor who can sign in, a widow family and an orphan file sponsored in
 * shekels and dollars, an amount change kept as history, and a child of another sponsor the demo sponsor must not see.
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

        if (Sponsor::where('email', self::EMAIL)->exists()) {
            $this->command?->warn('The sponsor demo is already seeded.');

            return;
        }

        $team = User::whereIn('role', User::FINANCE_ROLES)->orderBy('id')->firstOrFail();

        $sponsor = Sponsor::create([
            'name' => 'أبو خالد الحارثي',
            'email' => self::EMAIL,
            'phone' => '+966500000001',
            'country' => 'السعودية',
            'preferred_currency' => 'ILS',
            'notes' => 'كافل تجريبي للتجربة المحلية.',
            'created_by' => $team->id,
        ]);
        $account = User::create(['name' => $sponsor->name, 'email' => self::EMAIL, 'password' => self::PASSWORD, 'role' => User::ROLE_SPONSOR]);
        $sponsor->forceFill(['user_id' => $account->id])->save();

        $otherSponsor = Sponsor::create([
            'name' => 'عائلة العتيبي',
            'country' => 'الكويت',
            'preferred_currency' => 'USD',
            'created_by' => $team->id,
        ]);

        $widowFamily = OrphanFamily::factory()->approved()->widowFamily()->create([
            'source' => OrphanFamily::SOURCE_DASHBOARD,
            'mother_full_name' => 'فاطمة أحمد حسن النجار',
            'father_full_name' => 'محمد سالم خليل النجار',
            'documents' => [],
            'reviewed_by' => $team->id,
        ]);
        $yousef = $this->orphan($widowFamily, 'يوسف محمد سالم النجار', 9, 'primary', 'healthy');
        $mariam = $this->orphan($widowFamily, 'مريم محمد سالم النجار', 6, 'kindergarten', 'chronic_illness');

        SponsorshipManager::applyToWidowFamily($widowFamily, [
            'sponsorship_status' => OrphanFamily::SPONSORSHIP_SPONSORED,
            'sponsor_id' => $sponsor->id,
            'currency' => 'ILS',
            'started_at' => now()->subMonths(3)->startOfMonth()->toDateString(),
            'mother_amount' => 400,
            'orphans' => [$yousef->id => ['monthly_amount' => 200], $mariam->id => ['monthly_amount' => 200]],
        ], $team);

        $orphanFile = OrphanFamily::factory()->approved()->create([
            'source' => OrphanFamily::SOURCE_DASHBOARD,
            'contact_name' => 'عبد الله سالم أبو عيد',
            'father_full_name' => 'سامي عبد الله أبو عيد',
            'documents' => [],
            'reviewed_by' => $team->id,
        ]);
        $omar = $this->orphan($orphanFile, 'عمر سامي عبد الله أبو عيد', 12, 'preparatory', 'healthy');
        $salma = $this->orphan($orphanFile, 'سلمى سامي عبد الله أبو عيد', 15, 'secondary', 'injured');

        SponsorshipManager::applyToOrphans($orphanFile, [
            $omar->id => $this->orphanTerms($sponsor, 50, now()->subMonths(8)->startOfMonth()->toDateString()),
            $salma->id => $this->orphanTerms($otherSponsor, 60, now()->subMonths(5)->startOfMonth()->toDateString()),
        ], $team);
        SponsorshipManager::applyToOrphans($orphanFile, [
            $omar->id => $this->orphanTerms($sponsor, 75, now()->subMonths(2)->startOfMonth()->toDateString()),
        ], $team);

        $this->command?->info('Sponsor demo ready. Sign in as '.self::EMAIL.' / '.self::PASSWORD);
    }

    private function orphan(OrphanFamily $family, string $name, int $age, string $education, string $health): Orphan
    {
        return Orphan::factory()->for($family, 'family')->create([
            'full_name' => $name,
            'birth_date' => now()->subYears($age)->subMonths(3)->toDateString(),
            'education_stage' => $education,
            'health_status' => $health,
        ]);
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
