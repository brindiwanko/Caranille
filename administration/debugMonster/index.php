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
    
    <p>Ici vous aller pouvoir tester un monstre à des fins de teste.</p>
    
    <form method="POST" action="testMonster.php">
        Liste des monstres : <select name="adminMonsterId" class="form-control">
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($monster = $monsterQuery->fetch())
            {
                $adminMonsterId = $monster['monsterId'];
                $adminMonsterName = $monster['monsterName'];
                $adminMonsterLimited = $monster['monsterLimited'];
                $adminMonsterQuantity = $monster['monsterQuantity'];

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
        <input type="submit" name="test" class="btn btn-secondary btn-lg" value="Tester le monstre">
    </form>
    
    <?php
}
//S'il n'y a aucun monstre on prévient le joueur
else
{
    echo "Il n'y a actuellement aucun monstre";
}

require_once("../html/footer.php");