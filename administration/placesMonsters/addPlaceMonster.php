<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminplaceMonsterPlaceId'])
&& isset($_POST['adminplaceMonsterMonsterId'])
&& isset($_POST['token'])
&& isset($_POST['add']))
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
        && ctype_digit($_POST['adminplaceMonsterMonsterId'])
        && $_POST['adminplaceMonsterPlaceId'] >= 1
        && $_POST['adminplaceMonsterMonsterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminplaceMonsterPlaceId = htmlspecialchars($_POST['adminplaceMonsterPlaceId']);
            $adminplaceMonsterMonsterId = htmlspecialchars($_POST['adminplaceMonsterMonsterId']);

            //On fait une requête pour vérifier si le lieu choisie existe
            $placeQuery = $bdd->prepare("SELECT * FROM car_places 
            WHERE placeId = ?");
            $placeQuery->execute([$adminplaceMonsterPlaceId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe
            if ($placeRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($place = $placeQuery->fetch())
                {
                    //On récupère les informations du lieu
                    $adminPlaceMonsterplacePicture = $place['placePicture'];
                    $adminPlaceMonsterplaceName = $place['placeName'];
                }
                $placeQuery->closeCursor();

                //On fait une requête pour vérifier si le monstre choisit existe
                $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters 
                WHERE monsterId = ?");
                $monsterQuery->execute([$adminplaceMonsterMonsterId]);
                $monsterRow = $monsterQuery->rowCount();

                //Si le monstre existe
                if ($monsterRow == 1) 
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($monster = $monsterQuery->fetch())
                    {
                        //On récupère les informations du monstre
                        $adminPlaceMonsterMonsterPicture = $monster['monsterPicture'];
                        $adminPlaceMonsterMonsterName = $monster['monsterName'];
                    }
                    $monsterQuery->closeCursor();

                    //On fait une requête pour vérifier si le monstre n'est pas déjà dans cette lieu
                    $placeMonsterQuery = $bdd->prepare("SELECT * FROM car_places_monsters 
                    WHERE placeMonsterPlaceId = ?
                    AND placeMonsterMonsterId = ?");
                    $placeMonsterQuery->execute([$adminplaceMonsterPlaceId, $adminplaceMonsterMonsterId]);
                    $placeMonsterRow = $placeMonsterQuery->rowCount();

                    //Si le monstre n'est pas dans le lieu
                    if ($placeMonsterRow == 0) 
                    {
                        ?>
                
                        <p>ATTENTION</p> 

                        Vous êtes sur le point d'ajouter le monstre <em><?php echo $adminPlaceMonsterMonsterName ?></em> dans le lieu <em><?php echo $adminPlaceMonsterplaceName ?></em>.<br />
                        Confirmez-vous l'ajout ?

                        <hr>
                            
                        <form method="POST" action="addPlaceMonsterEnd.php">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminplaceMonsterPlaceId" value="<?php echo $adminplaceMonsterPlaceId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminplaceMonsterMonsterId" value="<?php echo $adminplaceMonsterMonsterId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="finalAdd" value="Je confirme">
                        </form>
                        
                        <hr>

                        <form method="POST" action="index.php">
                            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                        </form>
                        
                        <?php
                    }
                    //Si le monstre est déjà dans cette lieu
                    else
                    {
                        ?>
                        
                        Erreur : Ce monstre est déjà dans cette lieu
                        
                        <form method="POST" action="managePlaceMonster.php">
                            <input type="hidden" name="adminplaceMonsterPlaceId" value="<?php echo $adminplaceMonsterPlaceId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="manage" value="Retour">
                        </form>
                        
                        <?php
                    }
                    $monsterQuery->closeCursor();
                }
                //Si le monstre existe pas
                else
                {
                    echo "Erreur : Ce monstre n'existe pas";
                }
                $monsterQuery->closeCursor();
            }
            //Si le lieu existe pas
            else
            {
                echo "Erreur : Cette lieu n'existe pas";
            }
            $placeQuery->closeCursor();
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
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php");