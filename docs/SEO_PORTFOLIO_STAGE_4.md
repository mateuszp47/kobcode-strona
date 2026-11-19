# Etap 4 — opisy realizacji KOBCODE

## Stan początkowy i podstawa

19 listopada 2026 produkcyjne `/realizacje` odpowiadało HTTP 200. Cermax, Olaya, ParkStop i Walbud były opisane na wspólnej stronie; znaleziono również odnośnik do `/realizacje/home-kreator`, dodanego z panelu. Istniejący mechanizm `/realizacje/{slug}` obsługiwał zatwierdzone wpisy bazy danych. Nie zmieniono ani nie usunięto tego wpisu, jego adresu, zdjęć ani innych danych produkcyjnych. Nie mamy kopii produkcyjnej bazy.

Podstawa gałęzi `feature/kobcode-seo-portfolio`: `feature/kobcode-seo-content` przy `9934bdb`. Na początku prac `main` pozostawał przy `310de95`, a etap 3 był w otwartym PR #3. Nowa gałąź zawiera wcześniejsze optymalizacje. Nie scalono niczego do main i nie wdrożono produkcji. Różnica całego PR względem main obejmuje również poprzednie etapy; poniżej opisano zakres samego etapu 4.

## Rzeczywiste zmiany

- Rozbudowano istniejący widok szczegółów, zamiast budować drugi moduł portfolio. Wprowadzenie, potrzeba biznesowa, cel, zakres, rozwiązanie, rezultat, potwierdzone technologie, galeria, powiązane usługi i kontakt. Puste sekcje są pomijane.
- Zachowano układ, kolory, logo, nawigację i statyczne podglądy głównych projektów na stronie zbiorczej. Kolejność: Cermax, strona Olaya, aplikacja Olaya, ParkStop, Walbud, dalsze wpisy z panelu. Dodano linki do opublikowanych szczegółów; szkice nie powodują martwych linków.
- Przygotowano pięć kompletów treści w `config/case-studies.php`. Polecenie `kobcode:case-studies` uzupełnia tylko puste pola, kopiuje istniejące WebP do publicznego dysku i domyślnie tworzy szkice. Powtórzenie zachowuje treści administratora, zdjęcia, relacje, kolejność i decyzje publikacyjne. Istniejące opisy nie są automatycznie zastępowane nowymi propozycjami.
- Cermax: wyszukiwanie po nazwie/SKU/EAN, publiczne serie i kategorie, ceny/warianty/stany po zalogowaniu, koszyk i zamówienie. Bez niepotwierdzonych integracji i technologii.
- Strona Olaya: modele, produkty, realizacje, kontakt i zarządzanie treścią. Aplikacja Olaya: sprzedaż, produkcja, magazyn, faktury, role; Nuxt/Vue i Laravel. Pokazane dane są demonstracyjne.
- ParkStop: cennik, dojazd, transfer i formularz terminów podróży. Bez dopisywania płatności online czy integracji, których nie potwierdzono.
- Walbud: katalog, kategorie, realizacje, formularze i CMS; potwierdzone Laravel, Blade, Filament, Vite. Nie przedstawiono prac nad językami i walutami jako ukończonych wdrożeń.
- Relacje: Walbud — klient; Olaya — firma powiązana. Dla Cermax i ParkStop neutralne „Realizacja KOBCODE”, bez zgadywania, czy są klientem lub projektem własnym. Wprowadzono odpowiadającą temu opcję `portfolio`. `unverified` nadal blokuje publikację.
- Panel: edytory opisów z formatowaniem, technologie, wybór usług, porządkowana galeria z alt i podpisem. Dotychczasowe pola SEO, okładka, statusy i zatwierdzenie pozostały. Usunięcie pozycji galerii nie usuwa pliku z dysku.
- HTML opisów jest oczyszczany; skrypty, zdarzenia, style i dodatkowe H1 nie trafiają do strony. Zwykły tekst dotychczasowych wpisów nadal zachowuje podział wierszy.
- Każdy nowy opis ma własny title, description i H1; istniejący mechanizm canonical/OG/robots pozostał. Dodano BreadcrumbList zgodny z widoczną ścieżką nawigacji. Sitemap automatycznie uwzględnia wpisy po publikacji i pomija szkice.
- Usługi prowadzą do opublikowanych szczegółów odpowiednich projektów; przed publikacją zachowują działające kotwice zbiorczego portfolio. Case studies prowadzą do usług i kontaktu.
- WebP, rzeczywiste wymiary obrazów, priorytet okładki i lazy loading galerii. Zachowano proporcje i brak powiększania zrzutów. Galeria korzysta z istniejących reguł: dwie kolumny na szerokich ekranach, jedna na telefonie. Nie wygenerowano fikcyjnych grafik ani wyników liczbowych.

