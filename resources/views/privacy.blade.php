@extends('layout')
@section('title', 'Polityka prywatności — KOBCODE')
@section('description', 'Zasady przetwarzania danych w serwisie KOBCODE: kontakt, formularz, hosting, cookies, okresy przechowywania oraz prawa użytkownika.')
@section('content')
<section class="wrap section page">
<span class="eyebrow">KOBCODE / OCHRONA DANYCH</span>
<h1>Polityka<br><span>prywatności.</span></h1>
<p class="lead">Poniżej wyjaśniamy, jakie dane przetwarzamy podczas korzystania z kobcode.com, w jakim celu i jak możesz skorzystać ze swoich praw.</p>
<p>Data aktualizacji: 3 listopada 2026 r.</p>
<div class="prose">
<section><h2>1. Administrator i kontakt</h2>
<p>Administratorem danych osobowych jest KOBCODE spółka z ograniczoną odpowiedzialnością, NIP 9482601374, KRS 0000512381. Pełny <a class="text-link" href="#dane-spolki">adres siedziby i dane spółki znajdziesz w stopce</a> na tej samej stronie.</p>
<p>W sprawach dotyczących danych osobowych i realizacji swoich praw możesz napisać do KOBCODE na adres siedziby wskazany w stopce, z dopiskiem „Ochrona danych osobowych”.@if(config('site.contact_enabled')) Możesz też skorzystać z <a class="text-link" href="{{ route('contact') }}">formularza kontaktowego</a>.@endif W zgłoszeniu podaj, czego dotyczy sprawa i w jaki sposób możemy przekazać odpowiedź.</p>
</section>
<section><h2>2. Jakie dane otrzymujemy</h2>
<p>Gdy korzystasz z formularza, otrzymujemy imię, adres e-mail i treść zapytania. Nazwa firmy i numer telefonu są opcjonalne. Przetwarzamy także informacje, które dobrowolnie podasz w wiadomości lub dalszej korespondencji. Nie przesyłaj danych szczególnie chronionych, np. informacji o zdrowiu, ani danych innych osób, które nie są potrzebne do obsługi sprawy.</p>
<p>Podanie danych jest dobrowolne. Bez imienia, adresu e-mail i opisu sprawy nie można wysłać formularza. Jeśli kontaktujesz się w inny sposób, potrzebujemy informacji pozwalających obsłużyć zapytanie i odpowiedzieć.</p>
<p>Podczas korzystania z serwisu mogą być przetwarzane dane techniczne, takie jak adres IP, data i czas żądania, odwiedzany adres, informacje o przeglądarce, urządzeniu i błędach. Dane te mogą znajdować się w logach hostingu oraz danych sesji. Sam rekord zapytania w formularzu nie zawiera osobnego pola z adresem IP.</p>
</section>
<section><h2>3. Cele i podstawy prawne</h2>
<ul>
<li><strong>Przygotowanie oferty i działania przed zawarciem umowy</strong> na Twoje żądanie — art. 6 ust. 1 lit. b RODO, jeśli jesteś przyszłą stroną umowy.</li>
<li><strong>Odpowiedzi na pozostałe pytania i kontakt z przedstawicielami firm</strong> — art. 6 ust. 1 lit. f RODO. Naszym uzasadnionym interesem jest prowadzenie korespondencji i obsługa kontaktów biznesowych.</li>
<li><strong>Działanie i bezpieczeństwo serwisu</strong>, ochrona formularza przed nadużyciami oraz obsługa dostępu do panelu — art. 6 ust. 1 lit. f RODO. Naszym interesem jest ochrona danych i prawidłowe działanie usług.</li>
<li><strong>Ustalenie, dochodzenie lub obrona roszczeń</strong> — art. 6 ust. 1 lit. f RODO, w zakresie potrzebnym do ochrony naszych praw.</li>
</ul>
<p>Jeżeli zawrzemy umowę, dane potrzebne do jej wykonania i wymaganej prawem dokumentacji będziemy przetwarzać odpowiednio na podstawie art. 6 ust. 1 lit. b i c RODO. Szczegóły zależą od rodzaju współpracy.</p>
<p>Wysłanie zapytania nie oznacza zapisu do newslettera ani zgody na marketing. Nie wymagamy zgody na przetwarzanie danych, jeśli podstawą jest obsługa zapytania lub podjęcie działań przed zawarciem umowy.</p>
</section>
<section><h2>4. Formularz i dostęp do zapytań</h2>
<p>Wiadomość wysłana przez formularz jest zapisywana w bazie danych serwisu i udostępniana upoważnionym osobom w panelu administracyjnym. Formularz nie wysyła automatycznych powiadomień e-mail. Dane wykorzystujemy do obsługi sprawy i dalszego kontaktu.</p>
</section>
<section><h2>5. Odbiorcy danych i hosting</h2>
<p>Z usług hostingowych korzystamy w LH.pl sp. z o.o. Dostawca hostingu może przetwarzać dane zapisane w serwisie, logach i kopiach zapasowych w zakresie zapewnienia działania, bezpieczeństwa oraz utrzymania infrastruktury. Dostęp mogą mieć także osoby upoważnione przez KOBCODE i podmioty świadczące obsługę techniczną, w zakresie niezbędnym do powierzonych zadań i na odpowiedniej podstawie prawnej.</p>
<p>Dane mogą być udostępnione organom uprawnionym do ich otrzymania, jeżeli wynika to z przepisów. Nie sprzedajemy danych użytkowników. W obecnej konfiguracji serwisu nie korzystamy z narzędzi analitycznych ani reklamowych powodujących przekazywanie danych do państw poza Europejskim Obszarem Gospodarczym.</p>
</section>
<section><h2>6. Jak długo przechowujemy dane</h2>
<ul>
<li><strong>Zapytania, które nie doprowadziły do zawarcia umowy:</strong> do udzielenia odpowiedzi i zakończenia obsługi sprawy, a w przypadku rozmów o współpracy — do ich zakończenia lub rezygnacji z oferty. Po tym czasie zachowujemy wyłącznie dane, których dalsze przechowywanie jest uzasadnione obowiązkiem prawnym albo konkretnym roszczeniem, na zasadach opisanych poniżej.</li>
<li><strong>Korespondencja związana z umową lub roszczeniem:</strong> przez czas wykonywania umowy, a następnie do upływu właściwego terminu przedawnienia roszczeń. Jeśli powstanie spór, do jego prawomocnego zakończenia i rozliczenia. Obowiązkową dokumentację przechowujemy przez okres wymagany właściwymi przepisami.</li>
<li><strong>Dane aktywnej sesji:</strong> przez czas jej ważności. W aktualnej konfiguracji czas bezczynności sesji wynosi {{ (int) config('session.lifetime', 120) }} minut; po wygaśnięciu dane sesji podlegają technicznemu oczyszczeniu.</li>
<li><strong>Logi techniczne i kopie zapasowe:</strong> według okresów rotacji i retencji stosowanych dla danej usługi hostingowej. Jeśli konkretny zapis jest potrzebny do wyjaśnienia incydentu lub roszczenia, przechowujemy niezbędny zakres do zakończenia tej sprawy, zamiast całego zbioru logów.</li>
</ul>
<p>Dane, dla których ustał cel przetwarzania i nie ma innej podstawy ich dalszego przechowywania, usuwamy lub anonimizujemy. Usunięcie z systemu bieżącego nie oznacza natychmiastowego usunięcia z rotacyjnych kopii zapasowych; kopie wygasają zgodnie z cyklem retencji hostingu.</p>
</section>
<section><h2>7. Twoje prawa</h2>
<p>Na zasadach określonych w RODO możesz żądać dostępu do swoich danych i ich kopii, sprostowania, usunięcia lub ograniczenia przetwarzania. Prawo do przenoszenia danych przysługuje w zakresie, w którym przetwarzanie odbywa się automatycznie na podstawie umowy lub zgody.</p>
<p><strong>Możesz wnieść sprzeciw</strong> wobec przetwarzania opartego na prawnie uzasadnionym interesie, z przyczyn związanych z Twoją szczególną sytuacją. Nie wszystkie prawa mają zastosowanie w każdym przypadku, np. obowiązek prawny lub potrzeba obrony roszczeń mogą uzasadniać dalsze przechowywanie części danych.</p>
<p>Żądanie możesz przekazać w sposób opisany w punkcie 1. Odpowiadamy co do zasady w ciągu miesiąca; w przypadkach przewidzianych w RODO termin może zostać przedłużony, o czym poinformujemy. W razie uzasadnionych wątpliwości możemy poprosić o informacje konieczne do potwierdzenia tożsamości.</p>
<p>Masz również prawo wnieść skargę do Prezesa Urzędu Ochrony Danych Osobowych. Informacje o sposobach złożenia skargi są dostępne na <a class="text-link" href="https://uodo.gov.pl/">uodo.gov.pl</a>.</p>
</section>
<section><h2>8. Cookies i sesja</h2>
<p>Serwis korzysta z technicznych plików cookies związanych z sesją, ochroną formularzy i logowaniem do panelu. Należą do nich cookie sesji <strong>{{ config('session.cookie') }}</strong> oraz, gdy jest ustawiane przez aplikację, <strong>XSRF-TOKEN</strong> służące ochronie przed nieuprawnionymi żądaniami. Czas ważności sesji zależy od konfiguracji wskazanej w punkcie 6 i odnowienia jej podczas korzystania z serwisu.</p>
<p>Nie stosujemy w tej wersji Google Analytics, Meta Pixel, reklamowych cookies ani newslettera. Samo przeglądanie serwisu nie uruchamia takich narzędzi. Cookies niezbędne do realizacji żądanej usługi nie wymagają zgody na podstawie art. 399 ust. 3 Prawa komunikacji elektronicznej.</p>
<p>Możesz usuwać cookies lub blokować je w ustawieniach przeglądarki. Zablokowanie technicznych cookies może uniemożliwić wysłanie formularza lub zalogowanie do panelu.</p>
</section>
<section><h2>9. Decyzje automatyczne i linki zewnętrzne</h2>
<p>Nie podejmujemy na podstawie danych z tego serwisu decyzji wywołujących skutki prawne lub podobnie istotnie wpływających na użytkownika wyłącznie w sposób zautomatyzowany. Nie prowadzimy profilowania marketingowego.</p>
<p>Linki w portfolio prowadzą do zewnętrznych serwisów. Dopiero po przejściu na taki serwis obowiązują zasady jego administratora; sam link nie osadza jego panelu ani nie przekazuje mu danych z naszego formularza.</p>
</section>
<section><h2>10. Zmiany polityki</h2>
<p>Aktualizujemy politykę, gdy zmienia się sposób przetwarzania danych, funkcje serwisu lub przepisy. Data bieżącej wersji znajduje się na początku dokumentu. Dodanie nowych celów przetwarzania lub narzędzi wymagających zgody będzie wymagało odpowiedniego poinformowania użytkowników i, gdy to konieczne, uzyskania zgody przed ich uruchomieniem.</p>
</section>
</div>
</section>
@endsection
