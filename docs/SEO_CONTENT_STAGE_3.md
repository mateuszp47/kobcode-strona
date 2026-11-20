# Etap 3 — frazy i treści KOBCODE

19 listopada 2026. Gałąź: feature/kobcode-seo-content. main nadal wskazuje 310de95 i nie ma etapu 2. Nową gałąź utworzono po pobraniu main i uzupełniono fast-forwardem do feature/kobcode-seo-growth (713b3f5), która zawiera etap 2 i pięć ofert. Nie cofnięto optymalizacji ani nie zmieniono main. Ten PR obejmuje także niepołączone jeszcze zmiany poprzednich PR #1 i #2.

## Analiza pięciu konkurentów

Źródła odczytano w tym etapie, bez ponownego audytu technicznego KOBCODE. Nie zweryfikowano pozycji tych firm w Google, wolumenów ani trudności fraz. Ceny, terminy i rezultaty konkurentów nie są danymi o KOBCODE.

| Firma i źródło | Obserwacja | Wniosek dla KOBCODE |
| --- | --- | --- |
| Iguana Studio — https://iguanastudio.pl/tworzenie-stron-www-radom | Lokalny tytuł i H1, osobne oferty stron, portali i aplikacji. Projekty z regionu, pytania zakupowe i droga do wyceny. | Lokalizacja w istniejącej ofercie WWW, bez tworzenia stron pod kolejne miasta; wyjaśnienie zakresu przed wyceną. |
| Siła w precyzji — https://silawprecyzji.pl/ | H1 „Strony internetowe Radom”, portfolio z opisem wykonanej pracy i przejściem do case study. | Odwołanie do konkretnych zastosowań Walbud i ParkStop zamiast niepotwierdzonych obietnic skuteczności. |
| Webmode — https://webmode.pl/oferta/aplikacje-webowe | Aplikacje i systemy dedykowane opisane według procesów, paneli, ról oraz integracji; przykłady i etapy współpracy. | Własne, konkretne pytania o wybór aplikacji i role w panelu, związane z potwierdzonym zakresem Olaya. |
| H&D Interactive — https://had.pl/oferta | Rozdzielenie stron, CMS, CRM i aplikacji; zakres, proces i wezwanie do wyceny przy usłudze. | Jasne nazwy usług i odnośniki do odpowiedniego zakresu; bez kopiowania szerokiej oferty konkurenta. |
| w3dni — https://w3dni.pl/programy-na-zyczenie | Oprogramowanie według zastosowania: małe aplikacje, narzędzia pracowników, panele kontrahenta i API. | Wyjaśnić różnicę między stroną a aplikacją i pomóc klientowi wybrać pierwszy proces do wdrożenia. |

Wniosek to dopasowanie treści do zadań kupującego i dowodów z portfolio, nie twierdzenie, że wszyscy konkurenci pomijają te zagadnienia. KOBCODE nie przyjmuje cudzych opinii, cen, technologii ani statystyk jako własnych.

## Mapa intencji i fraz

| Podstrona | Jedna fraza główna | Frazy powiązane |
| --- | --- | --- |
| / | dedykowane oprogramowanie dla firm | oprogramowanie na zamówienie, systemy dla firm, aplikacje webowe |
| /uslugi/strony-internetowe | tworzenie stron internetowych Radom | strony internetowe dla firm, projektowanie stron internetowych, katalog produktów, edycja treści |
| /uslugi/aplikacje-internetowe | aplikacje internetowe na zamówienie | aplikacje webowe, panele administracyjne, obsługa zleceń, uprawnienia użytkowników |
| /uslugi/platformy-b2b | tworzenie platform B2B | platforma dla hurtowni, zamówienia B2B, konta kontrahentów, indywidualne ceny |
| /uslugi/automatyzacja-procesow | automatyzacja procesów biznesowych | integracje API, wymiana danych, automatyzacja obsługi zamówień |
| /uslugi/modernizacja-stron-internetowych | modernizacja stron internetowych | przebudowa strony firmowej, przeniesienie treści i adresów, katalog produktów |

To hipotezy fraz wynikające z oferty i analizy stron, bez wymyślonych wolumenów. Weryfikacja skuteczności: zapytania, strony docelowe i kliknięcia w GSC oraz jakość otrzymanych zapytań. Sama indeksacja ani wdrożenie treści nie gwarantują pozycji.

## Rzeczywiste zmiany względem growth 713b3f5

