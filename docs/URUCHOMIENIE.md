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
