# Obsługa danych osobowych

Polityka opisuje aktualny formularz Laravel, bazę zapytań, sesję i hosting LH.pl. Nie wprowadza analityki, marketingu, newslettera ani integracji z innymi panelami.

## Obowiązki administratora

- Po zakończeniu obsługi zapytania usuń je z panelu, jeżeli nie ma podstawy do dalszego przechowywania. Samo odczytanie zapytania nie usuwa danych. Aplikacja nie ma automatycznego harmonogramu usuwania zapytań.
- Dane potrzebne do umowy, dokumentacji wymaganej prawem lub konkretnego roszczenia przechowuj tylko przez właściwy okres; oddziel je od niepotrzebnej treści zapytań.
- Regularnie przeglądaj bazę zapytań i uprawnienia osób mających dostęp do panelu.
- Potwierdź w LH.pl okresy retencji logów, kopii zapasowych i miejsce przetwarzania oraz warunki powierzenia danych. Polityka nie deklaruje niezweryfikowanej liczby dni. Po uzyskaniu tych informacji doprecyzuj punkt 6.
- Zapewnij obsługę żądań przesyłanych pocztą na adres spółki oraz przez formularz, jeżeli jest włączony. Termin odpowiedzi co do zasady wynosi miesiąc.
- Zweryfikuj ustawienia produkcyjne: APP_ENV=production, APP_DEBUG=false, APP_URL=https://kobcode.com, SESSION_SECURE_COOKIE=true. Nie zmieniaj APP_KEY działającej aplikacji.
- Przed dodaniem zewnętrznych skryptów, osadzonych treści, analityki, marketingu lub nowych odbiorców zaktualizuj politykę i oceń wymóg zgody.

## Zakres zmiany

Treść polityki, opis danych przy formularzu oraz kotwica do danych spółki w stopce. Bez zmian w bazie, zależnościach i konfiguracji serwera. Kontakt pocztowy umożliwia realizację praw bez publikowania adresu e-mail.
