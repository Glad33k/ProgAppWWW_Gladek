## Wersja v1.5

## Instrukcja uruchomienia

1. Skopiuj katalog `lab4` do folderu `htdocs` w instalacji **XAMPP** (domyślnie `C:\xampp\htdocs\lab4`).
2. Uruchom panel sterowania XAMPP i włącz moduł **Apache**.
3. Otwórz przeglądarkę i przejdź pod adres:
   - Strona główna: `http://localhost/lab4/`
   - Katalog PHP: `http://localhost/lab4/lab4.php`

## Pseudokod działania `lab4.php`

```text
START
  1. Wywołaj session_start() przed wysłaniem jakichkolwiek danych HTML.
  2. Wczytaj tablicę $produkty za pomocą require_once (__DIR__ . '/includes/produkty.php').
  3. Ustaw domyślny filtr = 'wszystkie' oraz puste komunikaty błędów.

  4. JEŻELI w $_GET istnieje 'kategoria' TO:
       JEŻELI wartość jest tekstem i należy do ['wszystkie', 'ksiazki', 'akcesoria'] TO:
         filtr = $_GET['kategoria']
       W PRZECIWNYM RAZIE:
         ustaw komunikat błędu filtra (pozostaw filtr = 'wszystkie')

  5. JEŻELI metoda żądania HTTP === 'POST' TO:
       pobierz $_POST['produkt'] oraz $_POST['ilosc']
       zwaliduj oba pola jako tekstowy zapis liczby całkowitej (is_string + FILTER_VALIDATE_INT)
       odszukaj produkt w tablicy $produkty po polu 'id'

       JEŻELI walidacja typu nie powiodła się LUB produkt nie istnieje
              LUB ilosc < 1 LUB ilosc > 5 LUB ilosc > produkt['stan'] TO:
         ustaw kod odpowiedzi HTTP 400 (Bad Request)
         ustaw komunikat błędu formularza (nie nadpisuj $_SESSION)
       W PRZECIWNYM RAZIE:
         zapisz w $_SESSION['ostatni_wybor'] = ['id' => produktId, 'ilosc' => ilosc]
         wyślij nagłówek Location: lab4.php z kodem HTTP 303 (See Other)
         zakończ skrypt (exit)

  6. Wygeneruj dokument HTML:
       - dołącz nagłówek przez include (__DIR__ . '/includes/naglowek.php')
       - wyświetl zapisany wybór z $_SESSION['ostatni_wybor'] (lub informację o jego braku)
       - wyświetl formularz filtrowania (method="get")
       - przejdź pętlą while po $produkty, filtruj, mapuj kategorię (switch) i stan (if/elseif/else)
       - wyświetl formularz wyboru produktu (method="post") z opcjami ilości 1..5 (pętla for)
       - wszystkie dynamiczne ciągi znaków escapuj przez htmlspecialchars(..., ENT_QUOTES, 'UTF-8')
KONIEC
```

## Dokumentacja prób i wyników testów

Weryfikację przeprowadzono w przeglądarce z otwartymi narzędziami deweloperskimi (zakładka **Network / Sieć** oraz **Elements / Inspektor**).

| Próba | Kroki wykonania | Oczekiwany wynik | Rzeczywisty wynik |
| :--- | :--- | :--- | :--- |
| **1. Filtr GET** | 1. Wybrano kategorię *Książki* (`?kategoria=ksiazki`).<br>2. Wpisano w pasku adresu `?kategoria=nieznana` oraz `?kategoria[]=ksiazki`. | Dla `ksiazki` widoczne są tylko książki. Dla nieznanej wartości lub tablicy wyświetla się pełna lista oraz komunikat błędu. | **Zgodny.** Filtr `ksiazki` zwraca 2 książki (`GET 200`). Błędne parametry zwracają pełną listę (4 produkty) i komunikat błędu nad formularzem. |
| **2. Poprawny POST i odświeżenie** | 1. Wybrano *Pan Tadeusz* (stan: 12) i ilość `2`, kliknięto *Zapisz wybór*.<br>2. Odświeżono stronę (`F5`) i zmieniono filtr GET na *Akcesoria*. | W Network widoczne `POST 303`, a następnie `GET 200`. Odświeżenie nie wysyła ponownie formularza. Wybór w sesji pozostaje widoczny po zmianie filtra. | **Zgodny.** W zakładce Network zarejestrowano `lab4.php` (`POST 303`) oraz przekierowanie `lab4.php` (`GET 200`). Po `F5` i zmianie filtra zapisany wybór nadal widnieje na górze strony. |
| **3. Błędne wartości POST** | W Inspektorze zmodyfikowano formularz i wysłano kolejno: ilość `0`, `6`, `2.5`, `2abc`, brak pola `ilosc` (usunięty atrybut `name`), tablicę `ilosc[]` oraz nieistniejące ID produktu `999`. | Każda próba zwraca kod **HTTP 400**, wyświetla komunikat błędu przy formularzu POST i nie nadpisuje wcześniejszego poprawnego wyboru w sesji. | **Zgodny.** Wszystkie 7 przypadków brzegowych zwróciło w Network status `POST 400 Bad Request`, wyświetliło odpowiedni komunikat błędu, a w sesji pozostał *Pan Tadeusz (2 szt.)*. |
| **4. Stan magazynowy** | Wybrano produkt *Lampka do czytania LED* (posiadający w katalogu `stan => 1`) oraz ilość `2` sztuki, po czym wysłano formularz. | Serwer odrzuca żądanie kodem **HTTP 400** (mimo że `2` mieści się w zakresie `1–5`) i informuje o przekroczeniu stanu magazynowego. | **Zgodny.** Żądanie odrzucone ze statusem `POST 400`, wyświetlono komunikat: *„Błąd: Brak wystarczającej liczby sztuk (dostępny stan: 1).”* |
| **5. Odrębna sesja i tekst HTML** | 1. Otwarto `lab4.php` w oknie prywatnym (Incognito).<br>2. Tymczasowo ustawiono nazwę produktu na `<script>alert(1)</script>Test`, zapisano wybór, po czym przywrócono oryginalną nazwę. | W nowym oknie brak zapisanego wyboru. Tag `<script>` wyświetla się jako zwykły tekst bez wykonania kodu JS. | **Zgodny.** W trybie prywatnym wyświetlił się komunikat *„Brak zapisanego wyboru”*. Wstrzyknięty tag został wyescapowany przez `htmlspecialchars()` i wyświetlony jako tekst. |
| **6. Strona po zmianie ścieżek** | Otwarto `index.html`, podstrony `html/produkt.html` i `html/kontakt.html` oraz przetestowano animacje jQuery i zapytanie do `status.php`. | Poprawne ładowanie stylów CSS, obrazków, nawigacji między stronami oraz działanie skryptów i żądania do `status.php`. | **Zgodny.** Wszystkie zasoby (`../css/`, `../js/`, `../img/`, `../vendor/`) ładują się bez błędów w konsoli, animacje i `status.php` działają poprawnie. |