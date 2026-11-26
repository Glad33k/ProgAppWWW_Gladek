<?php
    session_start();
    require_once __DIR__ . '/includes/produkty.php';
    $tytulStrony = "Katalog PHP";
    $identyfikatorGrupy = "ISI-GR2"; 
    $filtr = 'wszystkie';
    $blad = '';
    if (isset($_GET['kategoria'])){
        $kat = $_GET['kategoria'];
        if(is_string($kat) && in_array($kat, ['wszystkie','ksiazki','akcesoria'])){
            $filtr = $kat;
        } else {
            $blad = "Nieznana kategoria. Wyświetlam pełną listę.";
        }
    }

    $bladPost = '';
    if($_SERVER['REQUEST_METHOD']==='POST'){
        $rawProdukt = $_POST['produkt'] ?? null;
        $rawIlosc = $_POST['ilosc'] ?? null;
        $produktId = is_string($rawProdukt)? filter_var($rawProdukt, FILTER_VALIDATE_INT) : false;
        $iloscWybrana = is_string($rawIlosc) ? filter_var($rawIlosc, FILTER_VALIDATE_INT) : false;
        if ($produktId ===false || $iloscWybrana ===false){
            http_response_code(400);
            $bladPost="Błąd: Produkt i ilość muszą być liczbami całkowitymi.";
        }else{
            $znalezionyProdukt= null;
            foreach($produkty as $p){
                if($p['id'] === $produktId){
                $znalezionyProdukt = $p;
                break;
                }
            
        }
        if($znalezionyProdukt === null){
            http_response_code(400);
            $bladPost="Błąd: Wybrany produkt nie istnieje w katalogu.";
        } elseif ($iloscWybrana <1 || $iloscWybrana >5){
            http_response_code(400);
            $bladPost = "Błąd: Ilość musi być liczbą z zakresu od 1 do 5.";
        } elseif($iloscWybrana > $znalezionyProdukt['stan']){
            http_response_code(400);
            $bladPost="Błąd: Brak wystarczającej liczby sztuk (dostępny stan: " . $znalezionyProdukt['stan'] . ").";
        } else {
            $_SESSION['ostatni_wybor'] = [
                'id' => $produktId,
                'ilosc' => $iloscWybrana
            ];

            header('Location: lab4.php',true,303);
            exit;
        }
    }
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tytulStrony; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body>
     <?php include __DIR__ . '/includes/naglowek.php';?>
    <main>
        <h1><?php echo $tytulStrony; ?></h1>
        <p>Identyfikator grupy: <strong><?php echo $identyfikatorGrupy; ?></strong></p>
        <section>
            <h2>Ostatni zapisany wybór </h2>
            <?php if (isset($_SESSION['ostatni_wybor'])){
                $wybraneId= $_SESSION['ostatni_wybor']['id'];
                $wybranaIlosc = $_SESSION['ostatni_wybor']['ilosc'];
                $nazwaWybranego = null;
                foreach($produkty as $p){
                    if($p['id']===$wybraneId){
                        $nazwaWybranego = $p['nazwa'];
                        break;
                    }
                }
                if($nazwaWybranego !==null){
                    echo "<p>Wybrany produkt: " . htmlspecialchars($nazwaWybranego, ENT_QUOTES, 'UTF-8') . ", ilość: " . htmlspecialchars((string)$wybranaIlosc,ENT_QUOTES,'UTF-8') . " szt.</p>";
                } else{
                    echo "<p>Zapisany produkt nie znajduje się już w katalogu.</p>";
                }
                }else {
                    echo "<p>Brak zapisanego wyboru.</p>";
                }
                
            ?>
            </section>
        <section>
            <h2>Filtruj produkty</h2>
            <?php if ($blad !==''): ?>
                <p> <?php echo $blad; ?></p>
            <?php endif; ?>
            <form action="lab4.php" method="get">
                <label for="kategoria">Kategoria:</label>
                <select name="kategoria" id="kategoria">
                    <option value="wszystkie" <?php if($filtr==='wszystkie') echo 'selected'; ?>>Wszystkie</option>
                    <option value="ksiazki" <?php if($filtr=='ksiazki') echo 'selected'; ?>>Książki </option>
                    <option value="akcesoria" <?php if($filtr === 'akcesoria') echo 'selected'; ?>>Akcesoria </option>
                </select>
                <button type="submit">Filtruj</button>
            </form>
        </section>
        <section>
            <h2>Lista produktów </h2>
            <ul>
                <?php
                $i=0;
                $ilosc=count($produkty);
                while($i < $ilosc){
                    $p = $produkty[$i];
                    $i++;
                    if($filtr!=='wszystkie' && $p['kategoria'] !== $filtr){
                        continue;
                    }
                    switch ($p['kategoria']){
                        case 'ksiazki':
                            $czytelnakategoria = 'Książka';
                            break;
                        case 'akcesoria':
                            $czytelnakategoria = 'Akcesorium';
                            break;
                        default:
                            $czytelnakategoria = 'Inna kategoria';
                            break;
                    }
                    if($p['stan']==0){
                        $komunikat='Niedostępny';
                    } elseif ($p['stan'] >=1 && $p['stan'] <=5){
                        $komunikat = 'Niski stan';
                    } else {
                        $komunikat = 'Dostępny';
                    }
                    echo "<li>";
                    echo "<strong>" . htmlspecialchars($p['nazwa'],ENT_QUOTES,'UTF-8'). "</strong>";
                    echo " [Kategoria: $czytelnakategoria] - <em>$komunikat</em>";
                    echo "</li>";
                }   
                ?>
            </ul>
        </section> 
        
        <section>
            <h2>Wybierz produkt </h2>
            <?php if ($bladPost !==''): ?>
                <p style="color: red; font-weight: bold;"><?php echo $bladPost;?></p>
            <?php endif; ?>

            <form action="lab4.php" method="post">
                <label for="produkt">Produkt:</label>
                <select name="produkt" id="produkt">
                    <?php foreach ($produkty as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['nazwa'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="ilosc">Ilość:</label>
                <select name="ilosc" id="ilosc">
                    <?php for($j=1; $j<=5; $j++):?>
                        <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit">Zapisz wybór</button>
            </form>
        </section>
    </main>

</body>
</html>