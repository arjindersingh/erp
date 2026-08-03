<?php

declare(strict_types=1);

namespace App\Core\System;

use Illuminate\Cache\CacheManager;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class SystemHealthService
{
    public function __construct(
        private readonly CacheManager $cache,
        private readonly Migrator $migrator,
        private readonly Filesystem $files,
    ) {}

    public function inspect(): SystemHealthReport
    {
        $checks = collect([
            $this->applicationCheck(), $this->databaseCheck(), $this->cacheCheck(), $this->queueCheck(),
            $this->storageCheck(), $this->mailCheck(), $this->environmentCheck(), $this->migrationCheck(),
            $this->phpCheck(), $this->logCheck(),
        ]);
        $status = $checks->contains(fn (HealthCheckResult $check): bool => $check->status === HealthStatus::Unhealthy)
            ? HealthStatus::Unhealthy
            : ($checks->contains(fn (HealthCheckResult $check): bool => $check->status === HealthStatus::Warning)
                ? HealthStatus::Warning
                : HealthStatus::Healthy);

        return new SystemHealthReport($status, $checks, now()->toIso8601String());
    }

    /** @return array{status: string, service: string, version: string, timestamp: string} */
    public function publicStatus(): array
    {
        try {
            DB::select('select 1');
            $status = 'ok';
        } catch (Throwable) {
            $status = 'unavailable';
        }

        return [
            'status' => $status,
            'service' => (string) config('app.name'),
            'version' => (string) config('system.version'),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function applicationCheck(): HealthCheckResult
    {
        return new HealthCheckResult('application', 'Application', HealthStatus::Healthy, 'Application boot completed.', (string) config('system.version'));
    }

    private function databaseCheck(): HealthCheckResult
    {
        return $this->attempt('database', 'Database', function (): array {
            DB::select('select 1');

            return [HealthStatus::Healthy, 'Database connection is available.', (string) config('database.default')];
        });
    }

    private function cacheCheck(): HealthCheckResult
    {
        return $this->attempt('cache', 'Cache', function (): array {
            $key = 'system-health:'.str()->uuid();
            $this->cache->put($key, 'ok', 10);
            $available = $this->cache->get($key) === 'ok';
            $this->cache->forget($key);

            return [$available ? HealthStatus::Healthy : HealthStatus::Unhealthy, $available ? 'Cache read and write succeeded.' : 'Cache verification failed.', (string) config('cache.default')];
        });
    }

    private function queueCheck(): HealthCheckResult
    {
        $connection = (string) config('queue.default');
        $configured = config("queue.connections.{$connection}") !== null;

        return new HealthCheckResult('queue', 'Queue', $configured ? HealthStatus::Healthy : HealthStatus::Unhealthy, $configured ? 'Queue connection is configured.' : 'Queue connection is missing.', $connection);
    }

    private function storageCheck(): HealthCheckResult
    {
        return $this->attempt('storage', 'Storage', function (): array {
            $path = 'health/'.str()->uuid().'.tmp';
            Storage::disk('local')->put($path, 'ok');
            $available = Storage::disk('local')->get($path) === 'ok';
            Storage::disk('local')->delete($path);

            return [$available ? HealthStatus::Healthy : HealthStatus::Unhealthy, $available ? 'Private storage is writable.' : 'Private storage verification failed.', 'local'];
        });
    }

    private function mailCheck(): HealthCheckResult
    {
        $mailer = (string) config('mail.default');
        $configured = config("mail.mailers.{$mailer}") !== null && filled(config('mail.from.address'));

        return new HealthCheckResult('mail', 'Mail', $configured ? HealthStatus::Healthy : HealthStatus::Warning, $configured ? 'Mail transport and sender are configured.' : 'Mail configuration is incomplete.', $mailer);
    }

    private function environmentCheck(): HealthCheckResult
    {
        $valid = filled(config('app.key')) && (! app()->environment('production') || ! config('app.debug'));

        return new HealthCheckResult('environment', 'Environment', $valid ? HealthStatus::Healthy : HealthStatus::Unhealthy, $valid ? 'Required environment safeguards are present.' : 'Application key or production debug configuration is unsafe.', app()->environment());
    }

    private function migrationCheck(): HealthCheckResult
    {
        return $this->attempt('migrations', 'Migrations', function (): array {
            $files = $this->migrator->getMigrationFiles(database_path('migrations'));
            $pending = array_diff(array_keys($files), $this->migrator->getRepository()->getRan());
            $count = count($pending);

            return [$count === 0 ? HealthStatus::Healthy : HealthStatus::Warning, $count === 0 ? 'Database schema is current.' : "{$count} migration(s) are pending.", (string) $count];
        });
    }

    private function phpCheck(): HealthCheckResult
    {
        $valid = version_compare(PHP_VERSION, '8.4.0', '>=');

        return new HealthCheckResult('php', 'PHP', $valid ? HealthStatus::Healthy : HealthStatus::Unhealthy, $valid ? 'PHP version meets the platform requirement.' : 'PHP 8.4 or newer is required.', PHP_VERSION);
    }

    private function logCheck(): HealthCheckResult
    {
        $directory = storage_path('logs');
        $available = $this->files->isDirectory($directory) && is_writable($directory);

        return new HealthCheckResult('logs', 'Application logs', $available ? HealthStatus::Healthy : HealthStatus::Unhealthy, $available ? 'Log directory is writable.' : 'Log directory is unavailable.');
    }

    /** @param callable(): array{HealthStatus, string, string|null} $callback */
    private function attempt(string $key, string $label, callable $callback): HealthCheckResult
    {
        try {
            [$status, $summary, $value] = $callback();

            return new HealthCheckResult($key, $label, $status, $summary, $value);
        } catch (Throwable) {
            return new HealthCheckResult($key, $label, HealthStatus::Unhealthy, "{$label} check failed.");
        }
    }
}
