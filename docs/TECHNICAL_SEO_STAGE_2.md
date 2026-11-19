# KOBCODE — etap 2: techniczne SEO

Audyt: 19 listopada 2026. Baza: main `310de95`. Zmiany wyłącznie na `feature/kobcode-technical-seo`; bez deploymentu i bez zmian w main.

## Ustalenia z produkcji

Rzeczywiste żądania HTTP wykonano z tego środowiska, niezależnie od raportu Search Console. Stan indeksu Google nie oznacza, że wszystkie endpointy są aktualnie sprawne.

| Adres | Wynik przed zmianami |
| --- | --- |
| Siedem publicznych podstron wskazanych w zadaniu | HTTP 200; unikalne title; index,follow; canonical https://kobcode.com; po jednym H1 |
| /robots.txt | HTTP 200, Allow: /, blokady /admin i /livewire, poprawny adres sitemap |
| /sitemap.xml | HTTP 500 — ogólny Server Error, bez ujawnionego stosu błędu |
| /nie-istnieje-audyt-seo | Prawidłowy HTTP 404 |
| http://kobcode.com/kontakt | HTTP 200 bez przekierowania do HTTPS |
| https://www.kobcode.com/kontakt | HTTP 200 bez przekierowania na domenę główną |
| /kontakt/ | Przekierowanie do /public/kontakt, następnie HTTP 200 |
| /public/kontakt | HTTP 200; canonical prowadzi poprawnie do /kontakt |
| /?utm_source=audit | HTTP 200; canonical bez parametru śledzenia |
| Font WOFF i obraz digital-form-960.webp | HTTP 200, brak Cache-Control w odpowiedziach statycznych |

Bez dostępu do produkcyjnego storage/logs nie można potwierdzić przyczyny błędu sitemap. Testy bazowej implementacji działały wcześniej, a błąd może wynikać również z niepełnego transferu plików lub konfiguracji hostingu. Nowa implementacja usuwa zależność sitemap od pliku widoku i cache Blade; nie maskuje awarii bazy i wymaga sprawdzenia HTTP 200 po wdrożeniu. Jeśli 500 pozostanie, konieczny jest odpowiedni wpis z logu Laravel, bez sekretów i danych osobowych.

## Faktycznie wprowadzone zmiany

- Sitemap generowana przez DOMDocument z prawidłowym escapowaniem XML i Content-Type. Zachowany endpoint, filtr publikacji i rzeczywisty updated_at projektów. Wszystkie siedem publicznych stron w mapie, także kontakt i prywatność. Brak fikcyjnych lastmod dla stałych stron, changefreq i priority.
- Canonicale normalizują ukośnik końcowy. Metadane portfolio opisują cały zakres realizacji, zamiast zamkniętej listy czterech marek.
- Middleware globalny obejmuje nagłówkiem X-Robots-Tag: noindex, nofollow panel, Livewire, /up i odpowiedzi błędów. Nie zmienia uwierzytelniania ani autoryzacji.
- Wyłącznie w production przy SITE_INDEXABLE=true i dla udanych publicznych GET/HEAD: trwały 301 z alternatywnego hosta do APP_URL oraz z niekanonicznej ścieżki (ukośnik końcowy, ujawnione przez rewrite /public). Parametry zapytania są zachowane. Formularze POST, panel, błędy i local/preview nie są kanonizowane. Test symuluje bazowy katalog /public z LH.
- Rzeczywiste width/height obrazów wgrywanych przez panel: rezerwacja miejsca zgodna z proporcjami pliku. W szczegółach projektu usunięto błędne stałe 1200x800, pozostawiono eager loading i dodano fetchpriority=high. Brakujące pliki i zewnętrzny storage nie powodują błędu; brak wymiarów jest wtedy świadomym fallbackiem. Alt ma fallback do nazwy projektu. Bez migracji bazy i zmiany wyglądu CSS.
- CI dodatkowo sprawdza listę tras. Testy obejmują siedem stron, DOM metadanych, JSON-LD, mapę i statusy jej URL, publikację/cofnięcie publikacji projektu, obrazy oraz ograniczenia przekierowań.

