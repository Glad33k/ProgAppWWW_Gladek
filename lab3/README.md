# Laboratorium 3 - Animacje jQuery i środowisko PHP

## Instrukcja uruchomienia
1. Projekt wymaga lokalnego serwera Apache. Zainstaluj pakiet XAMPP.
2. Skopiuj cały folder projektu (`lab3`) do katalogu `htdocs` w miejscu instalacji XAMPP (np. `C:\xampp\htdocs\lab3`).
3. Uruchom moduł Apache w panelu kontrolnym XAMPP.
4. Otwórz przeglądarkę i wpisz adres: `http://localhost/lab3/` (lub odpowiedni, jeśli port Apache został zmieniony).

## Wersja biblioteki jQuery
W projekcie wykorzystano pełną, lokalną wersję biblioteki **jQuery 4.0.0** (`vendor/jquery-4.0.0.min.js`).

## Pseudokody animacji

**Animacja A (Powiększenie po kliknięciu):**
1. Utwórz zmienną logiczną stanu (stanA = false).
2. Po kliknięciu odwróć stan i zaktualizuj aria-pressed.
3. Zatrzymaj kolejkę trwających animacji przez .stop(true, false).
4. Animuj wymiary do 240x130 (jeśli true) lub powrót do 160x90 (jeśli false) w czasie 300 ms.

**Animacja B (Powiększenie po najechaniu i fokusie):**
1. Utwórz zmienne logiczne dla hover i focus.
2. Przy zdarzeniach mouseenter/mouseleave/focus/blur aktualizuj odpowiedni stan.
3. Wywołaj funkcję sprawdzającą: jeśli którykolwiek ze stanów jest aktywny, animuj powiększenie (220x120px). W przeciwnym razie przywróć wymiary bazowe.
4. Użyj .stop(true, false) przed .animate() by uniknąć kolejkowania przy szybkich ruchach.

**Animacja C (Wzrost z każdym kliknięciem):**
1. Utwórz zmienną liczącą kroki (kroki = 0).
2. Przy kliknięciu zablokuj działanie, jeśli element jest w trakcie animacji używając .is(':animated').
3. Zablokuj działanie, jeśli licznik kroków >= 5.
4. Zwiększ licznik kroków, zaktualizuj tekst w role="status".
5. Wylicz docelowe wymiary ze stałych bazowych (szer + kroki * 20, wys + kroki * 10) i uruchom animację.

## Wyniki testów

| Próba | Rzeczywisty wynik po testach |
| :--- | :--- |
| **Szybkie kliknięcia A** | Brak kolejkowania. Szybkie kliknięcia kończą się płynnie w stanie adekwatnym do ich liczby, dzięki .stop(true, false). |
| **Kursor i fokus B** | Usunięcie kursora przy wciśniętym fokusie nie zmniejsza obiektu. Dopiero utrata obu stanów przywraca wymiar bazowy. Brak kolejkowania. |
| **Limit i reset C** | 5 kliknięć osiąga poprawnie 260x140 px. Kolejne kliknięcia są ignorowane. Użycie resetu natychmiast przywraca 160x90 px. |
| **Klawiatura i ruch** | Nawigacja klawiszem Tab dociera do przycisków. Enter/Spacja działają. Emulacja `prefers-reduced-motion: reduce` natychmiast wyłącza animacje (zmiany stają się skokowe, funkcjonalność zostaje zachowana). |

### Wyniki testów AJAX (Sprawdź serwer)
1. **Prawidłowe żądanie:** Metoda GET, kod odpowiedzi: 200 OK. Wyświetla się tekst "PHP działa".
2. **Brak pliku na serwerze:** Zmiana nazwy pliku powoduje w zakładce Network błąd 404 (Not Found). Na ekranie pojawia się przewidziany w `.fail()` komunikat o błędzie.
3. **Wolne 3G:** Podczas ładowania przycisk staje się nieaktywny (disabled), a na ekranie widnieje napis "Ładowanie...". Po otrzymaniu odpowiedzi przycisk jest odblokowywany.