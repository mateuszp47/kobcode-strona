<?php
namespace Database\Seeders;
use App\Models\Project;
use Illuminate\Database\Seeder;
class PortfolioDraftSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'slug' => 'walbud24',
                'title' => 'Walbud / Walbud24',
                'category' => 'Serwis firmowy i katalog produktów',
                'relationship' => 'client',
                'summary' => 'Serwis wykonany od podstaw dla klienta, z katalogiem produktów i panelem zarządzania.',
                'goal' => 'Prezentacja oferty i ułatwienie klientom przejścia od produktu do zapytania.',
                'scope' => 'Wykonanie projektu od podstaw. Zakres obejmuje serwis firmowy i katalog produktów. Szczegółowa lista funkcji zostanie uzupełniona po sprawdzeniu aktualnej wersji.',
                'solution' => 'Laravel, Blade, Filament i Vite. W pracach uwzględniono wersje polską, czeską i słowacką oraz ceny PLN i EUR; ich bieżący status wymaga weryfikacji.',
            ],
            [
                'slug' => 'olaya-strona',
                'title' => 'Olaya — strona internetowa',
                'category' => 'Serwis producenta saun',
                'relationship' => 'related',
                'summary' => 'Strona producenta saun ogrodowych wykonana od podstaw dla firmy powiązanej.',
                'goal' => 'Przedstawienie modeli saun, produktów i realizacji oraz ułatwienie kontaktu w sprawie oferty.',
                'scope' => 'Wykonanie projektu od podstaw. Szczegółowy opis funkcji i aktualnego wdrożenia pozostaje do weryfikacji.',
            ],
            [
                'slug' => 'olaya-aplikacja',
                'title' => 'Olaya — aplikacja do zarządzania',
                'category' => 'Aplikacja internetowa dla firmy',
                'relationship' => 'related',
                'summary' => 'Aplikacja i API wykonane od podstaw dla firmy powiązanej, rozwijane z myślą o obsłudze procesów przedsiębiorstwa.',
                'goal' => 'Uporządkowanie informacji o produkcji, specyfikacjach saun i gospodarce materiałowej.',
                'scope' => 'Wykonanie projektu od podstaw. Moduły i procesy zostaną opisane jako działające dopiero po weryfikacji.',
                'solution' => 'Panel Nuxt/Vue i API Laravel. Zakres prac obejmował produkcję, specyfikacje, magazyn oraz obszary CRM, zamówień i faktur. Lista nie oznacza ukończenia lub wdrożenia wszystkich modułów.',
            ],
        ];
        foreach ($projects as $order => $project) {
            Project::firstOrCreate(['slug' => $project['slug']], $project + [
                'delivery_status' => 'unverified',
                'sort_order' => $order,
                'approved_for_publication' => false,
                'is_published' => false,
            ]);
        }
    }
}
