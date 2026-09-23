<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Artisan command: php artisan bader:health
 *
 * Verifies all critical production dependencies are correctly configured.
 * Exit code 0 = all checks passed; 1 = at least one check failed.
 */
class ProductionHealthCheck extends Command
{
    protected $signature = 'bader:health {--json : Output results as JSON}';

    protected $description = 'Run a pre-launch production health check for Bader Humanitarian';

    /** @var array<array{check: string, status: string, note: string}> */
    private array $results = [];

    public function handle(): int
    {
        $this->checkAppKey();
        $this->checkDebugMode();
        $this->checkAppEnv();
        $this->checkDatabase();
        $this->checkCache();
        $this->checkMailConfig();
        $this->checkStorageLink();
        $this->checkLocaleFiles();

        if ($this->option('json')) {
            $this->line(json_encode($this->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->renderTable();
        }

        $failed = collect($this->results)->where('status', 'FAIL')->count();

        if ($failed > 0) {
            $this->newLine();
            $this->error("❌ {$failed} health check(s) failed. Fix before deploying to production.");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('✅ All health checks passed. Ready for production.');

        return self::SUCCESS;
    }

    // ── Checks ─────────────────────────────────────────────────────────────────

    private function checkAppKey(): void
    {
        $key = config('app.key');
        $this->record(
            'APP_KEY',
            ! empty($key),
            ! empty($key) ? 'Set' : 'Missing — run php artisan key:generate',
        );
    }

    private function checkDebugMode(): void
    {
        $debug = config('app.debug');
        $this->record(
            'APP_DEBUG=false',
            ! $debug,
            $debug ? 'DEBUG is true — must be false in production' : 'false',
        );
    }

    private function checkAppEnv(): void
    {
        $env = config('app.env');
        $this->record(
            'APP_ENV=production',
            $env === 'production',
            $env === 'production' ? 'production' : "Currently set to: {$env}",
        );
    }

    private function checkDatabase(): void
    {
        try {
            DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $this->record('Database', true, "Connected ({$driver})");
        } catch (Throwable $e) {
            $this->record('Database', false, 'Connection failed: '.$e->getMessage());
        }
    }

    private function checkCache(): void
    {
        try {
            $key = '_bader_health_'.time();
            Cache::put($key, 'ok', 5);
            $ok = Cache::get($key) === 'ok';
            Cache::forget($key);
            $driver = config('cache.default');
            $this->record('Cache', $ok, $ok ? "Working ({$driver})" : 'Read/write failed');
        } catch (Throwable $e) {
            $this->record('Cache', false, 'Error: '.$e->getMessage());
        }
    }

    private function checkMailConfig(): void
    {
        $mailer = config('mail.default');
        $from = config('mail.from.address');
        $isLog = $mailer === 'log';

        $this->record(
            'Mail config',
            ! empty($from),
            $isLog
                ? "Mailer is 'log' (no emails sent) — switch to smtp/mailgun for production"
                : "Mailer: {$mailer}, From: {$from}",
            warn: $isLog, // warn but not fail
        );
    }

    private function checkStorageLink(): void
    {
        $link = public_path('storage');
        $exists = is_link($link) || is_dir($link);
        $this->record(
            'Storage link',
            $exists,
            $exists ? 'public/storage symlink exists' : 'Missing — run php artisan storage:link',
        );
    }

    private function checkLocaleFiles(): void
    {
        $arExists = file_exists(lang_path('ar.json'));
        $enExists = file_exists(lang_path('en.json'));
        $both = $arExists && $enExists;

        $this->record(
            'Locale files (ar/en)',
            $both,
            $both ? 'ar.json and en.json present' : 'One or both locale files missing',
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function record(string $check, bool $pass, string $note, bool $warn = false): void
    {
        $status = $pass ? 'PASS' : ($warn ? 'WARN' : 'FAIL');
        $this->results[] = compact('check', 'status', 'note');
    }

    private function renderTable(): void
    {
        $this->newLine();
        $this->line('<fg=cyan>Bader Humanitarian — Production Health Check</>');
        $this->newLine();

        $rows = collect($this->results)->map(function (array $r) {
            $icon = match ($r['status']) {
                'PASS' => '<fg=green>✔ PASS</>',
                'WARN' => '<fg=yellow>⚠ WARN</>',
                default => '<fg=red>✘ FAIL</>',
            };

            return [$icon, $r['check'], $r['note']];
        })->toArray();

        $this->table(['Status', 'Check', 'Note'], $rows);
    }
}
