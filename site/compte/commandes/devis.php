<html>
    <head>
        <title>Disguise'Hub</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/~saephp11/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/~saephp11/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/~saephp11/img/favicon/favicon-16x16.png">
    <meta name="theme-color" content="#DE6E22">
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="../../css/general.css">
        <script type="text/javascript" src="../../include/fontawesome.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

        <?php
            include("../../include/header.php");

            // Récupérer les articles du panier à partir de la session ou de la base de données
            $cartItems = []; // Remplacer par votre code pour récupérer les articles du panier

            // Calculer le prix total en fonction des articles du panier
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['price'];
            }

            // Générer le contenu du fichier de devis
            $quoteContent = "Quote for your cart items:\n\n";
            foreach ($cartItems as $item) {
                $quoteContent .= "- " . $item['name'] . ": $" . $item['price'] . "\n";
            }
            $quoteContent .= "\nTotal Price: $" . $totalPrice;

            // Enregistrer le fichier de devis
            $quoteFilePath = "/path/to/save/quote.txt"; // Remplacer avec votre chemin de fichier désiré
            file_put_contents($quoteFilePath, $quoteContent);
        ?>

        <?php include("../../include/footer.php"); ?>

    </body>
</html>