## Adresy po publikacji

| Projekt | Adres | Powiązane usługi |
| --- | --- | --- |
| Cermax B2B | `/realizacje/cermax` | B2B, strony WWW |
| Olaya — strona | `/realizacje/olaya-strona` | strony WWW |
| Olaya — aplikacja | `/realizacje/olaya-aplikacja` | aplikacje, automatyzacja |
| ParkStop | `/realizacje/parkstop` | strony WWW |
| Walbud | `/realizacje/walbud24` | strony WWW |

Jeżeli istnieje historyczny wpis `b2b-cermax`, importer wykorzystuje go i zachowuje jego URL zamiast tworzyć duplikat. Nie dodano statycznego obejścia, które upubliczniałoby szkice.

## Migracja

`2026_11_19_160000_add_case_study_fields_to_projects.php` dodaje tylko trzy opcjonalne kolumny JSON: `technologies`, `gallery`, `service_links`. Nie usuwa rekordów i nie aktualizuje ich treści ani statusów. Cofnięcie migracji usuwałoby dane tych trzech nowych pól — przy zwykłym powrocie do wcześniejszego kodu pozostaw kolumny i nie uruchamiaj `migrate:rollback`.

## Zmienione pliki etapu 4

- `.github/workflows/checks.yml` — dodatkowa kontrola przeglądarkowa i zrzuty.
- `app/Console/Commands/ImportCaseStudies.php` — kontrolowany import.
- `app/Filament/Resources/ProjectResource.php` — pola panelu.
- `app/Models/Project.php` — JSON casts i neutralna relacja.
- `app/Support/ProjectContent.php` — bezpieczne formatowanie treści.
- `config/case-studies.php` — przygotowane opisy i przypisania materiałów.
- `database/migrations/2026_11_19_160000_add_case_study_fields_to_projects.php`.
- `resources/css/app.css` — czytelne listy i formatowanie opisów.
- `resources/views/project.blade.php`, `portfolio.blade.php`, `service.blade.php`.
- `resources/views/partials/case-study-link.blade.php`, `compact-project.blade.php`, `seo.blade.php`.
- `routes/web.php` — dane opublikowanych projektów do odnośników na usługach, bez zmiany adresów i nazw tras.
- `tests/Feature/CaseStudyTest.php`, `tests/Feature/PresentationTest.php`, `tests/browser/case-studies.mjs`.
- `docs/SEO_PORTFOLIO_STAGE_4.md`.

## Weryfikacja

Podstawowa wersja etapu 4: **30 testów Laravel/Livewire, 1025 asercji, wszystkie zakończone powodzeniem**. Wynik kolejnej aktualizacji zdjęć podano w PR. Kontrola Chromium: **18 wariantów widoków (6 adresów × 3 szerokości), wszystkie poprawne**, wraz z kontrolą menu mobilnego. Build Vite, cache widoków i lista tras również przeszły. Laravel, Livewire, kompilacja Blade oraz lista tras są sprawdzane na PHP 8.4 w GitHub Actions; środowisko edycji nie ma lokalnego PHP/Composera. Build Vite wykonano także lokalnie. Testy obejmują szkice, publikację, powtarzalny import, zachowanie własnych danych, historyczny slug, bezpieczeństwo HTML, stare rekordy po migracji, nowe pola panelu, meta/canonical, JSON-LD, obrazy, mapę i linki. Wcześniejsze testy formularza, panelu i SEO pozostają częścią zestawu.

