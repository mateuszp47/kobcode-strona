# KOBCODE · widoczność organiczna i zapytania ofertowe

**19 listopada 2026.** Na stronie produkcyjnej siedem sprawdzonych podstron działa, ale mapa witryny wskazana robotom odpowiada błędem 500. Pierwszym kryterium odbioru na LH.pl jest odpowiedź 200 z prawidłowym XML. Najlepszy punkt wyjścia do zdobywania zapytań stanowią trzy konkretne potrzeby kupujących — katalog z zapytaniem o produkt, zakupy hurtowe po cenach kontrahenta oraz panel do prowadzenia zleceń. Do każdej KOBCODE może już pokazać działanie rozwiązania na przykładzie z portfolio.

[Pull Request #2 do main](https://github.com/mateuszp47/kobcode-strona/pull/2) obejmuje wcześniejszy [PR techniczny #1](https://github.com/mateuszp47/kobcode-strona/pull/1). Baza to `main` w wersji `310de95`. W gałęzi `feature/kobcode-seo-growth` są również trzy commity kończące się na `f514561`. Kod oferty przetestowano w wersji `997f4d5`. Publikacja na LH.pl wymaga decyzji właściciela.

## Stan produkcji i priorytety

| Priorytet | Obserwacja z 19 listopada | Działanie i kryterium odbioru |
| --- | --- | --- |
| P0 | `robots.txt` zwraca 200, dopuszcza publiczne strony i wskazuje `/sitemap.xml`. Sama mapa zwraca 500. | PR zawiera generator XML i test publikacji projektów. Po zatwierdzonym transferze otworzyć mapę na LH. Przy dalszym 500 ustalić wyjątek z logu serwera. Przyczyna produkcyjna pozostaje niepotwierdzona. |
| P1 | Każda z siedmiu stron wskazanych w briefie zwraca 200, ma jeden H1, canonical w domenie HTTPS i dyrektywę index. Nieistniejąca ścieżka zwraca 404. | Zachować adresy. Dołączyć trzy nowe strony z odrębnymi potrzebami zakupowymi. Porównać adres kanoniczny zadeklarowany w HTML z wybranym przez Google. |
| P1 | HTTP i HTTPS z www dla `/kontakt` odpowiadają 200. `/kontakt/` kieruje do `/public/kontakt`, gdzie serwer zwraca 200. | Kod normalizuje alternatywny host i publiczną ścieżkę aplikacji. Wymuszenie HTTPS oraz pierwszy skok przepisywany przez Apache wymagają potwierdzenia w konfiguracji domeny na LH. |
| P1 | Oferta przed zmianą miała dwie docelowe strony. Istniał szczegółowy opis CERMAX, ale osoba szukająca platformy hurtowej nie miała własnego adresu usługi. | Oferta z pięcioma adresami, przykładami realizacji, pytaniami przed wyceną i bezpośrednią drogą do kontaktu. |
| P2 | HTML na produkcji ma prywatny cache. HTML, sprawdzony CSS i JS są kompresowane gzip. W odpowiedziach badanego CSS, JS, fontu i obrazu brak jawnego Cache-Control. | Najpierw monitorować wydajność po wdrożeniu. Na hostingu rozważyć długi cache dla wersjonowanych plików build/assets i krótszy dla grafik o stałych nazwach. Zachować poprawność sesji i formularza. |

**Punkt odniesienia.** Siedem adresów w indeksie, zero wykluczonych w raporcie i 3–4 dni działania Search Console pochodzą z briefu właściciela. Odpowiedzi HTTP zweryfikowano niezależnie. Nie było dostępu do pełnych zapytań GSC, liczby kwalifikowanych leadów ani danych Ahrefs, Semrush czy Keyword Plannera. Na tym etapie nie można przypisać zmianom wzrostu kliknięć lub przychodów.

## Konkurencja w czterech grupach

Badanie objęło wyniki wyszukiwania dla tworzenia stron w Radomiu, projektowania stron dla firm w Polsce, aplikacji na zamówienie i platform do obsługi zamówień B2B. Bezpośrednie zapytania do google.pl z ustawieniami Polski i języka polskiego dla wszystkich czterech grup zwróciły HTTP 429. Poniższe firmy znaleziono w dostępnej wyszukiwarce internetowej i sprawdzono ich strony. Kolejność tabeli jest tematyczna. Dokładnego miejsca w polskim Google, wyników mapowych ani wolumenu fraz nie zweryfikowano. Przykłady i wyniki opisane przez konkurencję stanowią jej własne deklaracje.

| Segment i firma | Organizacja oferty, realizacje, linkowanie i wiarygodność | Co z tego wynika dla KOBCODE |
| --- | --- | --- |
| Radom · [Siła w precyzji](https://silawprecyzji.pl/) | Lokalny nagłówek otwiera portfolio. Linki kierują do opisów m.in. kancelarii i wielojęzycznego katalogu. Przy projektach podano zakres prac. | Na stronie WWW kierować klienta wprost do katalogu Walbud lub rezerwacji ParkStop, zależnie od zadania. |
| Radom · [Iguana Studio](https://iguanastudio.pl/tworzenie-stron-www-radom) | Strona dla Radomia podaje projekt Todom, przykłady z Mazowsza, warunki cenowe i termin deklarowany przez wykonawcę. | Podać rzeczywistą siedzibę i model współpracy. Własne ceny lub terminy publikować dopiero po potwierdzeniu typowego zakresu. |
| Radom · [GQ Image](https://www.gqim.com/) | Siedziba i dane kontaktowe są widoczne obok oferty stron i sklepów. Wyróżnione realizacje na analizowanej stronie prowadzą też do brandingu. | Przy usłudze programistycznej pokazać konkretną funkcję wykonanego serwisu, np. sposób edycji katalogu. |
| Polska · [Ministerstwo Reklamy](https://www.ministerstworeklamy.pl/strona-internetowa/) | Z oferty można przejść do cennika i nazwanych realizacji. Osobne sekcje tłumaczą wpływ projektu graficznego, materiałów i funkcji na wycenę. | Wyjaśnić różnicę między liczbą produktów a liczbą szablonów i jakością materiałów. To pomaga klientowi przygotować sensowne zapytanie. |
| Polska · [DPL Agency](https://www.dplagency.pl/projektowanietworzeniestron) | Oferta prowadzi do wyceny i portfolio. Karty producenta, sieci sklepów i banku pokazują branżę i wykonany zakres CMS. | Pozwolić przejść z opisu projektu do usługi. Na przykład katalog z panelem edycji i platforma hurtowa mają różnych nabywców. |
| Polska · [H&D Interactive](https://had.pl/oferta) | Oferta rozdziela strony firmowe, CMS, CRM i aplikacje. Sekcje zawierają zakres, przebieg współpracy i odnośniki do wyceny konkretnej usługi. | Wyodrębnić intencje z dowodem wykonania. Platformie B2B dać własny adres, a panel administracyjny objaśnić w aplikacjach. |
| Aplikacje · [Complaia Systems](https://www.complaia.systems/oprogramowanie-dedykowane) | Punktem wejścia są ręczna praca i ograniczenia gotowych narzędzi. Obok opisu usługi są odnośniki do realizacji i wyceny oraz formularz. | Poprosić o opis jednego zlecenia i ról pracowników. Ogranicza to trudność przygotowania pierwszej wiadomości. |
| Aplikacje · [Webmode](https://webmode.pl/oferta/aplikacje-webowe) | Oferta łączy procesy, uprawnienia i pierwszą wersję produktu z nazwanymi przykładami, m.in. Ecotec MS i LoanMe. Pytania klienta są na tej samej stronie. | Użyć potwierdzonych ekranów OLAYA do wyjaśnienia różnicy między obsługą sprzedaży, magazynem i widokiem kierownika. |
| B2B · [ZETO-Rzeszów](https://zetorzeszow.pl/2026/06/02/greinplast-case-study-wdrozenie-platformy-b2b/) | Opis Greinplast prowadzi od procesu handlowców do platformy i ERP. Zawiera wypowiedź klienta, kontakty i pełniejsze opracowanie do pobrania. | Pokazać drogę przez katalog, konto firmowe i koszyk CERMAX. Publikację liczb zostawić do momentu uzyskania danych i zgody na ich użycie. |
| Automatyzacja · [CodeScriptum](https://codescriptum.pl/automatyzacja-zamowien-warszawa/) | Strona osadza usługę w konkretnym procesie hurtowni i opisuje obsługę wyjątków oraz etapy współpracy. Zakres podany przez konkurenta dotyczy jego własnej oferty. | Wyjaśnić, kto reaguje na brak ceny, nieznany produkt lub błąd przesłania danych. Klient może ocenić zakres prac przed rozmową. |

**Luki dostępne dla małej domeny.** W ofertach lokalnych dominują prezentacje projektów i lokalizacja. KOBCODE może pokazać, co właściciel firmy sam zrobi po odbiorze strony. W ofertach ogólnopolskich cennik i lista funkcji nie zawsze wyjaśniają wybór między katalogiem, rezerwacją i zakupem na koncie firmowym. W aplikacjach warto połączyć zasady uprawnień z rzeczywistym ekranem. W automatyzacji szczególnie przydatne jest wyjaśnienie, co dzieje się z zamówieniem po błędzie i kto je obsłuży. Są to możliwości wyróżnienia analizowanych stron, a nie stwierdzenie braku tych informacji w całych serwisach.

## Mapa zapytań i intencji

Priorytety oznaczają dopasowanie do oferty i możliwość pokazania dowodu wykonania. Pierwsze eksperymenty sprzedażowe warto oprzeć na trzech grupach zapytań — **platforma zamówień B2B dla hurtowni**, **strona z katalogiem produktów dla producenta** i **aplikacja do obsługi zleceń na zamówienie**.

| Adres | Intencja | Główna fraza i rozwinięcia | Dowód lub następny krok |
| --- | --- | --- | --- |
| `/` | Wybór wykonawcy i rodzaju rozwiązania | dedykowane oprogramowanie dla firm, tworzenie oprogramowania na zamówienie | Porównanie usług i przejście do oferty lub realizacji |
| `/uslugi/strony-internetowe` | Zamówienie serwisu lub katalogu, także lokalnie | tworzenie stron internetowych, projektowanie stron internetowych, strony internetowe dla firm, strony internetowe Radom, tworzenie stron internetowych Radom, strona z katalogiem produktów | Walbud, ParkStop i OLAYA, kryteria wyceny |
| `/uslugi/aplikacje-internetowe` | Zamówienie aplikacji dla zespołu | tworzenie aplikacji webowych, aplikacje internetowe na zamówienie, panele administracyjne dla firm, aplikacja do obsługi zleceń | Aplikacja i API OLAYA, zakres pierwszej wersji |
| `/uslugi/platformy-b2b` | Przeniesienie zakupów hurtowych do internetu | platformy B2B dla firm, systemy B2B, platforma zamówień dla hurtowni, katalog hurtowy z cenami dla kontrahentów | CERMAX, rozdzielenie katalogu i cen, droga do zamówienia |
| `/uslugi/automatyzacja-procesow` | Ograniczenie czynności ręcznych między systemami | automatyzacja procesów w firmie, integracje API dla firm, automatyzacja przekazywania zamówień | Reguły i wyjątki, przykład procesu OLAYA |
| `/uslugi/modernizacja-stron-internetowych` | Przebudowa działającej strony | modernizacja stron internetowych, przebudowa strony firmowej, przeniesienie katalogu i adresów URL | Kryteria zachowania treści i odbioru. ParkStop i Walbud są przykładami funkcji serwisów. |
| `/realizacje` | Weryfikacja wykonawcy | realizacje KOBCODE, przykłady platform B2B, portfolio aplikacji dla firm | Cztery opisane marki i zatwierdzone projekty z panelu |
| `/realizacje/{slug}` | Weryfikacja konkretnej realizacji | nazwa projektu z zakresem wykonanej usługi | Metadane projektu z CMS, publikacja zależna od zatwierdzenia |
| `/o-firmie` | Weryfikacja firmy | KOBCODE Radom, firma programistyczna KOBCODE | Siedziba na Mazowszu, projekty i współpraca ogólnopolska |
| `/kontakt` | Rozpoczęcie wyceny | KOBCODE kontakt, wycena projektu KOBCODE | Formularz i podpowiedź, co opisać |
| `/polityka-prywatnosci` | Informacja dla osoby kontaktującej się | polityka prywatności KOBCODE | Dostępna z formularza i stopki |

Skupienie dedykowanego oprogramowania na stronie głównej i paneli na stronie aplikacji ogranicza nakładanie się podobnych ofert. Radom ma jedno miejsce w kontekście usługi WWW. Potencjalny klient z innego miasta nadal widzi krajowy zasięg. Osobny opis konkretnego wdrożenia lub etapów modernizacji warto dodać po uzyskaniu materiałów i prawa do publikacji.

## Zmiany wykonane na gałęzi

- Pięć usług w jednym katalogu treści. Każda ma osobny adres, tytuł, opis, H1, zakres, przykład, pytania do wyceny i powiązane usługi. Zachowano obydwa istniejące adresy i dawne fragmenty `#automatyzacje` oraz `#integracje`.
- Strona główna rozróżnia usługi, stopka prowadzi do pięciu ofert. W portfolio przy CERMAX jest odnośnik do platform B2B, przy aplikacji OLAYA do aplikacji webowych, a przy serwisach do stron WWW. Zachowano kolejność marek i oznaczenie firmy powiązanej. W opisie OLAYA technologia dotyczy panelu i API.
- Widoczne okruszki nawigacyjne odpowiadają `BreadcrumbList`; `Service` ma nazwę odpowiadającą H1. Treść i linki renderuje Laravel. `Organization`, `WebSite`, `WebPage`, prywatność i formularz nadal korzystają z danych serwisu. Dane spółki są zgodne między stopką a publiczną prezentacją; porównanie z rejestrem i potwierdzenie obsługi w siedzibie pozostają po stronie firmy.
- Formularz poprzedza krótka wskazówka dla dwóch przypadków — przebudowy strony i zamówienia aplikacji. Mechanizm zapisu, walidacja i logowanie pozostają w dotychczasowych kontrolerach.
- Wcześniejsze zmiany z PR #1 obejmują generator mapy, canonicale, nagłówki dla błędów i części prywatnej oraz wymiary obrazów projektów z panelu. Katalog usług automatycznie zasila sitemapę i reguły normalizacji nowych adresów.
- Font Manrope przekodowano z WOFF do WOFF2 z dotychczasowego lokalnego pliku. Preload i CSS wskazują ten sam zasób. Rozmiar spadł z 36 540 do 29 456 B, czyli o 7 084 B (19,4%). Porównano mapę znaków, polskie litery i obecność osi zmiennego fontu. Starszy plik nadal obsługuje starsze wersje CSS.

### Pliki względem main

| Obszar | Nowe | Zmienione |
| --- | --- | --- |
| Laravel | `config/offer.php`, `app/Http/Middleware/PublicSeo.php`, `app/Support/ProjectImage.php` | `app/Http/Controllers/SeoController.php`, `app/Support/Seo.php`, `bootstrap/app.php`, `routes/web.php` |
| Widoki w `resources/views/` | — | `home.blade.php`, `service.blade.php`, `layout.blade.php`, `about.blade.php`, `contact.blade.php`, `portfolio.blade.php`, `project.blade.php`, `partials/compact-project.blade.php`, `partials/seo.blade.php` |
| Zasoby | `public/fonts/manrope-latin.woff2` | `resources/css/app.css` |
| Testy i CI | `tests/Feature/TechnicalSeoTest.php`, `tests/Feature/ServiceLinksTest.php` | `tests/Feature/PresentationTest.php`, `tests/Feature/SeoTest.php`, `.github/workflows/checks.yml` |
| Dokumentacja | `docs/TECHNICAL_SEO_STAGE_2.md`, `docs/SEO_GROWTH_PL.md` | `docs/SEO.md` |

## Wyniki weryfikacji

| Kontrola | Wynik |
| --- | --- |
| Laravel / PHP 8.4.27, SQLite | 23 testy, 554 asercje, wszystkie zaliczone. Sprawdzono m.in. odczyt stron, walidację i zapis zapytania, odrzucenie formularza z pułapką antyspamową, dostęp do panelu, status publikacji projektu oraz 404. |
| Metadane i schema | Na 10 stałych stronach sprawdzono unikalne tytuły i opisy, H1, canonical i JSON-LD. Testy porównują okruszki i nazwę usługi z HTML. Nie wykonywano zewnętrznego Rich Results Test. |
| Mapa i roboty | Lokalnie 200 i parsowalny XML, 10 stałych adresów oraz dodatkowe opublikowane projekty w teście. Stan roboczy wyklucza projekt. Podgląd z wyłączoną indeksacją zwraca noindex i nie udostępnia mapy. |
| Linkowanie | Przejście po wszystkich 10 stronach zweryfikowało 29 unikalnych wewnętrznych celów, w tym fragmenty odsyłające do realizacji. Każdy cel istnieje. |
| Przeglądarka | Chromium, szerokości 360, 390, 768 i 1440 px, po 10 stron. Brak przewijania poziomego dokumentu, błędów JS i zasobów odpowiadających 4xx/5xx. Na mobilnym widoku otwarto i zamknięto menu oraz przełączono pokaz realizacji. Jest to emulacja, bez próby na fizycznym telefonie. |
| Budowanie | Composer wykonał instalację lokalną. Repozytorium nie zawiera śledzonego composer.lock. Build Vite przeszedł po npm ci, CSS 72,01 kB (16,21 kB gzip), JS 1,53 kB (0,65 kB gzip). Komunikat Vite o ścieżce fontu dotyczy pliku serwowanego z public/fonts, jego pobranie w przeglądarce zakończyło się powodzeniem. |
| Cache aplikacji i CI | Kompilacja Blade, route:cache, config:cache oraz lista tras przeszły. [GitHub Actions dla kodu PR](https://github.com/mateuszp47/kobcode-strona/actions/runs/44492311600). |

Nie wysyłano próbnych zgłoszeń na produkcji, nie modyfikowano bazy hostingu ani ustawień logowania. Stan indeksowania nowych adresów można sprawdzić dopiero po wdrożeniu.

### Wydajność

Lighthouse 13.6.0, po jednej nawigacji dla konfiguracji mobilnej i desktopowej. Pomiar produkcyjny wykonano przez połączenie pośredniczące ze sprawdzeniem, że zwrócono HTML KOBCODE wraz z jego fontem, CSS, JS i grafikami. Próby lokalne korzystają z serwera PHP. Różnice między wierszami z różnych serwerów nie wyznaczają wielkości poprawy po wdrożeniu.

| Środowisko i strona | Urządzenie | Ocena wydajności | LCP | CLS | TBT |
| --- | --- | ---: | ---: | ---: | ---: |
| Produkcja, strona główna | mobile | 93 | 1,1 s | 0 | 0 ms |
| Produkcja, strona główna | desktop | 92 | 0,4 s | 0 | 0 ms |
| PR lokalnie, strona główna | mobile | 100 | 0,8 s | 0 | 0 ms |
| PR lokalnie, strona główna | desktop | 100 | 0,2 s | 0 | 0 ms |
| PR lokalnie, platformy B2B | mobile | 100 | 0,6 s | 0 | 0 ms |
| PR lokalnie, platformy B2B | desktop | 100 | 0,2 s | 0 | 0 ms |

API PageSpeed Insights zwróciło 429. Z pomiaru nawigacji nie uzyskano INP ani wyników terenowych CrUX. W panelu GSC sprawdzać 75. percentyl doświadczeń użytkowników, gdy pojawi się wystarczająca próba. Cele to LCP ≤ 2,5 s, INP ≤ 200 ms i CLS ≤ 0,1. TBT w tabeli jest osobną laboratoryjną miarą blokowania głównego wątku. [Dokumentacja raportu Core Web Vitals](https://support.google.com/webmasters/answer/9205520)

## FileZilla i LH.pl — procedura po akceptacji

**Miejsce docelowe** to istniejący katalog aplikacji `~/public_html/kobcode.com/`. W jego wnętrzu są `artisan`, `app/`, `bootstrap/`, `config/`, `resources/`, `routes/` i osobny `public/`. To układ dotychczasowego wdrożenia na LH. Jeżeli po zalogowaniu przez SFTP katalog główny konta widzisz pod inną nazwą, rozpoznaj aplikację po `artisan` i `public/index.php`. Wszystkie ścieżki w tabeli zakładają ten katalog jako punkt początkowy.

1. Na Macu przygotuj osobną kopię zaakceptowanej wersji. Polecenia poniżej zakładają wdrożenie z PR #2. Jeśli PR zostanie zmieniony po recenzji, użyj wersji, dla której przeszły końcowe testy.

   ```bash
   git clone --branch feature/kobcode-seo-growth https://github.com/mateuszp47/kobcode-strona.git kobcode-seo-lh
   cd kobcode-seo-lh
   git log -1 --oneline
   composer install --prefer-dist --no-interaction
   npm ci
   npm run build
   ```

   Composer na Macu dostarcza m.in. źródeł Filament wykrywanych przez Tailwind. Projekt po stronie serwera ma już zależności. Sprawdź, że lokalnie powstały `public/build/manifest.json`, wskazane w nim pliki w `public/build/assets/` i że istnieje `public/fonts/manrope-latin.woff2`. Nazwy z hashem odczytaj z własnego manifestu.

2. W FileZilli użyj dotychczasowego połączenia SFTP. Z prawego panelu pobierz do lokalnej kopii zapasowej pliki z tabeli, które istnieją na serwerze, oraz poprzedni manifest. Zanotuj, które pliki są nowe. Zachowaj istniejące pliki z hashem na serwerze, aby poprzedni manifest mógł ponownie działać. W tej aktualizacji nie ma migracji, zmiany zależności produkcyjnych ani potrzeby wymiany plików `.env`, `.htaccess`, `vendor/`, `storage/` lub `public/storage`.

3. Najpierw prześlij nowy font oraz **pliki z hashem** z lokalnego `public/build/assets/` do zdalnego `public/build/assets/`. Wykonaj podmianę zestawu PHP w krótkim oknie serwisowym — nowy układ stopki odwołuje się do nowych tras. Przy transferze pojedynczych plików odwiedzający mógłby chwilowo dostać niepełną wersję. Po zgodzie na wdrożenie w Termiusie:

   ```bash
   cd ~/public_html/kobcode.com
   /usr/local/php84-fpm/bin/php artisan down --retry=60
   ```

4. W lewym panelu otwórz katalog `kobcode-seo-lh`. Wysyłaj wymienione pliki do odpowiadających im ścieżek po prawej. Jeśli `app/Http/Middleware/` jeszcze nie istnieje, utwórz ten podkatalog. Lista obejmuje również zmiany odziedziczone z PR #1.

   | Pliki z Maca | Dokładny katalog docelowy względem katalogu KOBCODE |
   | --- | --- |
   | `config/offer.php` | `config/` |
   | `app/Support/Seo.php`, `app/Support/ProjectImage.php` | `app/Support/` |
   | `app/Http/Controllers/SeoController.php` | `app/Http/Controllers/` |
   | `app/Http/Middleware/PublicSeo.php` | `app/Http/Middleware/` |
   | `routes/web.php` | `routes/` |
   | `resources/views/home.blade.php`, `about.blade.php`, `contact.blade.php`, `layout.blade.php`, `service.blade.php`, `portfolio.blade.php`, `project.blade.php` (wszystkie z `resources/views/`) | `resources/views/` |
   | `resources/views/partials/compact-project.blade.php`, `resources/views/partials/seo.blade.php` | `resources/views/partials/` |
   | `bootstrap/app.php` | `bootstrap/` |
   | `public/fonts/manrope-latin.woff2` | `public/fonts/` |
   | Pliki CSS i JS z lokalnego `public/build/assets/` | `public/build/assets/` |
   | `public/build/manifest.json` po przesłaniu zasobów | `public/build/` |

   `resources/css/app.css` jest źródłem do lokalnego buildu. Na serwerze ruch obsługuje skompilowany CSS z manifestu. Jeśli zdalne drzewo utrzymujesz również jako kopię źródeł, plik źródłowy trafia do `resources/css/`. Folderów `tests/`, `docs/`, `.github/`, lokalnego cache i `node_modules/` aplikacja na LH nie potrzebuje do tej publikacji.

5. Gdy kolejka FileZilli zakończy się bez błędów, odśwież cache i sprawdź trasy. Jeśli pojawi się błąd, przywróć zestaw z kopii przed zakończeniem okna serwisowego.

   ```bash
   cd ~/public_html/kobcode.com
   /usr/local/php84-fpm/bin/php artisan config:clear
   /usr/local/php84-fpm/bin/php artisan route:clear
   /usr/local/php84-fpm/bin/php artisan view:clear
   /usr/local/php84-fpm/bin/php artisan view:cache
   /usr/local/php84-fpm/bin/php artisan route:list --path=uslugi
   /usr/local/php84-fpm/bin/php artisan up
   ```

6. Po wdrożeniu sprawdź statusy bez wysyłania danych klientów:

   ```bash
   curl -fsS https://kobcode.com/robots.txt
   curl -fsS https://kobcode.com/sitemap.xml
   curl -IL https://www.kobcode.com/kontakt
   curl -IL http://kobcode.com/kontakt
   curl -IL https://kobcode.com/kontakt/
   curl -IL https://kobcode.com/public/kontakt
   curl -I https://kobcode.com/uslugi/platformy-b2b
   curl -I https://kobcode.com/uslugi/automatyzacja-procesow
   curl -I https://kobcode.com/uslugi/modernizacja-stron-internetowych
   curl -I https://kobcode.com/fonts/manrope-latin.woff2
   curl -I https://kobcode.com/brak-strony-audyt-kobcode
   ```

   Mapa powinna zwrócić XML z 10 stałymi adresami i ewentualnymi zatwierdzonymi projektami z produkcyjnej bazy. Usługi i font powinny zwracać 200, ostatni adres 404. Warianty kontaktu powinny kończyć się na `https://kobcode.com/kontakt`. Sprawdź też wygląd na własnym telefonie, link CERMAX z oferty B2B oraz formularz na `/kontakt`. Ewentualne wysłanie oznaczonego zgłoszenia testowego w produkcji wykonuje właściciel po wdrożeniu.

7. Jeśli HTTP wciąż daje 200, sprawdź certyfikat oraz aktywne reguły domeny. Instrukcja LH opisuje wymuszenie HTTPS w pliku `.htaccess`. W tym projekcie reguła musi współpracować z istniejącym przepisaniem do `public/`; nie zastępuj całego pliku przykładem z poradnika. Zweryfikuj też `APP_URL=https://kobcode.com`, `APP_ENV=production`, `SITE_INDEXABLE=true` i dotychczasowe ustawienie formularza. Następnie sprawdź przekierowania ponownie. [Instrukcja LH.pl](https://www.lh.pl/pomoc/doc/przekierowanie-domeny-z-http-na-https/)

**Wycofanie.** W oknie serwisowym przywróć zmienione pliki PHP, widoki oraz poprzedni manifest z odpowiadającymi mu zasobami. Odśwież cache konfiguracji, tras i widoków jak w kroku 5. Stary kod nie korzysta z nowego fontu. Sprawdź stronę główną, przejścia z portfolio do usług, panel logowania oraz formularz. Przy błędzie mapy odczytaj wyjątek odpowiadający żądaniu w logu Laravel na hostingu. Diagnostyka nie wymaga publicznego włączania trybu debugowania.

## Działania wymagające potwierdzenia

| Decyzja | Co należy ustalić |
| --- | --- |
| Publikacja PR i zmiana wymuszania HTTPS | Termin wdrożenia, osoba odbierająca stronę na LH oraz stan reguł domeny. Zgłoszenie mapy do GSC ma sens po potwierdzeniu poprawnego XML na publicznym adresie. |
| Profil Firmy w Google | Czy KOBCODE rzeczywiście przyjmuje klientów w uprawnionej lokalizacji lub odwiedza ich osobiście. Sam adres rejestrowy nie rozstrzyga kwalifikacji. Przy działalności wyłącznie zdalnej Google wyklucza taki profil. Po potwierdzeniu można ujednolicić nazwę, kategorię, telefon i adres WWW z rzeczywistą działalnością. Nie zakładano profilu. [Zasady Google](https://support.google.com/business/answer/7039811) |
| Nowe dowody i dane handlowe | Zgody klientów na dodatkowe zrzuty, cytaty i odnośniki. Do opublikowania wyników potrzebne są źródło, okres i rozsądne porównanie. Firmowych stawek ani terminów nie wyprowadzano z cenników konkurencji. |
| Pomiar wpływu na zapytania | GSC mierzy wejścia z wyszukiwarki. Do przypisania wysłanego formularza i kwalifikacji leada potrzebna jest odrębna ewidencja lub uzgodniona analityka. Nie dodawano narzędzi śledzących. |

## Plan na 30, 60 i 90 dni od wdrożenia

| Okres | Działania właściciela | Co sprawdzić przy podejmowaniu decyzji |
| --- | --- | --- |
| Dni 1–30 | Sprawdzić odbiór na LH, zgłosić działającą mapę w GSC i użyć Inspekcji URL dla trzech nowych usług. Zapisać datę wdrożenia oraz pierwszy pełny zestaw danych dla Polski. W panelu oznaczać rzeczywiste zapytania zgodnie z usługą. | Stan pobrania mapy, przyczyna wykluczeń, ostatnie skanowanie, canonical Google, pierwsze wyświetlenia stron. Przy braku indeksacji sprawdzić odpowiedź na żywo i źródła linków. |
| Dni 31–60 | Porównać pełne okresy 28-dniowe, o ile dane obejmują oba. Wybrać stronę o widocznych wyświetleniach dla trafnych zapytań. Doprecyzować jeden nagłówek lub odpowiedź na powtarzające się pytanie klienta. Dobry pierwszy temat to „Katalog produktów czy platforma B2B z cenami po zalogowaniu?”. | W raporcie Skuteczność przejść od zapytania do zakładki Strony. Sprawdzić, czy wyświetla się właściwy URL oraz jak zmieniają się kliknięcia i CTR przy zbliżonej pozycji i urządzeniu. |
| Dni 61–90 | Opracować jedną udokumentowaną historię projektu z zakresem przed i po wdrożeniu. Jeśli klient się zgodzi, zaproponować mu rzeczowy opis współpracy z naturalnym odnośnikiem. Drugi materiał wybrać według zapytań, np. „Co przygotować do wyceny aplikacji do obsługi zleceń?” lub „Które adresy sprawdzić przed modernizacją strony?”. | Porównać niebrandowe kliknięcia i liczbę kwalifikowanych zapytań. Rozbudowywać usługi, które odpowiadają na rzeczywiste potrzeby. Przy kilkunastu wyświetleniach odłożyć ocenę wariantu tytułu do zebrania większej próby. |

**Wskaźniki do obserwacji.** W GSC wybrać typ „Sieć”, kraj Polska, następnie osobno telefon i komputer. Dla `/uslugi/` śledzić kliknięcia, wyświetlenia, CTR i średnią pozycję według pary zapytanie–strona. Oddzielić zapytania zawierające „kobcode” od pozostałych, a dla lokalnej usługi wyodrębnić słowa zawierające rdzeń „radom”. To filtr słów zapytania, a nie raport fizycznej lokalizacji użytkownika. W indeksowaniu sprawdzać liczbę prawidłowych adresów, wersje kanoniczne i błędy serwera, a w CWV — grupy URL i rozkład mobilny. Dla obrazów portfolio sprawdzać typ wyszukiwania „Grafika” osobno. Raportowanie biznesowe powinno pokazać, ilu zainteresowanych pasowało do oferty i ile zapytań przeszło do wyceny. [Skuteczność w GSC](https://support.google.com/webmasters/answer/17010961)

**Widoczność w wynikach z AI.** Nazwane realizacje, tekst dostępny w HTML, normalne linki i zgodność danych strukturalnych z tym, co widzi klient, wspierają odkrywanie treści również w tych wynikach. Dokumentacja Google wskazuje te same podstawowe wymagania. [Google Search Central](https://developers.google.com/search/docs/appearance/ai-features)
