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
                'scope' => 'Zaprojektowanie, wykonanie i wdrożenie serwisu firmowego z katalogiem produktów oraz panelem zarządzania.',
                'solution' => 'Serwis oparty na Laravel, Blade, Filament i Vite.',
            ],
            [
                'slug' => 'olaya-strona',
                'title' => 'Olaya — strona internetowa',
                'category' => 'Serwis producenta saun',
                'relationship' => 'related',
                'summary' => 'Strona producenta saun ogrodowych wykonana od podstaw dla firmy powiązanej.',
                'goal' => 'Przedstawienie modeli saun, produktów i realizacji oraz ułatwienie kontaktu w sprawie oferty.',
                'scope' => 'Zaprojektowanie, wykonanie i wdrożenie strony producenta saun: prezentacja marki, katalog modeli, galeria realizacji i formularze kontaktowe.',
            ],
            [
                'slug' => 'olaya-aplikacja',
                'title' => 'Olaya — aplikacja do zarządzania',
                'category' => 'Aplikacja internetowa dla firmy',
                'relationship' => 'related',
                'summary' => 'Aplikacja i API wykonane od podstaw i wdrożone dla firmy powiązanej do obsługi procesów przedsiębiorstwa.',
                'goal' => 'Uporządkowanie informacji o produkcji, specyfikacjach saun i gospodarce materiałowej.',
                'scope' => 'Zaprojektowanie, wykonanie i wdrożenie aplikacji oraz API do obsługi sprzedaży, produkcji i magazynu, z rolami i uprawnieniami użytkowników.',
                'solution' => 'Panel Nuxt/Vue połączony z API Laravel. Aplikacja porządkuje informacje o sprzedaży, produkcji, specyfikacjach saun i gospodarce materiałowej.',
            ],
        ];
        foreach ($projects as $order => $project) {
            Project::firstOrCreate(['slug' => $project['slug']], $project + [
                'delivery_status' => 'deployed',
                'sort_order' => $order,
                'approved_for_publication' => false,
                'is_published' => false,
            ]);
        }
    }
}
