<?php
    require_once __DIR__ . '/includes/produkty.php';
    $tytulStrony = "Katalog PHP";
    $identyfikatorGrupy = "ISI-GR2"; 
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
</div>
    <main>
        <h1><?php echo $tytulStrony; ?></h1>
        <p>Identyfikator grupy: <strong><?php echo $identyfikatorGrupy; ?></strong></p>
        <p>Test: Załadowano <?php echo count($produkty); ?> produkty</p>
    </main>

</body>
</html>