<?php
    $idProjektu = "sklep-pilkarski-v1.5";
    $pozycjeMenu = [];
    $aktywnyIdp = null;
    $trescPodstrony = null;
    $tytulWidoku = 'Piłki - Błąd';
    $kodBledu = 0;
    $komunikatBledu = '';

    try {
        require_once __DIR__ . '/cfg.php';
        require_once __DIR__ . '/showpage.php';

        $stmtMenu = $pdo->query('SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id');
        $pozycjeMenu = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);

        if (!isset($_GET['idp']) || $_GET['idp'] === '') {
            $aktywnyIdp = 'glowna';
        } elseif (is_string($_GET['idp']) && preg_match('/^[a-z0-9-]{1,20}$/', $_GET['idp']) === 1) {
            $aktywnyIdp = $_GET['idp'];
        } else {
            http_response_code(404);
            $kodBledu = 404;
            $tytulWidoku = '404 - Nie znaleziono podstrony';
            $komunikatBledu = 'Wybrana podstrona nie istnieje.';
        }

        if ($aktywnyIdp !== null) {
            $rekordStrony = PokazPodstrone($pdo, $aktywnyIdp);
            if ($rekordStrony !== null) {
                $tytulWidoku = $rekordStrony['page_title'];
                $trescPodstrony = $rekordStrony['page_content'];
            } else {
                http_response_code(404);
                $kodBledu = 404;
                $tytulWidoku = '404 - Nie znaleziono podstrony';
                $komunikatBledu = 'Wybrana podstrona nie istnieje.';
            }
        }
    } catch (Throwable $e) {
        $katalogLogow = __DIR__ . '/logs';
        if (!is_dir($katalogLogow)) {
            mkdir($katalogLogow, 0750, true);
        }
        $wpisLogu = '[' . date('Y-m-d H:i:s') . '] ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
        error_log($wpisLogu, 3, $katalogLogow . '/app.log');

        http_response_code(500);
        $kodBledu = 500;
        $pozycjeMenu = [];
        $aktywnyIdp = null;
        $trescPodstrony = null;
        $tytulWidoku = '500 - Błąd serwera';
        $komunikatBledu = 'Wystąpił wewnętrzny błąd serwera. Spróbuj ponownie później.';
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
                foreach($pozycjeMenu as $pozycja){
                    $alias= $pozycja['alias'];
                    $link = ($alias === 'glowna') ? 'index.php' : 'index.php?idp=' . $alias;
                    $aktywny = ($aktywnyIdp === $alias && $kodBledu === 0) ? ' aria-current="page"' : '';
                    $etykieta = htmlspecialchars($pozycja['page_title'],ENT_QUOTES, 'UTF-8');
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
        <?php if ($trescPodstrony !== null ): ?>
            <?php echo $trescPodstrony; ?>
        <?php else: ?>
            <section>
                <h2>Błąd <?php echo $kodBledu; ?></h2>
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