## Zachowane poprawne elementy

- Unikalne title i description, canonical i og:url oparte na APP_URL, a nie Host ani UTM. Escapowanie Blade i bezpieczne json_encode.
- Organization, WebSite, WebPage oraz Service na dwóch podstronach usług. Bez ocen, fikcyjnych opinii, cen i innych niepotwierdzonych danych. BreadcrumbList nie dodano: nie ma potrzeby dopisywania oznaczeń bez zmiany prezentacji nawigacji.
- Jedno H1 na publiczną podstronę i logiczne H2/H3. Brak konieczności zmiany tekstów biznesowych lub dodawania nowych podstron.
- Renderowanie treści i linków w HTML po stronie Laravel, niezależnie od JavaScript.
- Responsywne WebP, srcset, lazy loading niżej położonych screenshotów; główny obraz strony głównej pozostaje eager i fetchpriority=high.
- Lokalny font, preload, font-display: swap; brak zewnętrznych fontów i ciężkich bibliotek JS. Obrazy repozytorium mają łącznie ok. 1,5 MB; największy screenshot 1600 px ok. 188 KB.
- Istniejący robots.txt, routing, formularz i panel; domyślne SITE_INDEXABLE=false dla lokalnego środowiska. Nie dodano meta keywords.

## Wydajność: co zmierzono i czego nie

Lokalnie zainstalowano frontend przez npm ci --ignore-scripts i wykonano npm run build. Wynik: CSS 65,88 kB (gzip 15,33 kB), JS 1,53 kB (gzip 0,65 kB). Ostrzeżenie o foncie dotyczy stałej ścieżki /fonts/manrope-latin.woff; plik istnieje w public i produkcja zwraca HTTP 200. Nie jest to brakujący asset.

PageSpeed Insights API dla mobile strony głównej oraz /uslugi/strony-internetowe zwróciło 429 RESOURCE_EXHAUSTED (limit usługi). Nie uzyskano Lighthouse, CrUX ani pomiarów LCP/INP/CLS. Nie deklarujemy poprawy liczbowej ani zaliczenia CWV na podstawie samego kodu. Wymiary obrazów ograniczają znane ryzyko CLS, lecz nie stanowią pomiaru.

Nie ustawiono publicznego cache dla HTML: sesja i CSRF formularza wymagają zachowania poprawnego działania. Brak nagłówków cache statycznych fontów/obrazów odnotowano do konfiguracji LH; nie dodano w ciemno reguł Apache. AVIF nie dodano — obecne WebP są już małe, a nowe warianty bez pomiaru nie mają uzasadnienia.

## Testy

Środowisko edycji nie ma PHP i Composera. Zależności PHP z composer.lock, testy Laravel, kompilację Blade i routing wykonuje GitHub Actions (PHP 8.4), oddzielnie od lokalnego buildu npm. Dokładny wynik i link do workflow będą podane w PR. Nie testowano rzeczywistego logowania na produkcji ani nie wysłano formularza produkcyjnego.

## Wdrożenie na LH.pl — dopiero po akceptacji

Najpierw pobierz sprawdzoną gałąź na Macu; zachowaj lokalne zmiany:

```bash
git fetch origin
git switch feature/kobcode-technical-seo
git pull --ff-only origin feature/kobcode-technical-seo
php artisan view:clear
php artisan serve --port=8001
```

W FileZilli pobierz kopie zmienianych plików z serwera jako backup poza katalogiem publicznym. Prześlij poniższe pliki do odpowiedników w `/public_html/kobcode.com/`:

