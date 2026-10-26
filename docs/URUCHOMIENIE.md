# KOBCODE — pierwsza wersja

Wymagania: PHP 8.3+, Composer, Node zgodny z Vite 8 (20.19+ lub 22.12+), SQLite.

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
php artisan serve
```

Otwórz http://127.0.0.1:8000. Do edycji CSS/JS uruchom `npm run dev` w osobnym procesie.

## Stan

Pierwsza wersja zawiera stronę główną, dwie usługi, o firmie, kontakt i stronę portfolio oczekującą na materiały. README użytkownika pozostaje zachowane.

Kontakt e-mail i telefon są pobierane z SITE_CONTACT_EMAIL i SITE_CONTACT_PHONE. Pozostają puste do zatwierdzenia. Nie ma jeszcze formularza wysyłającego wiadomości ani panelu Filament. Nie publikować jako gotowej strony sprzedażowej przed ich podłączeniem.

SITE_INDEXABLE=false celowo blokuje indeksowanie przez meta robots. Przed produkcją: aktualne dane spółki, zatwierdzone kontakty, materiały portfolio, SMTP i polityka prywatności dopasowana do formularza, testy mobilne/dostępności, sitemap i właściwy APP_URL/HTTPS. Zmienić SITE_INDEXABLE dopiero po gotowości.

Walbud i Olaya: nieustalone autorstwo całości, relacja własny/kliencki, status wdrożeń i zgody na materiały. Nie publikować twierdzeń o funkcjach bez weryfikacji. Filament i docelowy formularz są kolejnym etapem, nie funkcją tej wersji.

## Aktualizacja — panel i formularz

Po pobraniu zmian: `composer install`, `php artisan migrate`, `php artisan storage:link`, `npm ci`, `npm run build`, `php artisan optimize:clear`. Następnie `php artisan kobcode:admin` — podaj własny e-mail i hasło. Panel: `/admin`. Nie ma domyślnego konta ani hasła.

Panel obsługuje realizacje, zdjęcie, opis alternatywny, kolejność i SEO oraz zapytania. Realizacja pojawia się publicznie po ustaleniu relacji własny/kliencki, potwierdzeniu materiałów i włączeniu publikacji. Zdjęcia na dysku publicznym są dostępne po URL także dla szkiców — wgrywaj wyłącznie materiały już zatwierdzone, nigdy poufne ekrany.

Formularz zapisuje zapytania w bazie, nie wysyła e-maili. Lokalnie włącz `SITE_CONTACT_ENABLED=true` w `.env`, następnie `php artisan config:clear`. Przed uruchomieniem publicznym uzupełnij informacje o prywatności o faktycznych dostawców, podstawy prawne, retencję i prawa osób. SITE_INDEXABLE pozostaje false.

Po zmianie danych kontaktowych w `.env` wyczyść cache konfiguracji. Wiadomości czytaj i usuwaj w panelu. Nie umieszczaj prawdziwych zapytań ani bazy danych w repozytorium.