Dodatkowy test Chromium używa wyłącznie tymczasowej bazy SQLite w CI. Sprawdza zbiorcze portfolio i pięć szczegółów przy szerokościach 390, 768 i 1440 px: odpowiedzi HTTP, jeden H1, załadowane obrazy, poziome przepełnienie, CTA, menu mobilne i błędy JS. Zrzuty Cermax oraz aplikacji Olaya są zapisywane jako artefakt workflow. Nie są to wyniki CWV ani test na fizycznym telefonie. Testy SQLite nie zastępują próby migracji na kopii produkcyjnej bazy MariaDB.

## Wdrożenie LH.pl po akceptacji

### 1. Przygotuj lokalnie i sprawdź

W istniejącym lokalnym repozytorium:

```bash
git fetch origin
git switch feature/kobcode-seo-portfolio
git pull --ff-only origin feature/kobcode-seo-portfolio
npm ci --ignore-scripts
npm run build
php artisan migrate
php artisan storage:link
php artisan kobcode:case-studies
php artisan view:clear
php artisan serve --port=8001
```

Zostaw własny lokalny `.env`. Nie kopiuj go z hostingu do repozytorium. Jeżeli symlink storage już istnieje, nie trzeba tworzyć go ponownie. Szkice znajdziesz w `/admin/projects`; przejrzyj je i zatwierdź publikację w panelu. Do sprawdzenia wszystkich pięciu na lokalnej wersji możesz wykonać osobno `php artisan kobcode:case-studies --publish` i ponownie uruchomić `php artisan serve --port=8001`. Opcja ta jest jawną decyzją o zatwierdzeniu tych pięciu wpisów; nie publikuje innych realizacji.

### 2. Kopia i tryb prac na serwerze

Przed przesyłaniem wykonaj eksport bazy w phpMyAdmin oraz kopię aktualizowanych katalogów i `storage/app/public` poza katalogiem publicznym WWW. Nie umieszczaj eksportu SQL w `public_html`. W Termiusie:

```bash
cd ~/public_html/kobcode.com
/usr/local/php84-fpm/bin/php artisan down
```

Tryb prac jest wskazany, bo panel i nowe widoki wymagają nowych kolumn, a przesyłanie przez FTP nie jest atomowe.

### 3. FileZilla — całe odpowiednie katalogi

Pobierz pliki z tej samej gałęzi, na której wykonano lokalny build. Przesyłaj zawartość wskazanych katalogów, scalając je i zastępując pliki o tej samej nazwie. Nie używaj synchronizacji usuwającej pozostałe pliki na serwerze.

| Lokalny katalog | Katalog na LH.pl |
| --- | --- |
| `app/Console/` | `/public_html/kobcode.com/app/Console/` |
| `app/Filament/` | `/public_html/kobcode.com/app/Filament/` |
| `app/Models/` | `/public_html/kobcode.com/app/Models/` |
| `app/Support/` | `/public_html/kobcode.com/app/Support/` |
| `config/` | `/public_html/kobcode.com/config/` |
| `database/migrations/` | `/public_html/kobcode.com/database/migrations/` |
| `resources/views/` | `/public_html/kobcode.com/resources/views/` |
| `routes/` | `/public_html/kobcode.com/routes/` |
| `public/build/` po lokalnym buildzie | `/public_html/kobcode.com/public/build/` |

W `public/build` wyślij nowe pliki `assets` przed `manifest.json`. Można zostawić starsze pliki z hashem na czas wdrożenia. Nie kopiuj `public/hot`. Źródłowy `resources/css/` nie jest potrzebny do uruchomienia produkcji, gdy wysłano gotowy build.

Nie przesyłaj całego projektu, `.env`, `.htaccess`, `vendor`, `node_modules`, `bootstrap/cache`, bazy SQLite ani lokalnego `storage`. Zachowaj obecny układ domeny i wewnętrzne kierowanie do `public` na LH.pl. Nie zmieniaj APP_KEY. Composer nie jest potrzebny: etap 4 nie zmienia zależności ani namespace istniejącego autoloadu App.

