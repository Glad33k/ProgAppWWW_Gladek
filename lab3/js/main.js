function inicjalizujMotyw() {
    const przyciskMotywu = document.getElementById('przycisk-motyw');
    
    if (!przyciskMotywu) return;

    let zapisanyMotyw = localStorage.getItem('motyw');
    if (zapisanyMotyw !== 'dark') {
        zapisanyMotyw = 'light';
    }

    function ustawMotyw(motyw) {
        document.documentElement.setAttribute('data-theme', motyw);
        localStorage.setItem('motyw', motyw);
        
        if (motyw === 'dark') {
            przyciskMotywu.setAttribute('aria-pressed', 'true');
            przyciskMotywu.textContent = 'Jasny motyw';
        } else {
            przyciskMotywu.setAttribute('aria-pressed', 'false');
            przyciskMotywu.textContent = 'Ciemny motyw';
        }
    }

    ustawMotyw(zapisanyMotyw);

    przyciskMotywu.addEventListener('click', function() {
        const aktualnyMotyw = document.documentElement.getAttribute('data-theme');
        const nowyMotyw = (aktualnyMotyw === 'dark') ? 'light' : 'dark';
        ustawMotyw(nowyMotyw);
    });
}

inicjalizujMotyw();

function inicjalizujZegar() {
    const wyswietlacz = document.getElementById('zegar-wyswietlacz');
    const btnStart = document.getElementById('btn-start');
    const btnStop = document.getElementById('btn-stop');

    if (!wyswietlacz || !btnStart || !btnStop) return;

    let interwalZegara = null;

    const formatator = new Intl.DateTimeFormat('pl-PL', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });

    function aktualizujCzas() {
        const teraz = new Date();
        wyswietlacz.textContent = formatator.format(teraz);
    }

    function startZegara() {
        if (interwalZegara !== null) return; 
        interwalZegara = setInterval(aktualizujCzas, 1000);
    }

    function stopZegara() {
        clearInterval(interwalZegara);
        interwalZegara = null;
    }

    aktualizujCzas(); 
    startZegara();

    btnStart.addEventListener('click', startZegara);
    btnStop.addEventListener('click', stopZegara);
}

inicjalizujZegar();

function inicjalizujWyszukiwarke() {
    const poleSzukaj = document.getElementById('wyszukiwarka-produktow');
    const licznik = document.getElementById('licznik-wynikow');
    const komunikatBrak = document.getElementById('brak-wynikow');
    const karty = document.querySelectorAll('.karta-produktu');

    if (!poleSzukaj || karty.length === 0) return;

    poleSzukaj.addEventListener('input', function(event) {
        const zapytanie = event.target.value.trim().toLowerCase();
        let liczbaDopasowan = 0;

        karty.forEach(karta => {
            const tytul = karta.querySelector('h3').textContent.toLowerCase();

            if (tytul.includes(zapytanie)) {
                karta.hidden = false;
                liczbaDopasowan++;
            } else {
                karta.hidden = true;
            }
        });
        licznik.textContent = `Znaleziono produktów: ${liczbaDopasowan}`;

        if (liczbaDopasowan === 0) {
            komunikatBrak.hidden = false;
        } else {
            komunikatBrak.hidden = true;
        }
    });
}

inicjalizujWyszukiwarke();

function inicjalizujFormularz() {
    const formularz = document.getElementById('formularz-kontaktowy');
    const poleProdukt = document.getElementById('pole-produkt');
    const poleIlosc = document.getElementById('pole-ilosc');
    const podgladCeny = document.getElementById('cena-podglad');
    const poleWiadomosc = document.getElementById('pole-wiadomosc');
    const bladWiadomosci = document.getElementById('blad-wiadomosci');
    const podsumowanie = document.getElementById('podsumowanie-wysylki');
    const podsumowanieDane = document.getElementById('podsumowanie-dane');
    const podsumowanieWiadomosc = document.getElementById('podsumowanie-wiadomosc');

    if (!formularz) return;

    const produktyDB = [
        { id: '1', nazwa: 'Piłka do piłki nożnej', cenaGrosze: 1999 },
        { id: '2', nazwa: 'Piłka do baseballa', cenaGrosze: 2499 },
        { id: '3', nazwa: 'Piłka do tenisa ziemnego', cenaGrosze: 999 }
    ];

    const formatatorPLN = new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
    });

    function obliczWartosc(cenaGrosze, ilosc) {
        return cenaGrosze * ilosc;
    }

    function aktualizujPodglad() {
        podsumowanie.hidden = true; 

        const idProduktu = poleProdukt.value;
        const ilosc = Number(poleIlosc.value); 
        const produkt = produktyDB.find(p => p.id === idProduktu);

        if (!produkt || !Number.isInteger(ilosc) || ilosc < 1 || ilosc > 5) {
            podgladCeny.textContent = 'Wybierz produkt i poprawną ilość (1-5 sztuk), aby zobaczyć cenę.';
            return;
        }

        const wartoscGrosze = obliczWartosc(produkt.cenaGrosze, ilosc);
        podgladCeny.textContent = `Aktualna wartość: ${formatatorPLN.format(wartoscGrosze / 100)}`;
    }

    poleProdukt.addEventListener('change', aktualizujPodglad);
    poleIlosc.addEventListener('input', aktualizujPodglad);
    
    poleWiadomosc.addEventListener('input', () => {
        bladWiadomosci.hidden = true; 
        podsumowanie.hidden = true;
    });

    formularz.addEventListener('submit', function(event) {
        event.preventDefault();

        const idProduktu = poleProdukt.value;
        const ilosc = Number(poleIlosc.value);
        const wiadomosc = poleWiadomosc.value.trim();
        const produkt = produktyDB.find(p => p.id === idProduktu);

        if (!produkt || !Number.isInteger(ilosc) || ilosc < 1 || ilosc > 5) return;

        if (wiadomosc.length < 10) {
            bladWiadomosci.hidden = false;
            poleWiadomosc.focus();
            return;
        }

        const wartoscGrosze = obliczWartosc(produkt.cenaGrosze, ilosc);
        podsumowanieDane.textContent = `Wybrany produkt: ${produkt.nazwa} (${ilosc} szt.). Wartość całkowita: ${formatatorPLN.format(wartoscGrosze / 100)}.`;
        podsumowanieWiadomosc.textContent = `Wiadomość: ${wiadomosc}`;
        
        podsumowanie.hidden = false;
    });
}

inicjalizujFormularz();