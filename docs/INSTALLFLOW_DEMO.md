# InstallFlow — lokalna demonstracja KOBCODE

InstallFlow jest fikcyjną marką i projektem demonstracyjnym KOBCODE. Nie jest realizacją wdrożoną dla rzeczywistej firmy. Nie należy wpisywać do niego prawdziwych danych.

## Uruchomienie na macOS

W katalogu istniejącego repozytorium, po zapisaniu własnych zmian:

```bash
git fetch origin
git switch feature/kobcode-demo-apps
git pull --ff-only origin feature/kobcode-demo-apps
composer install
npm ci
npm run build
```

Zachowaj istniejący lokalny `.env` i `APP_KEY`. Jeśli dopiero klonujesz projekt:

```bash
git clone --branch feature/kobcode-demo-apps https://github.com/mateuszp47/kobcode-strona.git
cd kobcode-strona
composer install
cp .env.example .env
php artisan key:generate
npm ci
npm run build
```

W lokalnym `.env` ustaw `APP_ENV=local`, `APP_URL=http://127.0.0.1:8001`, `DB_CONNECTION=sqlite`, `SESSION_DRIVER=file`, `CACHE_STORE=file`. Usuń lub zakomentuj lokalne `DB_DATABASE`, jeśli wskazuje inną bazę: domyślny plik to `database/database.sqlite`. Nie kopiuj produkcyjnego `.env` ani danych produkcyjnych. PHP musi obsługiwać `pdo_sqlite`; projekt wymaga PHP 8.4+.

```bash
touch database/database.sqlite
php artisan optimize:clear
php artisan migrate
php artisan storage:link
php artisan demo:installflow
php artisan serve --host=127.0.0.1 --port=8001
```

`migrate` powyżej dotyczy wyłącznie lokalnej bazy KOBCODE. Oddzielny schemat demonstracji tworzy `demo:installflow`, bez migracji ani rekordów w bazie KOBCODE. Jeśli lokalny `storage:link` już istnieje, nie trzeba go odtwarzać.

- Katalog aplikacji: http://127.0.0.1:8001/demo
- Opis InstallFlow: http://127.0.0.1:8001/demo/installflow/opis
- Działająca aplikacja: http://127.0.0.1:8001/demo/installflow

Nie trzeba tworzyć konta ani uruchamiać kolejki. Do ciągłej pracy nad CSS/JS można w drugim terminalu uruchomić `npm run dev`. Po jego zakończeniu wróć do `npm run build` i usuń ewentualny `public/hot`.

## Co działa

- Dashboard: statystyki i wykres ostatnich sześciu miesięcy, najbliższe montaże, aktywność, podsumowanie zespołu.
- CRM: wyszukiwanie i filtrowanie klientów, dodawanie, edycja, szczegóły, historia kontaktów, powiązane oferty i zlecenia. Usuwanie klienta dostępne tylko bez ofert i zleceń.
- Oferty: wielopozycyjny formularz, ilości ułamkowe, ceny netto, VAT 23%, suma brutto, edycja, statusy, jednorazowa konwersja zaakceptowanej oferty w zlecenie.
- Dokument oferty: osobny widok do druku i przycisk otwierający drukowanie przeglądarki, w tym „Zapisz jako PDF”. Nie ma serwerowego generatora PDF ani wysyłki dokumentów.
- Zlecenia: tworzenie, edycja, klient, pracownik, termin, kwota, pięć statusów, notatki i historia zmian. W zleceniu z oferty klient i kwota pozostają zgodne z ofertą źródłową.
- Kalendarz: miesiące, filtr pracownika, szczegóły wydarzeń, dodawanie z wybranego dnia, planowanie także zleceń bez terminu oraz zmiana terminu i pracownika.
- Zespół: lista, profil, dostępność, dodawanie i edycja, przypisane zlecenia i harmonogram. Są to rekordy demo, nie konta logowania.
- Raporty: wartości z zapisanych ofert i zleceń, liczby według statusów, przychód demonstracyjny i praca zespołu. „Przychód” oznacza sumę brutto zakończonych zleceń, nie zaksięgowane płatności.

Seeder zawiera 30 klientów, 24 zlecenia, 18 ofert i 6 pracowników. Daty są względem dnia utworzenia szablonu. Wszystkie nazwy i dane kontaktowe są fikcyjne. Zmiana statusu lub wartości wpływa na statystyki.

## Izolacja danych i przywracanie

Każda sesja Laravel otrzymuje losowy token 256-bitowy zapisany po stronie serwera. Token nie jest pobierany z parametrów URL ani formularzy. Sesja ma własny fizyczny plik SQLite skopiowany z dziennego szablonu. Żaden moduł nie korzysta z modeli użytkowników ani danych biznesowych KOBCODE.

