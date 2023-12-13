<html>

<head>
    <title>Panier - Disguise'Hub</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/general.css">
    <link rel="stylesheet" type="text/css" href="css/panier.css">
    <script type="text/javascript" src="include/fontawesome.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <?php
        // TEMPORAIRE : Création cookie panier factice
        if (empty($_COOKIE['cart'])) {
            $cart = array(
                "100000" => 1,
                "100001" => 53,
                "100002" => 3,
                "198" => 4
            );
            setcookie('cart', json_encode($cart), time() + (86400 * 30), '/');
        }
        // PENSER A SUPPRIMER CECI LORSQUE PAGES PRODUITS SERONT FAITES
    ?>

    <?php include("include/header.php"); ?>

    <?php
        $cartedit = false;

        // Vérifiez si le formulaire est soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $id = $_POST["id"];
            $amount = $_POST["amount"];

            $cart = [];
            if (isset($_COOKIE["cart"])) {
                $cart = json_decode($_COOKIE["cart"], true);
            }

            $sql = "SELECT * FROM Produit WHERE refProduit = :ref";
            $req = $conn -> prepare($sql);
            $req -> execute(["ref" => $id]);
            $nbresult = $req -> rowCount();

            function ajout() {
                global $cart, $id, $req, $nbresult;
                if ($nbresult == 0) {
                    echo "<script>alert('Le produit \"" . $id . "\" est introuvable.')</script>";
                    unset($cart[$id]);
                } else if ($nbresult > 1) {
                    echo "<script>alert('Une erreur est survenue avec le produit \"" . $id . "\".')</script>";
                    unset($cart[$id]);
                } else {
                    $product = $req -> fetch();
                    if ($product["qteProduit"] >= $cart[$id] + 1) {
                        $cart[$id]++;
                    } else {
                        if (isset($_POST["commander"])) {
                            header("Location: /product.php?id=" . $id . "&erreur=Produit+en+rupture+de+stock");
                            exit();
                        } else {
                            echo "<script>alert('Ajout impossible, le produit \"" . $product["nomProduit"] . "\" est en rupture de stock.')</script>";
                        }
                    }
                }
            }
            
            switch (true) {
                case isset($_POST["moins"]):
                    if (isset($cart[$id])) {
                        $cart[$id]--;
                        if ($cart[$id] <= 0) {
                            unset($cart[$id]);
                        }
                    }
                    break;
                case isset($_POST["plus"]):
                    ajout();
                    break;
                case isset($_POST["supprime"]):
                    unset($cart[$id]);
                    break;
                case isset($_POST["ajuste"]):
                    if ($nbresult == 1) {
                        $product = $req -> fetch();
                        if ($product["qteProduit"] == 0) {
                            unset($cart[$id]);
                        } else {
                            $cart[$id] = $product["qteProduit"];
                        }
                    } else {
                        unset($cart[$id]);
                    }
                    break;
                case isset($_POST["commander"]):
                    ajout();
            }
            
            if (count($cart) == 0) {
                setcookie('cart', '', time() - 3600, '/');
            } else {
                setcookie('cart', json_encode($cart), time() + (86400 * 30), '/');
            }

            if (isset($_POST["commander"])) {
                header("Location: /product.php?id=$id");
                exit();
            }

            $cartedit = true;
        }
        
    ?>

    <div class="content">

        <div class="panier">

            <h1>Panier</h1>

            <?php
                
                // Récupération du panier dans le cookie (si non modifié au-dessus)
                if (!$cartedit && isset($_COOKIE["cart"]) && !empty(json_decode($_COOKIE["cart"], true))) {
                    $cart = json_decode($_COOKIE["cart"], true);
                }

                // Panier trouvé et non vide
                if (isset($cart) && !empty($cart)) {

                    echo "<table>
                        <tr class='head'>
                                <td class='nom'>Nom</td>
                            <td class='couleur'>Couleur</td>
                            <td class='taille'>Taille</td>
                            <td class='prix'>Prix</td>
                            <td class='quantite'>Quantité</td>
                        </tr>";

                        $total = 0;

                        foreach ($cart as $id => $amount) {
                            
                            echo "<tr>
                                <form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='POST'>";

                            $sql = "SELECT * FROM Produit WHERE refProduit = :ref";
                            $req = $conn -> prepare($sql);
                            $req -> execute(["ref" => $id]);
                            
                            // Cas d'erreurs
                            if ($req && $req->rowCount() != 1) {
                                echo "<td class='erreur' colspan='5'>
                                    <div>
                                        <input type='hidden' name='id' value='" . $id . "'>
                                        <input type='hidden' name='amount' value='" . $amount . "'>";
                                        if ($req->rowCount() > 1) {
                                            echo "<a>Une erreur est survenue avec le produit \"" . $id . "\"</a>";
                                        } else if ($req->rowCount() == 0) {
                                            echo "<a>Produit \"" . $id . "\" introuvable</a>";
                                        }
                                        echo "<button type='submit' name='ajuste' class='ignore'>
                                            <i class='fa-regular fa-xmark' style='color: #FFFFFF;'></i>
                                        </button>
                                    </div>
                                </td>";

                                // Produit trouvé
                            } else {
                                $product = $req -> fetch();

                                // Rupture de stock
                                if ($product["qteProduit"] < $amount) {
                                    echo "<td class='erreur' colspan='5'>
                                        <div>
                                            <input type='hidden' name='id' value='" . $id . "'>
                                            <input type='hidden' name='amount' value='" . $amount . "'>";
                                            if ($product["qteProduit"] == 0) {
                                                echo "<a>Produit \"" . $product["nomProduit"] . "\" en rupture de stock</a>
                                                <button type='submit' name='ajuste' class='ignore'>
                                                    <i class='fa-regular fa-xmark' style='color: #FFFFFF;'></i>
                                                </button>";
                                            } else {
                                                echo "<a>Il ne reste que " . $product["qteProduit"] . " exemplaires du produit \"" . $product["nomProduit"] . "\"</a>
                                                <button type='submit' name='ajuste' class='ignore'>
                                                    <i class='fa-regular fa-circle-minus' style='color: #FFFFFF;'></i>
                                                </button>";
                                            }
                                        echo "</div>
                                    </td>";

                                // Affichage normal
                                } else {
                                    $total += $product["prixProduit"] * $amount;
                                    echo "<td class='nom'>
                                        <input type='hidden' name='id' value='" . $id . "'>
                                        <a>" . $product["nomProduit"] . "</a>
                                    </td>
                                    <td class='couleur'><a>" . $product["couleurProduit"] . "</a></td>
                                    <td class='taille'><a>" . $product["tailleProduit"] . "</a></td>
                                    <td class='prix'><a>" . number_format($product["prixProduit"], 2, ",", " ") . " €</a></td>
                                    <td class='quantite'>
                                        <input type='hidden' name='amount' value='" . $amount . "'>
                                        <button type='submit' name='moins' class='ignore'>
                                            <i class='fa-regular fa-circle-minus'></i>
                                        </button><a>" . $amount . "</a>";
                                        if ($product["qteProduit"] >= $amount + 1) {
                                            echo "<button type='submit' name='plus' class='ignore'>
                                                <i class='fa-regular fa-circle-plus'></i>
                                            </button>";
                                        } else {
                                            echo "<button type='submit' name='plus' class='ignore' style='cursor: not-allowed;' disabled>
                                                <i class='fa-regular fa-circle-plus'></i>
                                            </button>";
                                        }
                                    echo "</td>";
                                }
                            }
                                echo "</form>
                            </tr>";
                        }

                        echo "</table>
                    </div>";

                    echo "<div class='commande'>
                        <div class='prix'>
                            <p>Total :</p>
                            <a>" . number_format($total, 2, ",", " ") . " €</a>
                        </div>";

                    // Utilisateur non connecté
                    if (!isset($_SESSION["connexion"])) {
                            echo "<a class='button' href='/~saephp11/compte'>Se connecter pour commander</a>";

                    // Utilisateur connecté (formulaire de commande)
                    } else {
                        echo "<form action='" . $_SERVER["PHP_SELF"] . "' method='POST'>
                            <label class='subtitle'>Adresse</label>
                            <input type='text' name='adresse' autocomplete='street-address' required>
                    
                            <label class='subtitle'>Ville</label>
                            <input type='text' name='ville' autocomplete='address-level2' required>

                            <label class='subtitle'>Code postal</label>
                            <input type='number name='codePostal' autocomplete='postal-code' required>
                    
                            <label class='subtitle'>Pays</label>
                            <input type='text' name='pays' autocomplete='country-name' required>
                            
                            <button type='submit' name='commander'>Commander</button>
                        </form>";
                    }

                } else {
                    echo "<p>Votre panier est vide.</p>";
                }
            ?>

        </div>
        
    </div>

    <?php include("include/footer.php"); ?>

</body>

</html>