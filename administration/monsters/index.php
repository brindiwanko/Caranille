<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//on récupère les valeurs de chaque monstres qu'on va ensuite mettre dans le menu déroulant
//On fait une recherche dans la base de donnée de tous les monstres
$monsterQuery = $bdd->query("SELECT * FROM car_monsters");
$monsterRow = $monsterQuery->rowCount();

//S'il existe un ou plusieurs monstres on affiche le menu déroulant
if ($monsterRow > 0) 
{
    ?>
    <form method="POST" action="manageMonster.php">
        Liste des monstres : <select name="adminMonsterId" class="form-control">
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($monster = $monsterQuery->fetch())
            {
                $adminMonsterId = $monster['monsterId'];
                $adminMonsterName = $monster['monsterName'];
                $adminMonsterLimited = $monster['monsterLimited'];
                $adminMonsterQuantity = $monster['monsterQuantity'];
                $adminMonsterQuantityBattle = $monster['monsterQuantityBattle'];
                $adminMonsterQuantityEscaped = $monster['monsterQuantityEscaped'];
                $adminMonsterQuantityVictory = $monster['monsterQuantityVictory'];
                $adminMonsterQuantityDefeated = $monster['monsterQuantityDefeated'];
                $adminMonsterQuantityDraw = $monster['monsterQuantityDraw'];

                if ($adminMonsterLimited == "No")
                {
                    $adminMonsterLimitedValue = "Non";
                }
                else
                {
                    $adminMonsterLimitedValue = "Oui";
                }
                ?>
                <option value="<?php echo $adminMonsterId ?>"><?php echo "N°$adminMonsterId - $adminMonsterName (Limité : $adminMonsterLimitedValue - Restant : $adminMonsterQuantity)" ?></option>
                <?php
            }
            $monsterQuery->closeCursor();
            ?>
            
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="manage" class="btn btn-secondary btn-lg" value="Gérer le monstre">
    </form>
    
    <?php
}
//S'il n'y a aucun monstre on prévient le joueur
else
{
    echo "Il n'y a actuellement aucun monstre";
}
?>

<hr>

Générer X montre(s) vierge.

<form method="POST" action="generateMonster.php">
    Quantité : <input type="number" name="adminQuantityMonsterGenerate" class="form-control" placeholder="Quantité" value="1" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="generate" value="Générer le(s) monstre(s)">
</form>

<?php require_once("../html/footer.php");