```text
app/Http/Controllers/SeoController.php
app/Http/Middleware/PublicSeo.php          # nowy plik; utwórz folder Middleware, jeśli nie istnieje
app/Support/Seo.php
app/Support/ProjectImage.php               # nowy plik
bootstrap/app.php
resources/views/portfolio.blade.php
resources/views/project.blade.php
resources/views/partials/compact-project.blade.php
```

Prześlij najpierw nowe klasy i kontroler, następnie widoki; bootstrap/app.php jako ostatni (uruchamia middleware). Nie przesyłaj całych folderów app ani bootstrap. Nie przesyłaj lokalnego bootstrap/cache, vendor, node_modules, .env ani storage. Nie nadpisuj root .htaccess ani public/.htaccess. Istniejący views/sitemap.blade.php może pozostać — kontroler już z niego nie korzysta.

Bez zmian w zależnościach, migracjach, CSS i JS. Na serwerze nie potrzeba composer install, migracji ani npm. Build został przetestowany, lecz nie trzeba przesyłać public/build, bo nie zmieniono frontendowych plików źródłowych ani klas CSS.

W Termiusie:

```bash
cd ~/public_html/kobcode.com
/usr/local/php84-fpm/bin/php artisan config:clear
/usr/local/php84-fpm/bin/php artisan view:clear
/usr/local/php84-fpm/bin/php artisan view:cache
```

APP_URL powinien już wynosić https://kobcode.com, APP_ENV=production, APP_DEBUG=false, SITE_INDEXABLE=true. Nie zmieniaj APP_KEY ani danych bazy. Middleware zadziała dopiero przy production i włączonej indeksowalności.

Sprawdź statusy i Location (bez -L zobaczysz pierwszy redirect):

```bash
curl -I https://kobcode.com/
curl -I https://kobcode.com/robots.txt
curl -I https://kobcode.com/sitemap.xml
curl -I https://www.kobcode.com/kontakt
curl -I https://kobcode.com/public/kontakt
curl -IL https://kobcode.com/kontakt/
curl -I https://kobcode.com/nie-istnieje-audyt-seo
```

Oczekiwane: 200 dla kanonicznych stron/mapy/robots, XML mapy; 301 z www i /public, finalnie 200 /kontakt; 404 dla nieistniejącego adresu. Apache może nadal dodać pierwszy krok /kontakt/ → /public/kontakt; aplikacja zakończy ten łańcuch na /kontakt. Sprawdź zachowanie zdjęć projektu, formularza i panelu. Nie zgłaszaj ponownie wszystkich zaindeksowanych adresów bez potrzeby.

### HTTPS i cache — osobna czynność na hostingu

Aplikacja celowo nie wymusza HTTPS na podstawie niezweryfikowanych nagłówków reverse proxy. Obecnie HTTP odpowiada 200, ale canonical wskazuje HTTPS. W panelu LH należy sprawdzić/włączyć przekierowanie HTTP → HTTPS dla istniejącej domeny; przed zapisaniem potwierdzić certyfikat, bieżące reguły root .htaccess i sposób przekazywania HTTPS do PHP. Nie podano gotowych reguł Apache, ponieważ aktualnego pliku root na serwerze nie pobrano. Sprawdzić potem `curl -IL http://kobcode.com/kontakt`: finał https://kobcode.com/kontakt, bez pętli. Dla statycznych obrazów/fontów można ustawić cache w hostingu po sprawdzeniu obsługi modułów; długie immutable wyłącznie dla zasobów z hashem wersji.

### Rollback

Przywróć z backupu zmienione pliki, bootstrap/app.php jako pierwszy (wyłącza middleware), następnie pozostałe. Wyczyść config i widoki, ponownie wykonaj view:cache. Bez cofania bazy. Nowe nieużywane klasy mogą pozostać. Jeśli po zmianie mapy nadal występuje 500, przeczytaj ostatni odpowiadający mu wyjątek w storage/logs/laravel.log; nie włączaj APP_DEBUG na produkcji.

## Źródła

- https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls
- https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
- https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
- https://web.dev/articles/optimize-cls
