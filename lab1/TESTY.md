Wersja przeglądarki: Opera GX Core:136.0.6008.111
1. Nu HTML Checker:

    oczekiwany wynik: brak błędów

    zaobserwowany wynik: jedna literówka w article class.

    Poprawa - poprawa literówki

    Po wyłączeniu css: Strona nadal czytelna, nagłówki są po kolei najpierw h1, potem menu a na końcu stopka.

2. Widoki - rezponsywnosc

    oczekiwany wynik: ponizej 768px 1 kolumna, powyzej 3 kolumny, brak uciętych tekstów i suwaka na ekranie

    zaobserwowany wynik: Zdjęcia zachowują proporcję, kolumny pojawiają się zgodnie z założeniem, brak uciętych tekstów ani suwaka na ekranie.

3. klawaiatura - tab

    oczekiwany wynik: wyraźny obrys wokół aktywnego elementu 
    
    zaobserwowany wynik: Czerwony obrys wokół zaznaczonego elementu, po kliknięciu w etykietę kursor automatycznie ląduje w polu tekstowym

4. walidacja

    oczekiwany wynik: Przeglądarka zablokuje możliwość wysłania błędnego formularza

    zaobserwowany wynik: Przeglądarka blokuje wysłanie formularza, wyskakuje komunikat, że trzeba wpisać pole, czy np. wartość liczbowa musi mieścić się w 1-5. Po poprawnym wysłaniu w adresie pojawia sie: http://localhost:8000/kontakt.html?email=test%40gmail.com&produkt=produkt3&ilosc=3&wiadomosc=wiadomość+testowa

5. eksperyment

    oczekiwany wynik: brak parametru email w adresie url

    zaobserwowany wynik: tak wygląda adres url po usunięciu name: http://localhost:8000/kontakt.html?produkt=produkt1&ilosc=3&wiadomosc=Wiadomość+testowa

    wyjaśnienie: przeglądarka w zapytaniu get wysyła tylko wartości z atrybutem "name"

    metoda get w devtools: Request URL
http://localhost:8000/kontakt.html?email=testtest%40gmail.com&produkt=produkt1&ilosc=3&wiadomosc=wiadmo%C5%9B%C4%87+testowa
Request Method
GET


    




