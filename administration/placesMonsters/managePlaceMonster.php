<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminplaceMonsterPlaceId'])
&& isset($_POST['token'])
&& isset($_POST['manage']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminplaceMonsterPlaceId'])
        && $_POST['adminplaceMonsterPlaceId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminplaceMonsterPlaceId = htmlspecialchars($_POST['adminplaceMonsterPlaceId']);

            //On fait une requête pour vérifier si le lieu choisit existe
            $placeQuery = $bdd->prepare("SELECT * FROM car_places 
            WHERE placeId = ?");
            $placeQuery->execute([$adminplaceMonsterPlaceId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe
            if ($placeRow == 1)
            {
                //On fait une requête pour rechercher tous les monstres présent dans cette lieu
                $placeMonsterQuery = $bdd->prepare("SELECT * FROM car_monsters, car_places, car_places_monsters
                WHERE placeMonsterMonsterId = monsterId
                AND placeMonsterPlaceId = placeId
                AND placeId = ?");
                $placeMonsterQuery->execute([$adminplaceMonsterPlaceId]);
                $placeMonsterRow = $placeMonsterQuery->rowCount();

                //S'il existe un ou plusieurs monstre dans le lieu on affiche le menu déroulant
                if ($placeMonsterRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="deletePlaceMonster.php">
                        Monstres présent dans le lieu : <select name="adminplaceMonsterMonsterId" class="form-control">
                                
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($placeMonster = $placeMonsterQuery->fetch())
                            {
                                //On récupère les informations du monstre
                                $adminplaceMonsterMonsterId = $placeMonster['monsterId'];
                                $adminPlaceMonsterMonsterName = $placeMonster['monsterName'];
                                ?>
                                <option value="<?php echo $adminplaceMonsterMonsterId ?>"><?php echo "N°$adminplaceMonsterMonsterId - $adminPlaceMonsterMonsterName"; ?></option>
                                <?php
                            }
                            $placeMonsterQuery->closeCursor();
                            ?>
                            
                        </select>
                        <input type="hidden" name="adminplaceMonsterPlaceId" value="<?php echo $adminplaceMonsterPlaceId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="delete" class="btn btn-secondary btn-lg" value="Retirer le monstre">
                    </form>
                    
                    <hr>

                    <?php
                }
                $placeMonsterQuery->closeCursor();

                //On fait une requête pour afficher la liste des monstres du jeu qui ne sont pas dans le lieu
                $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters
                WHERE (SELECT COUNT(*) FROM car_places_monsters
                WHERE placeMonsterPlaceId = ?
                AND placeMonsterMonsterId = monsterId) = 0");
                $monsterQuery->execute([$adminplaceMonsterPlaceId]);
                $monsterRow = $monsterQuery->rowCount();
                //S'il existe un ou plusieurs monstres on affiche le menu déroulant pour proposer au joueur d'en ajouter
                if ($monsterRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="addPlaceMonster.php">
                        Monstres disponible : <select name="adminplaceMonsterMonsterId" class="form-control">
                                
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($monster = $monsterQuery->fetch())
                            {
                                //On récupère les informations du monstre
                                $adminplaceMonsterMonsterId = $monster['monsterId'];
                                $adminPlaceMonsterMonsterName = $monster['monsterName'];
                                ?>
                                <option value="<?php echo $adminplaceMonsterMonsterId ?>"><?php echo "N°$adminplaceMonsterMonsterId - $adminPlaceMonsterMonsterName"; ?></option>
                                <?php
                            }
                            $monsterQuery->closeCursor();
                            ?>
                            
                        </select>
                        <input type="hidden" name="adminplaceMonsterPlaceId" value="<?php echo $adminplaceMonsterPlaceId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="add" class="btn btn-secondary btn-lg" value="Ajouter le monstre">
                    </form>
                    
                    <?php
                }
                //Si il n'y a actuellement aucun monstre dans le jeu
                else
                {
                    echo "Il n'y a actuellement aucun monstre";
                }
                ?>

                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le lieu n'exite pas
            else
            {
                echo "Erreur : lieu indisponible";
            }
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