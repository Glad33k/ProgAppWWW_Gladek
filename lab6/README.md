# Sklep z piłkami — Laboratorium 6 (`sklep-pilkarski-v1.5`)

## Instrukcja odtworzenia środowiska

### 1. Uruchomienie serwera lokalnego
1. Skopiuj katalog `lab6` do folderu `htdocs` instalacji **XAMPP** (`C:\xampp\htdocs\lab6`).
2. Uruchom w panelu sterowania XAMPP usługi **Apache** oraz **MySQL** (MariaDB).

### 2. Odtworzenie bazy danych i tabeli
1. Otwórz w przeglądarce `http://localhost/phpmyadmin`.
2. Przejdź do zakładki **Import** (lub **SQL**) i wykonaj kolejno:
   - `sql/schema.sql` — tworzy bazę `moja_strona` oraz tabelę `page_list` z kodowaniem `utf8mb4_unicode_ci` (wszystkie kolumny `NOT NULL`, klucz główny `id`, unikalny indeks na `alias`),
   - `sql/seed.sql` — importuje podstrony z laboratorium 5 (`glowna`, `produkt`, `kontakt`, `filmy` ze `status = 1`) oraz jeden szkic (`szkic` ze `status = 0`).

### 3. Utworzenie lokalnego użytkownika bazy danych
W zakładce **SQL** w phpMyAdmin (jako administrator `root`) utwórz lokalnego użytkownika z prawem wyłącznie do odczytu (`SELECT`) z tabeli `moja_strona.page_list`:

```sql
CREATE USER 'sklep_user'@'localhost' IDENTIFIED BY 'TWOJE_SILNE_HASLO';
GRANT SELECT ON moja_strona.page_list TO 'sklep_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Konfiguracja połączenia PDO i uruchomienie aplikacji
1. Skopiuj plik `cfg.example.php` jako `cfg.php` w katalogu `lab6/`.
2. Uzupełnij w `cfg.php` pola `$dbUser = 'sklep_user';` oraz `$dbPass` hasłem ustawionym w kroku 3 (plik `cfg.php` jest ignorowany przez Git na podstawie `.gitignore`).
3. Otwórz projekt w przeglądarce:
   - Strona główna: `http://localhost/lab6/index.php`
   - Katalog PHP: `http://localhost/lab6/lab4.php`
   - Szkielet panelu CMS: `http://localhost/lab6/admin/admin.php`

---

## Pseudokod działania `index.php` oraz `showpage.php`

```text
FUNKCJA PokazPodstrone(pdo, alias):
  1. Przygotuj zapytanie SQL (prepare):
     SELECT id, page_title, page_content FROM page_list WHERE alias = :alias AND status = 1 LIMIT 1
  2. Wykonaj zapytanie (execute) z bindowaniem parametru :alias.
  3. Pobierz pojedynczy wiersz jako tablicę asocjacyjną (fetch).
  4. Zwróć rekord strony albo null, jeżeli rekord nie istnieje lub ma status = 0.

START index.php:
  1. Ustaw wersję projektu ('sklep-pilkarski-v1.5') oraz domyślne zmienne widoku.
  2. W bloku TRY (przed wysłaniem jakiegokolwiek kodu HTML):
       a) Wczytaj cfg.php (utworzenie obiektu $pdo z charset=utf8mb4, ERRMODE_EXCEPTION
          oraz wyłączonym EMULATE_PREPARES) i showpage.php przez require_once z __DIR__.
       b) Pobierz opublikowane pozycje menu zapytaniem:
          SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id.
       c) Zwaliduj parametr $_GET['idp']:
            - jeżeli brak 'idp' lub pusty tekst -> aktywnyIdp = 'glowna'
            - jeżeli tekst pasuje do wzorca /^[a-z0-9-]{1,20}$/ -> aktywnyIdp = $_GET['idp']
            - w przeciwnym razie (tablica, niedozwolone znaki, >20 znaków) -> ustaw HTTP 404.
       d) JEŻELI aktywnyIdp != null TO:
            rekord = PokazPodstrone($pdo, aktywnyIdp)
            JEŻELI rekord != null TO:
              ustaw tytulWidoku = rekord['page_title'] oraz trescPodstrony = rekord['page_content']
            W PRZECIWNYM RAZIE:
              ustaw HTTP 404 i komunikat błędu.
  3. W bloku CATCH (Throwable):
       a) Zapisz datę, klasę wyjątku i komunikat błędu wyłącznie w lokalnym logu (logs/app.log).
       b) Wyczyść dane menu i treści, ustaw HTTP 500 oraz ogólny komunikat błędu.
  4. Wygeneruj dokument HTML:
       a) Wypisz <title> oraz etykiety linków w <nav> przez htmlspecialchars(..., ENT_QUOTES, 'UTF-8').
       b) Aktywną pozycję menu oznacz atrybutem aria-current="page", zachowaj osobny link do lab4.php.
       c) W <main> wypisz pobrany fragment HTML z bazy (bez wykonywania PHP) albo sekcję błędu 404/500.
KONIEC
```

