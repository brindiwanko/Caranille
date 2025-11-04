<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminplaceShopPlaceId'])
&& isset($_POST['adminPlaceShopShopId'])
&& isset($_POST['token'])
&& isset($_POST['delete']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminplaceShopPlaceId'])
        && ctype_digit($_POST['adminPlaceShopShopId'])
        && $_POST['adminplaceShopPlaceId'] >= 1
        && $_POST['adminPlaceShopShopId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminplaceShopPlaceId = htmlspecialchars($_POST['adminplaceShopPlaceId']);
            $adminPlaceShopShopId = htmlspecialchars($_POST['adminPlaceShopShopId']);

            //On fait une requête pour vérifier si le lieu choisie existe
            $placeQuery = $bdd->prepare("SELECT * FROM car_places 
            WHERE placeId = ?");
            $placeQuery->execute([$adminplaceShopPlaceId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe
            if ($placeRow == 1) 
            {
                while ($place = $placeQuery->fetch())
                {
                    //On récupère les informations du magasin
                    $adminPlaceShopplaceName = $place['placeName'];
                }
        
                //On fait une requête pour vérifier si le magasin choisit existe
                $shopQuery = $bdd->prepare("SELECT * FROM car_shops 
                WHERE shopId = ?");
                $shopQuery->execute([$adminPlaceShopShopId]);
                $shopRow = $shopQuery->rowCount();

                //Si le magasin existe
                if ($shopRow == 1) 
                {
                    while ($shop = $shopQuery->fetch())
                    {
                        //On récupère les informations du magasin
                        $adminPlaceShopShopName = $shop['shopName'];
                    }

                    //On fait une requête pour vérifier si le magasin n'est pas déjà dans cette lieu
                    $placeShopQuery = $bdd->prepare("SELECT * FROM car_places_shops 
                    WHERE placeShopPlaceId = ?
                    AND placeShopShopId = ?");
                    $placeShopQuery->execute([$adminplaceShopPlaceId, $adminPlaceShopShopId]);
                    $placeShopRow = $placeShopQuery->rowCount();

                    //Si le magasin n'est pas dans le lieu
                    if ($placeShopRow == 1) 
                    {
                        ?>
                        
                        <p>ATTENTION</p>
                        
                        Vous êtes sur le point de retirer le magasin <em><?php echo $adminPlaceShopShopName ?></em> du lieu <em><?php echo $adminPlaceShopplaceName ?></em>.<br />
                        Confirmez-vous ?

                        <hr>
                            
                        <form method="POST" action="deletePlaceShopEnd.php">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminplaceShopPlaceId" value="<?php echo $adminplaceShopPlaceId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="adminPlaceShopShopId" value="<?php echo $adminPlaceShopShopId ?>">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" class="btn btn-secondary btn-lg" name="finalDelete" value="Je confirme">
                        </form>
                
                        <hr>

                        <form method="POST" action="index.php">
                            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                        </form>
                        
                        <?php
                    }
                    //Si le magasin n'exite pas
                    else
                    {
                        echo "Erreur : Ce magasin n'est pas dans cette lieu";
                    }
                    $placeShopQuery->closeCursor();
                }
                //Si le magasin existe pas
                else
                {
                    echo "Erreur : Ce magasin n'existe pas";
                }
                $placeShopQuery->closeCursor();
            }
            //Si le lieu existe pas
            else
            {
                echo "Erreur : Cette lieu n'existe pas";
            }
            $shopQuery->closeCursor();
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