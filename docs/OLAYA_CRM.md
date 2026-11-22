# KOBCODE → OLAYA: Sprzedaż → Zapytania

Integracja formularza `/kontakt` z istniejącą listą zapytań CRM OLAYA. Nie wysyła wiadomości e-mail. Frontend OLAYA nie wymaga przebudowy: korzysta już z `/api/crm/leads`, pokazuje źródło i treść zapytania.

## Gotowe zachowanie

1. KOBCODE sprawdza formularz (w tym CSRF, limit żądań i pole antyspamowe), zapisuje wiadomość lokalnie, a następnie próbuje przekazać ją do OLAYA.
2. OLAYA otrzymuje imię, e-mail, opcjonalny telefon i nazwę firmy oraz pełną treść. Zapytanie ma źródło **Formularz kobcode.com**, etap **Nowe zapytanie** (`new`), wartość 0 i brak przypisanego opiekuna. Firma i treść są zapisane w notatkach.
3. Widoczność pozostaje zgodna z istniejącymi uprawnieniami CRM: początkowo zapytanie widzi superadministrator lub osoba z `crm.leads.view.all`. Uprawniony menedżer przypisuje opiekuna; handlowiec widzi swoje zapytania. Integracja nie rozszerza praw dostępu.
4. KOBCODE zachowuje wiadomość także przy awarii sieci. Status przekazania jest widoczny w panelu KOBCODE: Oczekuje, Przekazano lub Sprawdź. Kod błędu jest widoczny w szczegółach.
5. Każda wiadomość ma stały UUID. OLAYA atomowo zapisuje potwierdzenie przyjęcia, więc ponowienie po utracie odpowiedzi nie tworzy kolejnego zapytania i nie nadpisuje późniejszych notatek handlowca. Powtórzenie identyfikatora z inną treścią zwraca 409. Usunięte przez uprawnioną osobę zapytanie nie jest odtwarzane przy późnym ponowieniu.

## Dwie gałęzie do sprawdzenia

- `mateuszp47/kobcode-strona`: `feature/kobcode-crm-inquiries`, baza `feature/kobcode-demo-apps` (z aktualnym SEO, demo, strzałkami i telefonem).
- `Symtronik/olaya-api`: `feature/kobcode-crm-inquiries`, baza `mateusz-panel`. Ta gałąź zawiera najnowsze funkcje panelu, których nie ma na `main`. Nie zastępuj jej starszym `main`.
- `Symtronik/olaya-dashboard`: bez zmian. Sprawdzono aktualny kod `mateusz-panel`.

Zmiany nie zostały wdrożone na serwery ani połączone z gałęziami bazowymi.

## Konfiguracja (dopiero przy osobno uzgodnionym wdrożeniu)

Najpierw wdraża się odbiornik w OLAYA, następnie nadawcę w KOBCODE. Klucz generuje się samodzielnie, np. lokalnie:

```bash
php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
```

Nie umieszczaj klucza w repozytorium, frontendzie, zrzutach ani wiadomościach. Ten sam nowy klucz wpisz w dwóch plikach `.env`, pod różnymi nazwami. Nie używaj istniejącego klucza formularza olaya.pl.

W `.env` API OLAYA:

```dotenv
KOBCODE_WEBSITE_API_KEY=NOWY_WSPOLNY_KLUCZ
```

W `.env` KOBCODE:

```dotenv
SITE_CONTACT_ENABLED=true
SITE_CONTACT_PHONE="+48 882 137 202"
OLAYA_CRM_ENABLED=true
OLAYA_CRM_URL=https://api.symtronik.pl/api/public/crm/kobcode-leads
OLAYA_CRM_API_KEY=NOWY_WSPOLNY_KLUCZ
```

Adres powyżej odpowiada wcześniej używanej bazie API panelu. Przed uruchomieniem sprawdź, czy wdrażasz nowy endpoint na właściwej instancji OLAYA obsługującej panel. Żadne testy tej zmiany nie wysyłały danych na ten produkcyjny adres.

Na obu aplikacjach, w katalogu zawierającym `artisan`, po przesłaniu plików i przygotowaniu kopii bazy:

```bash
php artisan migrate --force
php artisan optimize:clear
```

