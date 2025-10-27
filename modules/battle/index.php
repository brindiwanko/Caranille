<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il n'y a actuellement pas de combat on redirige le joueur vers l'accueil
if ($battleRow == 0) { exit(header("Location: ../../modules/main/index.php")); }

require_once("../../html/header.php");

//On calcul les MP nécessaire au lancement d'une attaque magique
$mpNeed = round($characterMagicTotal / 10);
?>

<p><img src="<?php echo $opponentPicture ?>" height="100" width="100"></p>

Combat de <?php echo $characterName ?> contre <?php echo $opponentName ?><br />

HP de <?php echo $characterName ?> : <?php echo "$characterHpMin/$characterHpTotal" ?><br />
MP de <?php echo $characterName ?> : <?php echo "$characterMpMin/$characterMpTotal" ?><br /><br />

HP de <?php echo $opponentName ?> : <?php echo "$battleOpponentHpRemaining/$opponentHp" ?><br />
MP de <?php echo $opponentName ?> : <?php echo "$battleOpponentMpRemaining/$opponentMp" ?><br /><br />

<hr>

<form method="POST" action="physicalAttack.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="attack" value="Attaque physique"><br>
</form>

<hr>

<form method="POST" action="magicAttack.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="magic" value="Attaque magique (<?php echo $mpNeed; ?> MP)"><br>
</form>

<hr>

<?php
//On cherche tous les objets que possède le joueur pour les utiliser en combat
$itemQuery = $bdd->prepare("SELECT * FROM  car_items, car_items_types, car_inventory 
WHERE itemItemTypeId = itemTypeId
AND itemId = inventoryItemId
AND itemTypeName = 'Item'
AND inventoryCharacterId = ?");
$itemQuery->execute([$characterId]);
$itemRow = $itemQuery->rowCount();

//Si un ou plusieurs objets ont été trouvé
if ($itemRow > 0)
{
    ?>
    
    <form method="POST" action="useItem.php">
        Objet(s) disponible : <select class="form-control" name="itemId" >
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($item = $itemQuery->fetch())
            {
                //On récupère les informations de l'objet
                $itemId = $item['itemId']; 
                $itemName = $item['itemName'];
                $itemQuantity = $item['inventoryQuantity'];
                ?>
                <option value="<?php echo $itemId ?>"><?php echo "$itemName ($itemQuantity disponible)"; ?></option>
                <?php
            }
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" class="btn btn-secondary btn-lg" name="useItem" value="Utiliser">
    </form>
    
    <?php
}
else 
{
    echo "Vous ne possédez aucun objet";
}
$itemQuery->closeCursor();
?>

<hr >

<form method="POST" action="escape.php">
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="escape" value="Abandonner"><br />
</form>

<?php require_once("../../html/footer.php"); ?>