---

## Dokumentacja prób i wyników testów

Weryfikację przeprowadzono w przeglądarce przy otwartych narzędziach deweloperskich (panel **Network / Sieć** oraz **Console / Konsola**).

| Próba | Kroki wykonania | Oczekiwany wynik | Rzeczywisty wynik |
| :--- | :--- | :--- | :--- |
| **1. Odczyt i aktualizacja treści** | 1. Otwarto `http://localhost/lab6/index.php` oraz `index.php?idp=`.<br>2. W phpMyAdmin zmieniono fragment treści w rekordzie `glowna`, odświeżono stronę (`F5`), a następnie przywrócono pierwotny tekst. | Oba adresy otwierają stronę `glowna` ze statusem `HTTP 200`. Zmiana w bazie jest widoczna od razu bez edycji plików PHP i HTML. | **Zgodny.** W panelu Network zarejestrowano status `GET 200`. Zmieniona treść pojawiła się natychmiast po odświeżeniu strony. |
| **2. Nowa strona i menu** | 1. Dodano w `page_list` nowy opublikowany rekord (`alias = 'o-nas'`, `page_title = 'O nas'`, `status = 1`) i odświeżono stronę.<br>2. Kliknięto nowy link w menu, a po sprawdzeniu usunięto rekord testowy. | Nowy link pojawia się w menu, otwiera podstronę ze statusem `HTTP 200` bez zmiany kodu PHP, a aktywny link ma `aria-current="page"`. | **Zgodny.** Link „O nas” pojawił się w menu, otworzył treść (`GET 200`) i otrzymał atrybut `aria-current="page"`. |
| **3. Szkic i publikacja** | 1. Sprawdzono widoczność rekordu `szkic` (`status = 0`) w menu oraz pod adresem `?idp=szkic`.<br>2. Zmieniono `status` na `1`, odświeżono stronę, po czym przywrócono `0`.<br>3. Próbowano dodać drugi rekord z aliasem `glowna`. | Rekord ze statusem `0` nie pojawia się w menu i zwraca `HTTP 404`. Po zmianie na `1` jest widoczny i zwraca `200`. Duplikat aliasu jest odrzucany przez bazę. | **Zgodny.** Dla `status = 0` brak linku w menu i status `GET 404`. Po ustawieniu `1` strona działa (`GET 200`). Próba dodania duplikatu `glowna` została odrzucona przez bazę błędem unikalności klucza `alias`. |
| **4. Błędne dane w adresie** | Wywołano kolejno adresy:<br>- `?idp=nieznana`<br>- `?idp[]=filmy`<br>- `?idp=../cfg.php`<br>- `?idp=' OR 1=1--` | Każda próba zwraca `HTTP 404` z komunikatem błędu i linkiem powrotnym, bez żadnych ostrzeżeń PHP. | **Zgodny.** Wszystkie 4 żądania zwróciły w Network kod `GET 404 Not Found` oraz czysty widok błędu bez ostrzeżeń PHP. |
| **5. Awaria bazy** | 1. W `cfg.php` ustawiono tymczasowo błędny port `$dbPort = 3307;`.<br>2. Odświeżono `index.php`, sprawdzono kod odpowiedzi oraz zawartość `logs/app.log`.<br>3. Przywrócono port `3306`. | Strona zwraca `HTTP 500` z ogólnym komunikatem bez danych dostępowych, SQL ani ścieżek, a przyczyna zapisuje się w lokalnym logu. | **Zgodny.** W Network widoczny status `GET 500 Internal Server Error`, na stronie ogólny komunikat awarii, a w `logs/app.log` zapisał się wpis `PDOException` z przyczyną błędu połączenia. |
| **6. Funkcje po migracji** | 1. Sprawdzono style CSS, przełącznik motywu, zegar, obrazy piłek, animacje jQuery, AJAX (`status.php`) oraz 3 filmy YouTube na `?idp=filmy`.<br>2. W `lab4.php` przefiltrowano produkty, zapisano wybór w formularzu i przełączono podstrony. | Wszystkie zasoby, animacje, AJAX i filmy działają. Katalog filtruje produkty, zapisuje wybór (`POST 303`, potem `GET 200`) i zachowuje go w sesji. | **Zgodny.** Zasoby, skrypty i 3 filmy działają bez błędów w konsoli. Katalog wykonuje `POST 303` -> `GET 200`, a wybór pozostaje aktywny po przejściu między podstronami. |
| **7. Odtworzenie środowiska** | 1. Utworzono pustą bazę testową `moja_strona_test`.<br>2. Zaimportowano schemat i dane, wskazano bazę testową w `cfg.php` i uruchomiono projekt.<br>3. Przywrócono `moja_strona` i usunięto bazę testową. | Aplikacja uruchamia się zgodnie z instrukcją w `README.md` i poprawnie wyświetla polskie znaki (`utf8mb4`). | **Zgodny.** Po imporcie schematu i danych oraz wskazaniu nowej bazy projekt działa poprawnie, a polskie znaki renderują się bez błędów. |