- Połączenie: `installflow`, niezależne od `database.default`.
- Pliki: `storage/app/private/demo/installflow/`, poza katalogiem publicznym, bazy z prawami `0600`.
- Czas demonstracji: 120 minut od jej rozpoczęcia. Po wygaśnięciu powstaje nowa przestrzeń.
- Przycisk „Przywróć dane” usuwa wyłącznie klucze demo z bieżącej sesji. Nie wylogowuje użytkownika z KOBCODE i nie zmienia danych innych sesji.
- Współbieżne zapisy są serializowane blokadą sesji i pliku. Konwersja oferty jest idempotentna.
- Sprzątanie: `php artisan demo:installflow --prune`, również zaplanowane co godzinę w Laravel Scheduler. Usuwa nieużywane pliki starsze niż czas sesji + 60 minut. Aktywne blokady są respektowane. Małe pliki blokad pozostają, aby nie dopuścić do wyścigu przy ich ponownym tworzeniu.
- Nowy szablon powstaje codziennie lub po zwiększeniu `demo.version`. Nie wpływa na działające sesje przed ich wygaśnięciem.

Walidacja odbywa się na serwerze, kwoty są liczone w groszach, zapisy wymagają CSRF, treści są escapowane przez Blade. Ograniczenia: 200 klientów, ofert i zleceń, 40 pracowników, 400 kontaktów na przestrzeń, do 20 pozycji oferty. Limity żądań mają osobne liczniki dla odczytu, zapisu i resetu. Nie ma połączeń z pocztą, SMS, płatnościami ani zewnętrznymi systemami.

## SEO i dotychczasowy serwis

Widoki i zasoby aplikacji mają osobne wejścia Vite i własny layout. Interfejs wewnętrzny zawsze zwraca `noindex, nofollow` oraz `private, no-store`. Jedynie `/demo` i `/demo/installflow/opis` mogą w przyszłości wejść do mapy witryny. Domyślnie ich indeksowanie jest wyłączone.

Gałąź powstała z `kobcode-seo-poprawki` (`d476b883`), ponieważ `main` nie zawierał najnowszych zmian SEO i portfolio. Przy przeglądzie PR trzeba uwzględnić tę różnicę; wcześniejsze prace nie zostały cofnięte.

## Weryfikacja

```bash
php artisan test
php artisan view:cache
php artisan route:list --path=demo
npm run build
```

`tests/Feature/Demo/InstallFlowTest.php` sprawdza CRUD, kwoty, walidację, konwersję, historię, kalendarz, raporty, fizyczną izolację sesji, reset, wygaśnięcie, sprzątanie, CSRF, indeksowanie i blokadę produkcyjną. Istniejące testy KOBCODE nadal są uruchamiane.

GitHub Actions dodatkowo uruchamia `tests/browser/installflow.mjs`: faktyczny obieg przez formularze, dwie niezależne sesje przeglądarkowe, PDF z widoku druku, menu mobilne i dziesięć widoków przy szerokościach 390, 768 i 1440 px. Zrzuty i PDF są artefaktami przebiegu CI. Testy przeglądarkowe korzystają z jednorazowej lokalnej bazy, nie z serwera produkcyjnego.

## Przygotowanie przyszłego udostępnienia publicznego

Ta gałąź nie jest wdrażana na LH.pl. W środowisku `production` demo pozostaje wyłączone. Przed osobno zatwierdzonym udostępnieniem trzeba:

1. Potwierdzić `pdo_sqlite`, `flock`, zapis do prywatnego storage oraz cache obsługujący blokady sesji; uwzględnić zewnętrzny storage przy wielu serwerach. Obecna architektura zakłada jeden serwer i lokalny dysk.
2. Skonfigurować cron `php artisan schedule:run` co minutę i sprawdzić działanie sprzątania. Ustalić limit dysku oraz monitorowanie liczby plików i ruchu, wykonać próbę obciążeniową. Same limity IP nie zastępują ochrony przed masowym tworzeniem nowych sesji.
3. Wykluczyć `/demo/installflow` i podstrony aplikacji z cache CDN/proxy, stosować HTTPS i bezpieczne ciasteczka. Sprawdzić ochronę całego prywatnego `storage` przy obecnym sposobie kierowania domeny na LH.pl.
4. Włączyć świadomie `DEMO_APPS_ENABLED=true` oraz `DEMO_APPS_ALLOW_PUBLIC=true`, przebudować zasoby i odświeżyć cache konfiguracji. Obie flagi są wymagane poza `local/testing`.
5. Dopiero po akceptacji publicznej prezentacji ustawić `DEMO_PAGES_INDEXABLE=true` przy `SITE_INDEXABLE=true`. Wewnętrzny interfejs pozostaje wyłączony z indeksowania.
6. Zaktualizować informację o prywatności o sesję demonstracyjną i retencję danych, sprawdzić na środowisku testowym cały workflow i istniejący formularz KOBCODE.

Nie trzeba wykonywać migracji InstallFlow na produkcyjnej bazie MySQL. Nie uruchamiaj seedera InstallFlow na domyślnym połączeniu. Nie udostępniaj plików SQLite ani tokenów. Funkcja nie zmienia konfiguracji logowania panelu KOBCODE.
