# KOBCODE — uruchomienie i aktualizacja

Wymagania: PHP 8.3+, Composer, Node 22.12+ lub 24, SQLite.

## Pierwsze uruchomienie

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan storage:link
npm ci
npm run build
php artisan kobcode:admin
php artisan serve --port=8001
```

Panel: http://127.0.0.1:8001/admin. Konto tworzysz interaktywnie komendą kobcode:admin. Nie ma domyślnego hasła ani publicznej rejestracji.

## Aktualizacja poprzedniej wersji

Zatrzymaj serwer przez Control+C. Jeśli Git zgłosi konflikt lokalnych zmian, nie nadpisuj ich.

```sh
git pull origin main
composer update filament/filament --with-all-dependencies
php artisan migrate
php artisan storage:link
npm ci
npm run build
php artisan optimize:clear
php artisan kobcode:admin
php artisan serve --port=8001
```

Komendę tworzenia administratora wykonaj tylko raz dla danego konta. Jeśli dowiązanie storage już istnieje, nie twórz go ponownie. Nie kopiuj ponownie .env i nie generuj ponownie APP_KEY.

## Zakres

Panel Filament: edycja realizacji, cel/problem/zakres/rozwiązanie/rezultat, zdjęcie i jego opis, kolejność i SEO, publikacja i szkice; odczyt i usuwanie zapytań oraz oznaczanie ich jako przeczytane.

Projekt pojawia się publicznie po ustaleniu relacji własny/kliencki, potwierdzeniu praw i treści oraz włączeniu publikacji. Pliki na dysku publicznym są dostępne po URL także dla szkiców — wgrywaj wyłącznie zatwierdzone materiały, nigdy poufne ekrany.

Walbud to projekt dla klienta, Olaya to firma powiązana; wszystkie trzy rozwiązania wykonano od podstaw. Użytkownik zezwolił na nazwy, logotypy i zatwierdzone zrzuty. Do sprawdzenia pozostają status wdrożeń, szczegółowe funkcje i rezultaty. Szkice dodasz komendą `php artisan kobcode:portfolio-drafts`; szczegóły w docs/PORTFOLIO.md. Nie publikujemy fikcyjnych rezultatów ani opinii.

## Formularz

Zapytania zapisują się w bazie i są dostępne w panelu. Formularz nie wysyła powiadomień e-mail. Lokalnie dodaj w .env:

```dotenv
SITE_CONTACT_ENABLED=true
```

Potem `php artisan config:clear`. SITE_CONTACT_EMAIL i SITE_CONTACT_PHONE pozostają puste do zatwierdzenia. Przed uruchomieniem publicznym uzupełnij informacje o prywatności o dostawców, podstawy prawne, retencję i prawa osób. Obecna strona prywatności jest wersją roboczą.

SITE_INDEXABLE=false pozostaw do gotowości produkcyjnej. Nie dodano analityki ani reklamowych narzędzi śledzących. Formularz ma walidację, ochronę CSRF, honeypot i limit prób. Nie umieszczaj prawdziwych zapytań ani bazy w repozytorium.

## Weryfikacja

GitHub Actions sprawdza instalację zależności, testy publicznych stron, publikacji i panelu oraz formularza, kompilację Blade i budowanie CSS/JS. Weryfikacja wizualna na urządzeniach pozostaje oddzielnym krokiem.
