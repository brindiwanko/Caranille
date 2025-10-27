<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'est pas dans un lieu on le redirige vers la carte du monde
if ($characterPlaceId == 0) { exit(header("Location: ../../modules/map/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On fait une jointure entre les 3 tables car_shops, car_places, car_places_shops pour récupérer les magasin lié à le lieu
$shopQuery = $bdd->prepare("SELECT * FROM car_shops, car_places, car_places_shops
WHERE placeShopShopId = shopId
AND placeShopPlaceId = placeId
AND placeId = ?");
$shopQuery->execute([$placeId]);
$shopRow = $shopQuery->rowCount();

//Si plusieurs magasins ont été trouvé
if ($shopRow > 0)
{
    ?>

    <form method="POST" action="selectedShop.php">
        Liste des magasins : <select name="shopId" class="form-control">

            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($shop = $shopQuery->fetch())
            {
                //On récupère les informations du magasin
                $shopId = $shop['shopId']; 
                $shopName = $shop['shopName'];
                ?>
                <option value="<?php echo $shopId ?>"><?php echo $shopName ?></option>
                <?php
            }
            ?>

        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="enter" class="btn btn-secondary btn-lg" value="Entrer dans le magasin">
    </form>
    
    <?php
}
//S'il n'y a aucun magasin de disponible on prévient le joueur
else
{
    echo "Il n'y a aucun magasin de disponible.";
}
$shopQuery->closeCursor();

require_once("../../html/footer.php"); ?>