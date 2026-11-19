# Laboratorium 5 – Wspólny szablon, routing podstron PHP i multimedia

Rozbudowa projektu o jednolity szablon układu w `index.php`, dynamiczne dołączanie treści podstron na podstawie białej listy (`$mapaPodstron`) oraz parametru `$_GET['idp']`, obsługę kodów błędów HTTP (`404` oraz `500`), a także responsywną podstronę z osadzonymi materiałami wideo (`iframe`).


Przed wydzieleniem wspólnego szablonu skopiowano katalog `lab4` do `htdocs/lab5` i zweryfikowano jego poprawne działanie pod adresem `http://localhost/lab5/index.html` oraz `http://localhost/lab5/lab4.php`:
- **Filtr katalogu (`GET`):** Wybór kategorii poprawnie zawęża listę produktów.
- **Zapis wyboru (`POST` + Sesja):** Wysłanie formularza zwraca `POST 303`, a następnie `GET 200` i zapisuje wybór w `$_SESSION['ostatni_wybor']`.
- **Żądanie AJAX (`status.php`):** Asynchroniczne zapytanie na stronie głównej zwraca status `200 OK` i aktualizuje komunikat w DOM.



### Adresy aplikacji
- **Strona główna:** `http://localhost/lab5/index.php` (lub `http://localhost/lab5/index.php?idp=glowna`)
- **Podstrona produktu:** `http://localhost/lab5/index.php?idp=produkt`
- **Podstrona kontaktowa:** `http://localhost/lab5/index.php?idp=kontakt`
- **Podstrona z filmami:** `http://localhost/lab5/index.php?idp=filmy`
- **Katalog PHP (Lab 4):** `http://localhost/lab5/lab4.php`
- **Endpoint AJAX:** `http://localhost/lab5/status.php`

## Instrukcja uruchomienia

1. Umieść katalog `lab5` w folderze `htdocs` serwera **XAMPP** (`C:\xampp\htdocs\lab5`).
2. Uruchom usługę **Apache** w panelu sterowania XAMPP.
3. Otwórz w przeglądarce adres `http://localhost/lab5/index.php`.

## Pseudokod działania index.php

```text
START
  1. Zdefiniuj zmienną $idProjektu oraz tablicę $mapaPodstron (biała lista: klucz => [etykieta, tytul, plik]).
  2. Odczytaj parametr $_GET['idp'] przed wysłaniem jakichkolwiek danych HTML:
       JEŻELI brak $_GET['idp'] LUB $_GET['idp'] === '' TO:
         aktywnyIdp = 'glowna'
       W PRZECIWNYM RAZIE JEŻELI $_GET['idp'] jest tekstem ORAZ istnieje w $mapaPodstron TO:
         aktywnyIdp = $_GET['idp']
       W PRZECIWNYM RAZIE:
         ustaw kod odpowiedzi HTTP 404 (Not Found)
         ustaw komunikat błędu 404 (nieznana podstrona)

  3. JEŻELI aktywnyIdp został ustalony TO:
       zbuduj pełną ścieżkę: __DIR__ . '/' . $mapaPodstron[aktywnyIdp]['plik']
       JEŻELI is_file(ścieżka) ORAZ is_readable(ścieżka) TO:
         plikDoDolaczenia = ścieżka
       W PRZECIWNYM RAZIE:
         ustaw kod odpowiedzi HTTP 500 (Internal Server Error)
         ustaw ogólny komunikat błędu 500 (bez ujawniania ścieżki systemowej)

  4. Wygeneruj wspólny szablon HTML:
       - w <title> wypisz tytuł widoku zabezpieczony przez htmlspecialchars(..., ENT_QUOTES, 'UTF-8')
       - w <nav> wygeneruj linki z $mapaPodstron, oznaczając aktywny wpis atrybutem aria-current="page"
       - w <main>:
           JEŻELI plikDoDolaczenia !== null TO dołącz plik przez include
           W PRZECIWNYM RAZIE wyświetl komunikat błędu (404 lub 500) i odnośnik do index.php
       - w <footer> wypisz $idProjektu przez echo z htmlspecialchars()
KONIEC
```

