<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportCaseStudies extends Command
{
    protected $signature = 'kobcode:case-studies {--publish : Zatwierdź i opublikuj pięć przygotowanych opisów po ich sprawdzeniu} {--refresh-media : Podmień wcześniejsze zdjęcia Cermax pochodzące z importera}';
    protected $description = 'Uzupełnia puste pola opisów portfolio; domyślnie nowe wpisy są szkicami, bez nadpisywania istniejących treści';

    public function handle(): int
    {
        foreach (config('case-studies') as $slug => $defaults) {
            // The historical Cermax alias may already exist; never create its duplicate.
            $project = Project::whereIn('slug', $slug === 'cermax' ? ['cermax', 'b2b-cermax'] : [$slug])->orderBy('id')->first();
            $project ??= new Project(['slug' => $slug, 'delivery_status' => 'deployed', 'approved_for_publication' => false, 'is_published' => false]);
            $isNew = ! $project->exists;
            $screenshots = $defaults['screenshots'];
            unset($defaults['screenshots']);
            foreach ($defaults as $field => $value) {
                if ($project->$field === null || $project->$field === '' || $project->$field === []) {
                    $project->$field = $value;
                }
            }
            // Never change an existing relationship or publication decision implicitly.
            $refreshMedia = $slug === 'cermax' && $this->option('refresh-media');
            if ($refreshMedia && $project->cover && ! $this->isManagedCermaxImage($project->cover)) {
                $this->warn('Cermax: własną okładkę zachowano; nowy zrzut można wybrać w panelu.');
            }
            if (! $project->cover || ($refreshMedia && $this->isManagedCermaxImage($project->cover))) {
                $cover = $this->copyScreenshot($slug, $screenshots[0]);
                if ($cover) {
                    $project->cover = $cover['path'];
                    $project->cover_alt = $refreshMedia ? $cover['alt'] : ($project->cover_alt ?: $cover['alt']);
                }
            }
            $managedGallery = collect($project->gallery ?? [])->every(fn (array $image) => $this->isManagedCermaxImage($image['path'] ?? ''));
            if ($refreshMedia && $project->gallery && ! $managedGallery) $this->warn('Cermax: własną galerię zachowano; nowe zrzuty można wybrać w panelu.');
            if ((! $project->gallery || ($refreshMedia && $managedGallery)) && count($screenshots) > 1) {
                $project->gallery = array_values(array_filter(array_map(
                    fn (array $image) => $this->copyScreenshot($slug, $image), array_slice($screenshots, 1)
                )));
            }
            if ($this->option('publish')) {
                if (in_array($project->relationship, ['client', 'own', 'related', 'portfolio'], true)) {
                    $project->approved_for_publication = true;
                    $project->is_published = true;
                } else {
                    $this->warn("{$project->slug}: pozostawiono szkic — ustal relację w panelu.");
                }
            }
            if ($project->isDirty() || $isNew) {
                $project->save();
            }
            $this->line($project->slug.': '.($project->is_published ? 'opublikowany' : 'szkic'));
        }
        $this->info('Istniejące opisy, pliki i ustawienia zachowano. Publikacja wymaga osobnego użycia --publish lub zatwierdzenia w panelu.');
        return self::SUCCESS;
    }

    private function copyScreenshot(string $slug, array $image): ?array
    {
        $width = is_file(public_path('images/portfolio/'.$image['image'].'-2047.webp')) ? 2047 : 1600;
        $source = public_path('images/portfolio/'.$image['image'].'-'.$width.'.webp');
        if (! is_file($source)) {
            $this->warn('Brak materiału: '.$image['image'].' — pominięto zdjęcie.');
            return null;
        }
        $path = 'portfolio/case-studies/'.$slug.'/'.$image['image'].'.webp';
        $this->copyFile($source, $path);
        if ($width === 2047) {
            foreach ([800, 1600] as $variant) {
                $variantSource = public_path('images/portfolio/'.$image['image'].'-'.$variant.'.webp');
                if (is_file($variantSource)) $this->copyFile($variantSource, substr($path, 0, -5).'-'.$variant.'.webp');
            }
        }
        return ['path' => $path, 'alt' => $image['alt'], 'caption' => $image['caption']];
    }
    private function isManagedCermaxImage(string $path): bool
    {
        return (bool) preg_match('~^portfolio/case-studies/cermax/cermax-(home|catalog|b2b|panel-(start|products|orders|clients|reports))\.webp$~', $path);
    }

    private function copyFile(string $source, string $path): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists($path)) return;
        $stream = fopen($source, 'rb');
        try {
            if (! $disk->put($path, $stream)) throw new \RuntimeException('Nie zapisano zdjęcia: '.$path);
        } finally {
            if (is_resource($stream)) fclose($stream);
        }
    }

}
