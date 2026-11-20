<?php

namespace App\Demo\InstallFlow;

use Database\Seeders\InstallFlowSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

final class Workspace
{
    public static function connect(string $path): void
    {
        DB::purge('installflow');
        config(['database.connections.installflow' => ['driver' => 'sqlite', 'database' => $path,
            'prefix' => '', 'foreign_key_constraints' => true, 'busy_timeout' => 15000, 'journal_mode' => 'DELETE']]);
    }

    public static function root(): string
    {
        $root = config('demo.storage_path');
        File::ensureDirectoryExists($root, 0700, true);
        return $root;
    }

    public static function template(): string
    {
        $root = self::root();
        // Daily relative dates keep the calendar useful; version changes create a fresh template.
        $path = $root.'/template-v'.config('demo.version').'-'.now()->format('Y-m-d').'.sqlite';
        $lock = fopen($root.'/template.lock', 'c');
        flock($lock, LOCK_EX);
        try {
            if (! is_file($path)) {
                $temporary = $path.'.building';
                if (is_file($temporary)) unlink($temporary);
                touch($temporary); chmod($temporary, 0600);
                self::connect($temporary);
                $migration = require database_path('migrations/installflow/2026_11_20_000000_create_installflow_tables.php');
                $migration->up();
                (new InstallFlowSeeder)->run();
                DB::disconnect('installflow');
                rename($temporary, $path);
            }
            return $path;
        } finally {
            flock($lock, LOCK_UN); fclose($lock);
        }
    }

    public static function path(string $token): string
    {
        if (! preg_match('/^[a-f0-9]{64}$/D', $token)) throw new \InvalidArgumentException('Invalid demo session.');
        return self::root().'/'.$token.'.sqlite';
    }
}
