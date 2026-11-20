<?php
namespace App\Console\Commands;

use App\Demo\InstallFlow\{DemoAccess, Workspace};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallFlowDemo extends Command
{
    protected $signature = 'demo:installflow {--prune : Usuń wygasłe bazy demonstracyjne}';
    protected $description = 'Przygotuj prywatny szablon SQLite InstallFlow lub usuń wygasłe sesje, bez zmian w bazie KOBCODE';

    public function handle(): int
    {
        if (! DemoAccess::enabled()) { $this->error('Demo wyłączone. Lokalnie ustaw APP_ENV=local i DEMO_APPS_ENABLED=true.'); return self::FAILURE; }
        if ($this->option('prune')) {
            $removed = 0;
            foreach (File::glob(Workspace::root().'/*.sqlite') as $file) {
                if (filemtime($file) > now()->subMinutes(config('demo.lifetime_minutes') + 60)->timestamp) continue;
                if (str_starts_with(basename($file), 'template-') && str_contains(basename($file), now()->format('Y-m-d'))) continue;
                $lock = fopen($file.'.lock', 'c');
                if (flock($lock, LOCK_EX | LOCK_NB)) { if (is_file($file)) { unlink($file); $removed++; } flock($lock, LOCK_UN); }
                fclose($lock);
            }
            $this->info('Usunięte wygasłe bazy: '.$removed);
        } else {
            Workspace::template();
            $this->info('InstallFlow gotowy: /demo. Dane powstają w osobnych prywatnych bazach SQLite dla każdej sesji.');
        }
        return self::SUCCESS;
    }
}
