<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//on récupère les valeurs de chaque magasin qu'on va ensuite mettre dans le menu déroulant
$shopQuery = $bdd->query("SELECT * FROM car_shops
ORDER by shopName");
$shopRow = $shopQuery->rowCount();

//S'il existe un ou plusieurs magasin(s) on affiche le menu déroulant
if ($shopRow > 0) 
{
    ?>
    
    <form method="POST" action="manageShop.php">
        Liste des magasins : <select name="adminShopId" class="form-control">
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($shop = $shopQuery->fetch())
            {
                $adminShopId = $shop['shopId'];
                $adminShopName = $shop['shopName'];
                ?>
                <option value="<?php echo $adminShopId ?>"><?php echo "$adminShopName"; ?></option>
                <?php
            }
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer le magasin">
    </form>
    
    <?php
}
//S'il n'y a actuellement aucun magasin on prévient le joueur
else
{
    echo "Il n'y a actuellement aucun magasin";
}
$shopQuery->closeCursor();
?>

<hr>

<form method="POST" action="addShop.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="add" value="Créer un magasin">
</form>

<?php require_once("../html/footer.php");