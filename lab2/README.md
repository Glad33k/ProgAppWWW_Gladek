## Instrukcja uruchomienia
1. Pobierz lub sklonuj folder z projektem na dysk lokalny
2. w konsoli przejdź do folderu z projektem (...\lab2)
3. Wpisz polecenie python -m http.server 8000
4. Odpal stronę localhost:8000 na przeglądarce

### 1. Wyszukiwarka produktów (index.html)
* **Zdarzenie:** Wystąpienie zdarzenia `input` na polu tekstowym `<input type="search">`.
* **Dane wejściowe:** Wartość wpisana przez użytkownika pobrana przez `event.target.value`. Następuje oczyszczenie ze skrajnych spacji i ujednolicenie wielkości liter.
* **Obliczenie:** Skrypt iteruje po pobranej kolekcji kart produktów. Z każdej karty wyciąga tytuł i sprawdza za pomocą metody, czy zawiera on szukaną frazę.
* **Zmiana DOM:** W zależności od wyniku porównania, skrypt zmienia atrybut `hidden` kart. Następnie wstrzykuje przez `textContent` nową wartość do licznika wyników oraz ewentualnie odkrywa komunikat o braku dopasowań.

### 2. Formularz kontaktowy i kalkulator cen (kontakt.html)
* **Zdarzenie:** `change` na polu wyboru produktu, `input` na polu ilości oraz `submit` na całym formularzu. Zdarzenie `submit` jest przechwytywane przez `event.preventDefault()`, co blokuje wysłanie danych na serwer i przeładowanie strony.
* **Dane wejściowe:** ID wybranego produktu z listy `<select>`, ilość sztuk z pola `<input type="number">` oraz tekst z pola `<textarea>`.
* **Obliczenie:**
  * Rzutowanie ilości na typ liczbowy.
  * Weryfikacja, czy liczba jest całkowita i mieści się w przedziale od 1 do 5.
  * Pobranie ceny produktu z lokalnej tablicy obiektów, gdzie cena zapisana jest w formacie całkowitym (w groszach).
  * Wywołanie niezależnej od drzewa DOM funkcji `obliczWartosc(cenaGrosze, ilosc)`, zwracającej całkowity iloczyn.
  * Sprawdzenie długości tekstu wiadomości po wywołaniu `trim()`.
* **Zmiana DOM:** Wyświetlenie błędów walidacji w przypadku niepoprawnych danych i wymuszenie aktywności pola przez `.focus()`. Dla poprawnych danych, przeliczenie groszy na złotówki, sformatowanie kwoty za pomocą `Intl.NumberFormat` i wstrzyknięcie pełnego podsumowania do elementu podsumowującego, używając bezpiecznego `textContent` zapobiegającego atakom XSS.

### 3. Zegar (index.html)
* **Zdarzenie:** Wywołanie funkcji `setInterval` przy starcie strony lub kliknięciu przycisku "Start", wywołującej funkcję aktualizującą co 1000 ms. Przycisk "Stop" wywołuje `clearInterval`.
* **Dane wejściowe:** Obiekt systemowego czasu wywoływany w każdej pętli jako `new Date()`.
* **Obliczenie:** Sformatowanie czasu z wykorzystaniem `Intl.DateTimeFormat`.
* **Zmiana DOM:** Podmiana zawartości tekstowej `textContent` w akapicie wyświetlacza zegara na nowo wygenerowany ciąg znaków, co nadpisuje starą wartość w czasie rzeczywistym.