## Dokumentacja prób i wyników testów

| Próba | Kroki wykonania | Oczekiwany wynik | Rzeczywisty wynik |
| :--- | :--- | :--- | :--- |
| **1. Adres domyślny i menu** | Otwarto `index.php` oraz `index.php?idp=`, a następnie kliknięto kolejno wszystkie odnośniki w nawigacji. | `index.php` i `index.php?idp=` ładują stronę główną (`HTTP 200`). Wszystkie linki otwierają właściwe treści we wspólnym szablonie i zmieniają `<title>` oraz `aria-current="page"`. | **Zgodny.** Strona główna otwiera się domyślnie, menu dynamicznie podświetla aktywną podstronę i zmienia tytuł karty w przeglądarce. |
| **2. Nieznany lub tablicowy klucz** | Wpisano w pasku adresu `index.php?idp=nieznana` oraz `index.php?idp[]=filmy`. | Serwer zwraca kod **HTTP 404**, wyświetla komunikat błędu i link powrotny do strony głównej bez ostrzeżeń PHP. | **Zgodny.** W zakładce *Network* oba żądania mają status `404 Not Found`, brak ostrzeżeń `Warning`/`Notice`, widoczny link powrotny. |
| **3. Próba podania ścieżki** | Wywołano adres `index.php?idp=..%2Fstatus.php` (próba *Path Traversal*). | Serwer odrzuca żądanie kodem **HTTP 404**, a zawartość `status.php` nie zostaje dołączona. | **Zgodny.** Ponieważ `../status.php` nie figuruje jako klucz w `$mapaPodstron`, zwrócono `HTTP 404` i komunikat błędu. |
| **4. Brak pliku widoku** | Tymczasowo zmieniono nazwę pliku `html/filmy.html` na `html/filmy_bak.html`, otwarto `index.php?idp=filmy`, a po teście przywrócono nazwę pliku. | Serwer zwraca kod **HTTP 500** i wyświetla ogólny komunikat błędu bez ujawniania fizycznej ścieżki na dysku. | **Zgodny.** W panelu *Network* zarejestrowano status `500 Internal Server Error`, na ekranie wyświetlono bezpieczny komunikat bez ścieżki `C:\xampp\...`. |
| **5. Strona główna i AJAX** | Otwarto `index.php`, sprawdzono działanie zegara, ciemnego motywu, animacji jQuery oraz zakładkę *Network* dla `status.php`. | Działają wszystkie style CSS, obrazy i skrypty JS. W panelu *Network* widoczne jest udane żądanie do `status.php` (`HTTP 200`), a na stronie pojawia się jego wynik. | **Zgodny.** Zasoby ładują się poprawnie, żądanie XHR/Fetch do `status.php` zwraca `200 OK` i wyświetla status na stronie głównej. |
| **6. Katalog i sesja po migracji** | Przejście z menu do `lab4.php`, filtrowanie kategorii, zapis wyboru (`POST 303` -> `GET 200`), powrót na `index.php`, wejście w *Filmy* i ponowny powrót do `lab4.php`. | Przekierowanie PRG działa poprawnie (`303` -> `200`), a stan sesji `$_SESSION['ostatni_wybor']` utrzymuje się podczas nawigacji między `index.php` a `lab4.php`. | **Zgodny.** Po przejściu przez podstrony `index.php` i powrocie do `lab4.php` zapisany wcześniej produkt i jego ilość nadal są widoczne. |
| **7. Filmy i układ mobilny** | Otwarto `index.php?idp=filmy`, uruchomiono wszystkie 3 odtwarzacze, sprawdzono nawigację klawiszem `Tab` oraz ustawiono szerokość widoku na `360 px` w DevTools. | Wszystkie 3 filmy odtwarzają się po kliknięciu (brak autoodtwarzania), linki są osiągalne z klawiatury, a przy `360 px` brak poziomego paska przewijania. | **Zgodny.** Filmy uruchamiają się poprawnie, Fokus `Tab` przechodzi po odtwarzaczach i linkach, a przy szerokości `360 px` układ mieści się w oknie bez scrolla poziomego. |