- Strona główna: krótszy tytuł SEO, ogólny opis bez zamkniętej listy marek, wyraźne odniesienie do dedykowanego oprogramowania i jego zastosowań w leadzie. Zachowano H1 „Oprogramowanie na zamówienie”; zmieniono H2 sekcji oferty. Dodano pytanie o różnicę między stroną a aplikacją.
- Pięć usług: dopracowane title/description; H1 z nazwą usługi; H2 opisujące konkretne zastosowania. Dodano po jednym pytaniu pomagającym przygotować zapytanie: edycja produktów, wybór aplikacji, indywidualne ceny, pierwsza automatyzacja i zachowanie adresów przy przebudowie.
- Panele administracyjne: zachowane w ofercie aplikacji, z własnym nagłówkiem i stabilną kotwicą #panele-administracyjne. Link z głównej prowadzi bezpośrednio do tego opisu. Drugi odnośnik kieruje z głównej do modernizacji.
- Zachowano wcześniej przygotowane powiązania usług, kontaktu i projektów. W tym etapie nie edytowano opisów ani zdjęć portfolio.
- Nie dodano nowych adresów ponad pięć istniejących na growth. Oddzielne strony dedykowanego oprogramowania i paneli dublowałyby obecną stronę główną oraz aplikacje. Trzy adresy nieobecne jeszcze na main (B2B, automatyzacja, modernizacja) zostały odziedziczone, a nie odtworzone.
- Nie zmieniono CSS, JavaScript, formularza, logowania, routingu, danych ani mechanizmów technicznego SEO z etapu 2. Lokalny kontekst Radomia na stronie WWW i „O firmie” odziedziczono z growth; aktualny brief wprost obejmuje frazę lokalną i działalność ogólnopolską.

## Pliki tego etapu

- config/offer.php
- resources/views/home.blade.php
- resources/views/service.blade.php
- tests/Feature/ContentNavigationTest.php
- docs/SEO_CONTENT_STAGE_3.md

Lista zmian całego PR względem main obejmuje również wcześniejsze etapy. Nie utożsamiać jej z listą plików do ponownego wysłania, jeżeli growth jest już poprawnie wdrożony.

## Weryfikacja

Dodatkowy test przechodzi po linkach wszystkich dziesięciu publicznych podstron i sprawdza strony docelowe oraz kotwice (w tym nowy odnośnik do paneli). Istniejące testy nadal kontrolują metadane, H1, JSON-LD, mapę, publikację projektów, formularz i panel. Wynik końcowego workflow podany w PR. Build wykonano również lokalnie. Brak lokalnego PHP/Composera w środowisku edycji: testy Laravel i kompilacja Blade są wykonywane w GitHub Actions na PHP 8.4.

Nie wykonano ponownego audytu produkcyjnego, zewnętrznego Rich Results Test ani pomiarów CWV — nie były celem tego etapu. Nie wykonywano nowego porównania wizualnego ani testu na fizycznym telefonie. Nie dodano nowych szablonów ani reguł CSS: usługi nadal używają istniejącego układu, który przechodzi do jednej kolumny na małych ekranach. Bez deklaracji gwarantowanych pozycji.

## Wdrożenie na LH.pl po akceptacji

### Jeśli growth 713b3f5 i etap 2 są już wdrożone

W istniejącym folderze projektu na Macu:

```bash
git fetch origin
git switch feature/kobcode-seo-content
git pull --ff-only origin feature/kobcode-seo-content
php artisan view:clear
php artisan serve --port=8001
```

W FileZilli wykonaj kopię trzech zastępowanych plików poza katalogiem serwowanym przez WWW. Wyślij z lokalnej gałęzi:

| Plik | Dokładne miejsce na LH.pl |
| --- | --- |
| config/offer.php | /public_html/kobcode.com/config/offer.php |
| resources/views/home.blade.php | /public_html/kobcode.com/resources/views/home.blade.php |
| resources/views/service.blade.php | /public_html/kobcode.com/resources/views/service.blade.php |

Następnie w Termiusie:

```bash
cd ~/public_html/kobcode.com
/usr/local/php84-fpm/bin/php artisan config:clear
/usr/local/php84-fpm/bin/php artisan view:clear
/usr/local/php84-fpm/bin/php artisan view:cache
```

Nie potrzeba migracji, Composera ani nowego public/build: zmieniono teksty i kotwicę, bez nowych klas CSS, zmian JS i zależności. npm run build służy w tym etapie do weryfikacji. Nie nadpisuj .env, .htaccess, vendor, storage i bootstrap/cache. Jeśli wcześniej świadomie korzystano z config:cache, odtwórz go po kontroli aktualnych wartości konfiguracji; nie włączaj nowych ustawień środowiska w ciemno.

Sprawdź stronę główną, pięć usług, link „Poznaj panele administracyjne dla firm” oraz formularz i panel bez wysyłania rzeczywistych danych. Nowych adresów nie przybyło, więc nie trzeba ponownie zgłaszać całej witryny w Search Console. Rollback: przywrócenie trzech kopii oraz config:clear/view:clear/view:cache; bez cofania bazy.

### Jeśli na serwer trafił tylko etap 2 albo wersja starsza

Najpierw zastosuj kompletną listę odziedziczonych plików i procedurę z docs/SEO_GROWTH_PL.md, ale wszystkie pliki pobierz z feature/kobcode-seo-content. Wtedy wymagane są także config/offer.php, routes/web.php, szablony, WOFF2 oraz lokalny build i wysłanie public/build. Samo przesłanie trzech plików z tabeli nie wystarczy, jeśli nowe trasy nie są jeszcze zainstalowane. Nie wdrażano niczego automatycznie i nie zmieniano main.