To lista względem zainstalowanego etapu 3. Jeżeli na produkcji pozostała starsza wersja, uwzględnij również checklistę `docs/SEO_GROWTH_PL.md` i `docs/SEO_CONTENT_STAGE_3.md`, korzystając z plików z obecnej gałęzi. W szczególności brakujące wcześniejsze pliki `app/Http/`, `public/fonts/` i inne zasoby nie zostaną naprawione samym powyższym transferem. Istniejące statyczne zdjęcia `/public/images/portfolio/*-1600.webp` muszą pozostać; importer używa ich jako źródła.

### 4. Migracja i import szkiców

W Termiusie, po zakończeniu transferu:

```bash
cd ~/public_html/kobcode.com
/usr/local/php84-fpm/bin/php artisan config:clear
/usr/local/php84-fpm/bin/php artisan migrate --force
/usr/local/php84-fpm/bin/php artisan kobcode:case-studies
/usr/local/php84-fpm/bin/php artisan view:clear
/usr/local/php84-fpm/bin/php artisan view:cache
/usr/local/php84-fpm/bin/php artisan up
```

Jeżeli migracja lub import zgłosi błąd, nie kontynuuj publikacji. Zachowaj komunikat i sprawdź problem. Jeżeli przerwiesz transfer, przywróć kopię kodu przed `artisan up`.

Importer zapisuje nowe kopie zdjęć w `storage/app/public/portfolio/case-studies/`. Nie przenosi ani nie usuwa istniejących materiałów. Działający symlink `public/storage` z poprzedniego wdrożenia jest nadal potrzebny. Jeżeli go nie ma, wykonaj `/usr/local/php84-fpm/bin/php artisan storage:link`.

### 5. Przejrzyj i opublikuj

W `/admin/projects` przejrzyj pięć wpisów. Import zachowuje niepuste własne opisy, więc w istniejących wpisach świadomie zdecyduj, czy zastąpić je rozbudowaną propozycją z `config/case-studies.php`. Nie kopiuj kodu PHP do edytora — tylko właściwą treść pola. Możesz publikować projekty pojedynczo w panelu.

Po przejrzeniu wszystkich pięciu alternatywnie wykonaj:

```bash
/usr/local/php84-fpm/bin/php artisan kobcode:case-studies --publish
```

Istniejący wpis z relacją `unverified` pozostaje szkicem do poprawy w panelu. Polecenie nie zmienia stanu wdrożenia starszego wpisu. Nie publikuje żadnych innych projektów.

Sprawdź `/realizacje`, pięć adresów szczegółów, dotychczasowy `/realizacje/home-kreator`, powiązane usługi, kontakt, panel i `/sitemap.xml`. Sprawdź zdjęcia na telefonie. Mapa ma aktualizować się automatycznie; szkice wciąż zwracają 404. Nie trzeba ponownie zgłaszać wszystkich starych URL do Google. Nowe publiczne adresy można sprawdzić w inspekcji URL Search Console.

### Powrót do poprzedniej wersji

Przywróć kopie katalogów kodu i poprzedni `public/build/manifest.json` wraz z jego zasobami. Wyczyść config i widoki, odtwórz `view:cache`. Pozostaw nowe opcjonalne kolumny, aby nie utracić ich zawartości. Jeżeli chcesz cofnąć także publikację/treści, użyj panelu albo kontrolowanej kopii bazy — nie stosuj `migrate:fresh`, `db:wipe` ani rollbacku usuwającego kolumny. Nie usuwaj z dysku zdjęć, które mogą być nadal używane przez wpisy.


## Aktualizacja zrzutów Cermax i podpisów danych demonstracyjnych

Przesłane przez właściciela pięć oryginalnych zrzutów zastępuje podglądy publicznego serwisu Cermax: pulpit, produkty, zamówienia, klienci i raporty. Największe pliki mają oryginalne 2047 px szerokości i bezstratny WebP; zgodność pikseli z PNG sprawdzono przy konwersji. Przygotowano także warianty 800/1600 px i srcset, bez wyostrzania, powiększania lub zmiany zawartości ekranu. Oryginały WebP zajmują około 99–158 kB każdy.