W KOBCODE dodatkowo można wykonać `php artisan view:cache`. Nie zastępuj produkcyjnego `.env` plikiem `.env.example`. Nie przesyłaj lokalnych baz SQLite ani `storage`. Nie zmieniaj konfiguracji domeny ani `.htaccess`.

Nowe migracje są addytywne:

- KOBCODE: `2026_11_22_170000_add_olaya_delivery_to_inquiries.php` — status i dane przekazania przy lokalnym zapytaniu.
- OLAYA: `2026_11_22_170000_create_crm_kobcode_receipts.php` — potwierdzenia przyjęcia z unikalnym identyfikatorem i powiązaniem z CRM.

Przy FileZilla aktualizowane katalogi KOBCODE to `app/`, `config/`, `routes/`, `resources/views/` i `database/migrations/`. W API OLAYA: `app/Http/Controllers/Api/Crm/`, `config/`, `routes/`, `database/migrations/` względem katalogu Laravel z `artisan` (w repozytorium jest to podfolder `app/`). Nie potrzeba nowego builda frontendu ani nowych zależności Composer dla tej integracji. Jeśli na serwerze brak wcześniejszych zmian z gałęzi bazowych, najpierw trzeba uwzględnić ich osobne wymagania wdrożenia.

## Ponawianie i obsługa awarii

Pierwsza próba następuje bezpośrednio po zapisie formularza (limit połączenia 3 s, całego żądania 8 s). Ponowienia po błędzie sieci, 408, 425, 429 lub 5xx: po 1 minucie, 5 minutach, 15 minutach, potem co godzinę, maksymalnie 8 prób. Odpowiedzi 4xx niewymagające ponawiania, błędna konfiguracja i przekierowania zatrzymują automatyczne próby. Wiadomość pozostaje lokalnie. Nie zapisujemy treści formularza, klucza ani odpowiedzi API w logach integracji.

Włącz istniejący Laravel Scheduler w KOBCODE (cron co minutę, z właściwą ścieżką PHP 8.4+ na LH.pl):

```bash
cd /home/platne/serwer323746/public_html/kobcode.com
/usr/local/php84-fpm/bin/php artisan schedule:run
```

Nie dodawaj drugiego identycznego zadania, jeśli cron już działa. Ręczne przetworzenie oczekujących zapytań:

```bash
php artisan kobcode:deliver-inquiries
```

Ponowienie błędu po naprawieniu konfiguracji lub świadome przekazanie jednego wcześniejszego zapytania (zastąp 123 jego identyfikatorem z KOBCODE):

```bash
php artisan kobcode:deliver-inquiries --inquiry=123
```

Istniejące przed aktualizacją wiadomości nie są masowo eksportowane. Powtórzenie komendy dla już przekazanego rekordu nie wysyła go ponownie. Nadawca wymaga HTTPS poza lokalnym środowiskiem; nie podąża za przekierowaniami. Dostęp do odbiornika chroni osobny klucz oraz limit żądań. Cache KOBCODE musi obsługiwać blokady atomowe (np. używany plikowy lub bazodanowy cache Laravel).

## Sprawdzenie lokalne

W już działającym lokalnym KOBCODE, po pobraniu gałęzi:

```bash
git fetch origin
git switch feature/kobcode-crm-inquiries
git pull --ff-only origin feature/kobcode-crm-inquiries
php artisan migrate
php artisan optimize:clear
php artisan serve --port=8001
```

W lokalnym API OLAYA pobierz jego gałąź o tej samej nazwie. W katalogu `panel-olaya/olaya-api/app` (lub własnym folderze API z `artisan`) wykonaj lokalną migrację, ustaw nowy wspólny klucz i uruchom `php artisan serve --port=8000`. Lokalny URL nadawcy: `http://127.0.0.1:8000/api/public/crm/kobcode-leads`, `APP_ENV=local`. Panel Nuxt uruchamia się dotychczasowym `npm run dev`. Nigdy nie konfiguruj lokalnych testów do produkcyjnej bazy lub produkcyjnego odbiornika.

Testy KOBCODE: `php artisan test`. Testy API OLAYA: `php artisan test --filter='KobcodeCrmLeadApiTest|PublicCrmLeadApiTest|CrmLeadApiTest'`. Testy automatyczne używają fikcyjnych danych i odizolowanej bazy SQLite; zewnętrzne wywołania nadawcy są atrapami HTTP.
