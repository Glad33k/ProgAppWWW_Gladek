<?php
    $idProjektu="sklep-pilkarski-v1";
    $mapaPodstron = [
        'glowna' => [
            'etykieta' => 'Strona główna',
            'tytul' => 'Piłki - Strona Główna',
            'plik' => 'html/glowna.html'
        ],
        'produkt' => [
            'etykieta' => 'Produkt',
            'tytul' => 'Piłki - Szczegóły produktu',
            'plik' => 'html/produkt.html'
        ],
        'kontakt' => [
            'etykieta' => 'Kontakt',
            'tytul' => 'Piłki - Kontakt',
            'plik' => 'html/kontakt.html'
        ],
        'filmy' => [
            'etykieta' => 'Filmy',
            'tytul' => 'Piłki - Filmy',
            'plik' => 'html/filmy.html'
        ]
    ];
    $aktywnyIdp = null;
    $plikDoDolaczenia = null;
    $tytulWidoku = 'Piłki - Błąd';
    $kodBledu = 0;
    $komunikatBledu = '';
    if (!isset($_GET['idp']) || $_GET['idp'] === ''){
        $aktywnyIdp = 'glowna';
    } elseif (is_string($_GET['idp']) && array_key_exists($_GET['idp'], $mapaPodstron)){
        $aktywnyIdp = $_GET['idp'];
    } else {
        http_response_code(404);
        $kodBledu = 404;
        $tytulWidoku = '404 - Nie znaleziono podstrony';
        $komunikatBledu = 'Wybrana podstrona nie istnieje.';
    }
    if($aktywnyIdp !== null){
        $wybranyWpis = $mapaPodstron[$aktywnyIdp];
        $tytulWidoku = $wybranyWpis['tytul'];
        $sciezkaPliku = __DIR__ . '/' . $wybranyWpis['plik'];
        if(is_file($sciezkaPliku) && is_readable($sciezkaPliku)){
            $plikDoDolaczenia = $sciezkaPliku;
        }
        else {
            http_response_code(500);
            $kodBledu = 500;
            $tytulWidoku = '500 - Błąd serwera';
            $komunikatBledu = 'Wystąpił wewnętrzny błąd serwera podczas ładowania widoku.';
        }
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($tytulWidoku, ENT_QUOTES, 'UTF-8');?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/lab3.css">
    <script src="vendor/jquery-4.0.0.min.js" defer></script>
    <script src="js/lab3.js" defer></script>
    <script src="js/main.js" defer></script>
</head>
<body>
    <header>
        <div class="naglowek-gora">
        <h1>Witam w sklepie z piłkami</h1>
        <div class="panel-zegara" style="margin-bottom: 20px;">
    <p id="zegar-wyswietlacz">Ładowanie czasu...</p>
    <button type="button" id="btn-start">Start zegara</button>
    <button type="button" id="btn-stop">Stop zegara</button>
</div>
    </div>
        <div class="nawigacja">
        <nav>
            <ul>
                <?php
                foreach($mapaPodstron as $klucz => $dane){
                    $link = ($klucz === 'glowna') ? 'index.php' : 'index.php?idp=' . $klucz;
                    $aktywny = ($aktywnyIdp === $klucz && $kodBledu === 0) ? ' aria-current="page"' : '';
                    $etykieta = htmlspecialchars($dane['etykieta'],ENT_QUOTES, 'UTF-8');
                    echo "<li><a href=\"$link\"$aktywny>$etykieta</a></li>";
                    }
                ?>
                <li><a href="lab4.php">Katalog PHP</a></li>
            </ul>
        </nav>
        <button type="button" id="przycisk-motyw" aria-pressed="false">Ciemny motyw</button>
        </div>
    </header>
    <main>
        <?php if ($plikDoDolaczenia !== null):?>
            <?php include $plikDoDolaczenia; ?>
        <?php else: ?>
            <section>
                <h2>Błąd <?php echo $kodBledu;?></h2>
                <p><?php echo htmlspecialchars($komunikatBledu, ENT_QUOTES, 'UTF-8'); ?></p>    
                <p><a href="index.php">Powrót do strony głównej</a></p>
        </section>
        <?php endif; ?>
    </main>
    <footer>
        <p>Identyfikator projektu: <?php echo htmlspecialchars($idProjektu,ENT_QUOTES, 'UTF-8');?></p>
    </footer>
    
</body>
</html>