<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminMonsterDropMonsterId'])
&& isset($_POST['adminMonsterDropItemId'])
&& isset($_POST['token'])
&& isset($_POST['finalDelete']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminMonsterDropMonsterId'])
        && ctype_digit($_POST['adminMonsterDropItemId'])
        && $_POST['adminMonsterDropMonsterId'] >= 1
        && $_POST['adminMonsterDropItemId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminMonsterDropMonsterId = htmlspecialchars($_POST['adminMonsterDropMonsterId']);
            $adminMonsterDropItemId = htmlspecialchars($_POST['adminMonsterDropItemId']);

            //On fait une requête pour vérifier si le monstre choisit existe
            $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters 
            WHERE monsterId = ?");
            $monsterQuery->execute([$adminMonsterDropMonsterId]);
            $monsterRow = $monsterQuery->rowCount();

            //Si le monstre existe
            if ($monsterRow == 1) 
            {
                //On fait une requête pour vérifier si l'objet choisit existe
                $itemQuery = $bdd->prepare("SELECT * FROM car_items 
                WHERE itemId = ?");
                $itemQuery->execute([$adminMonsterDropItemId]);
                $itemRow = $itemQuery->rowCount();

                //Si l'objet existe
                if ($itemRow == 1) 
                {
                    //On fait une requête pour vérifier si l'objet est sur ce monstre
                    $monsterDropQuery = $bdd->prepare("SELECT * FROM car_monsters_drops 
                    WHERE monsterDropMonsterID = ?
                    AND monsterDropItemID = ?");
                    $monsterDropQuery->execute([$adminMonsterDropMonsterId, $adminMonsterDropItemId]);
                    $monsterDropRow = $monsterDropQuery->rowCount();

                    //Si cet objet est sur le monstre
                    if ($monsterDropRow == 1) 
                    {
                        //On supprime les objets et équippements du monstre
                        $monsterDropDeleteQuery = $bdd->prepare("DELETE FROM car_monsters_drops
                        WHERE monsterDropItemID = ?");
                        $monsterDropDeleteQuery->execute([$adminMonsterDropItemId]);
                        $monsterDropDeleteQuery->closeCursor();
                        ?>

                        L'objet a bien été retiré du monstre

                        <hr>
                            
                        <form method="POST" action="manageMonsterDrop.php">
                            <input type="hidden" name="adminMonsterDropMonsterId" value="<?php echo $adminMonsterDropMonsterId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Continuer">
                        </form>
                        
                        <?php
                    }
                    //Si l'objet n'exite pas
                    else
                    {
                        echo "Erreur : Objet indisponible";
                    }
                    $monsterDropQuery->closeCursor();
                }
                //Si l'objet existe pas
                else
                {
                    echo "Erreur : Objet indisponible";
                }
                $itemQuery->closeCursor();
            }
            //Si le monstre existe pas
            else
            {
                echo "Erreur : Ce monstre n'existe pas";
            }
            $monsterQuery->closeCursor();
        }
        //Si tous les champs numérique ne contiennent pas un nombre
        else
        {
            echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
        }
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");