Na głównej, w portfolio oraz szczegółach Cermax i aplikacji Olaya widnieje podpis: „Na zrzutach przedstawiono dane demonstracyjne. Nazwy, kwoty i pozostałe informacje służą prezentacji działania systemu.” Podpisy galerii Cermax opisują rzeczywiście pokazany ekran, a nie wcześniejszą publiczną stronę.

Dodatkowe pliki: `public/images/portfolio/cermax-panel-*.webp`, `resources/views/partials/demo-notice.blade.php`. Zmieniono także `home.blade.php`, `partials/project-screenshot.blade.php`, `app/Support/ProjectImage.php` i testy zdjęć. Nie dodano kolejnej migracji ani zależności.

Przy aktualizacji istniejącego etapu 4 do listy transferu dodaj cały `public/images/portfolio/` → `/public_html/kobcode.com/public/images/portfolio/`, zachowując inne zdjęcia. Aktualizuj `resources/views/`, `app/Console/`, `app/Support/`, `config/` oraz nowy lokalny `public/build/`. Nie nadpisuj całego `storage`.

Po transferze i czyszczeniu konfiguracji można odświeżyć wcześniej importowane zdjęcia:

```bash
/usr/local/php84-fpm/bin/php artisan config:clear
/usr/local/php84-fpm/bin/php artisan kobcode:case-studies --refresh-media
/usr/local/php84-fpm/bin/php artisan view:clear
/usr/local/php84-fpm/bin/php artisan view:cache
```

Opcja `--refresh-media` podmienia wyłącznie rozpoznane zdjęcia Cermax z poprzedniego importu, bez zmiany opisów lub publikacji. Zachowuje własne zdjęcia i galerie administratora; w takim przypadku wypisuje komunikat, a nowe materiały można ustawić w panelu. Nowe rekordy nadal powstają jako szkice. Na lokalnej bazie użyj analogicznie `php artisan kobcode:case-studies --refresh-media`, a następnie `php artisan serve --port=8001`.

## Poprawka lokalnego ładowania zdjęć (gałąź kobcode-seo-poprawki)

Zdjęcia okładek, galerii i podglądów z lokalnego dysku publicznego korzystają teraz z adresów `/storage/...`. Poprzednio bezwzględne URL pochodziły z `APP_URL`, więc domyślne `http://localhost:8000` kierowało zdjęcia na inny port niż strona otwarta na `http://127.0.0.1:8001`. Poprawka obejmuje również srcset; zdalne dyski zachowują własne adresy. Ponowne uruchomienie importera odtwarza brakujące pliki wcześniej importowanych materiałów (w tym zdjęcie Walbud), bez nadpisywania istniejących plików i własnych uploadów administratora. Widoki i dane pozostają bez zmian.

Test przeglądarkowy celowo uruchamia aplikację pod innym adresem niż APP_URL, sprawdzając rzeczywiste wczytanie wszystkich obrazów. Dodano regresyjne testy Walbud, galerii i wariantów zdjęć.

Po pobraniu kodu lokalnie:

```bash
php artisan config:clear
php artisan storage:link
php artisan kobcode:case-studies --refresh-media
php artisan view:clear
php artisan serve --port=8001
```

Istniejącego poprawnego linku `public/storage` nie trzeba zastępować. Nie używaj `storage:unlink` ani usuwania zdjęć. Jeżeli oryginalny plik dodany samodzielnie przez panel nie istnieje na danym komputerze, trzeba go skopiować do lokalnego `storage/app/public` lub ponownie wybrać w panelu — repozytorium nie zawiera prywatnych uploadów.

Dla tej poprawki nie potrzeba nowego builda ani nowej migracji. Po zainstalowaniu etapu 4 wystarczy aktualizacja `app/Support/`, `app/Console/` i `resources/views/`, a następnie wyczyszczenie widoków. Konfiguracji hostingu, logowania i produkcyjnego `.env` nie zmieniono.
