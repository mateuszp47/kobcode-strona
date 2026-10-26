<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        foreach (DB::table('projects')->whereIn('slug', ['walbud24', 'olaya-strona', 'olaya-aplikacja'])->get() as $project) {
            $changes = ['delivery_status' => 'deployed'];
            $replacements = [
                'Wykonanie projektu od podstaw. Zakres obejmuje serwis firmowy i katalog produktów. Szczegółowa lista funkcji zostanie uzupełniona po sprawdzeniu aktualnej wersji.' => 'Zaprojektowanie, wykonanie i wdrożenie serwisu firmowego z katalogiem produktów oraz panelem zarządzania.',
                'Laravel, Blade, Filament i Vite. W pracach uwzględniono wersje polską, czeską i słowacką oraz ceny PLN i EUR; ich bieżący status wymaga weryfikacji.' => 'Serwis oparty na Laravel, Blade, Filament i Vite.',
                'Wykonanie projektu od podstaw. Szczegółowy opis funkcji i aktualnego wdrożenia pozostaje do weryfikacji.' => 'Zaprojektowanie, wykonanie i wdrożenie strony producenta saun: prezentacja marki, katalog modeli, galeria realizacji i formularze kontaktowe.',
                'Aplikacja i API wykonane od podstaw dla firmy powiązanej, rozwijane z myślą o obsłudze procesów przedsiębiorstwa.' => 'Aplikacja i API wykonane od podstaw i wdrożone dla firmy powiązanej do obsługi procesów przedsiębiorstwa.',
                'Wykonanie projektu od podstaw. Moduły i procesy zostaną opisane jako działające dopiero po weryfikacji.' => 'Zaprojektowanie, wykonanie i wdrożenie aplikacji oraz API do obsługi sprzedaży, produkcji i magazynu, z rolami i uprawnieniami użytkowników.',
                'Panel Nuxt/Vue i API Laravel. Zakres prac obejmował produkcję, specyfikacje, magazyn oraz obszary CRM, zamówień i faktur. Lista nie oznacza ukończenia lub wdrożenia wszystkich modułów.' => 'Panel Nuxt/Vue połączony z API Laravel. Aplikacja porządkuje informacje o sprzedaży, produkcji, specyfikacjach saun i gospodarce materiałowej.',
            ];
            foreach (['summary', 'scope', 'solution'] as $field) {
                if (isset($replacements[$project->$field])) $changes[$field] = $replacements[$project->$field];
            }
            DB::table('projects')->where('id', $project->id)->update($changes);
        }
    }
    public function down(): void {}
};
