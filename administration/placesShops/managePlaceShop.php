<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminplaceShopPlaceId'])
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
        if (ctype_digit($_POST['adminplaceShopPlaceId'])
        && $_POST['adminplaceShopPlaceId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminplaceShopPlaceId = htmlspecialchars($_POST['adminplaceShopPlaceId']);

            //On fait une requête pour vérifier si le lieu choisit existe
            $placeQuery = $bdd->prepare("SELECT * FROM car_places 
            WHERE placeId = ?");
            $placeQuery->execute([$adminplaceShopPlaceId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe
            if ($placeRow == 1)
            {
                //On fait une requête pour afficher la liste des magasins de cette lieu
                $placeShopQuery = $bdd->prepare("SELECT * FROM car_shops, car_places, car_places_shops
                WHERE placeShopShopId = shopId
                AND placeShopPlaceId = placeId
                AND placeId = ?
                ORDER BY shopName");
                $placeShopQuery->execute([$adminplaceShopPlaceId]);
                $placeShopRow = $placeShopQuery->rowCount();

                //S'il existe un ou plusieurs magasins dans le lieu on affiche le menu déroulant
                if ($placeShopRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="deletePlaceShop.php">
                        Magasins présent dans le lieu : <select name="adminPlaceShopShopId" class="form-control">
                                
                            <?php
                            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                            while ($placeShop = $placeShopQuery->fetch())
                            {
                                //On récupère les informations du magasin
                                $adminPlaceShopShopId = $placeShop['shopId'];
                                $adminPlaceShopShopName = $placeShop['shopName'];
                                ?>
                                <option value="<?php echo $adminPlaceShopShopId ?>"><?php echo "$adminPlaceShopShopName"; ?></option>
                                <?php
                            }
                            $placeShopQuery->closeCursor();
                            ?>
                            
                        </select>
                        <input type="hidden" name="adminplaceShopPlaceId" value="<?php echo $adminplaceShopPlaceId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="delete" class="btn btn-secondary btn-lg" value="Retirer le magasin">
                    </form>
                    
                    <hr>

                    <?php
                }
                $placeShopQuery->closeCursor();

                //On fait une requête pour afficher la liste des magasins du jeu qui ne sont pas dans le lieu
                $shopQuery = $bdd->prepare("SELECT * FROM car_shops
                WHERE (SELECT COUNT(*) FROM car_places_shops
                WHERE placeShopPlaceId = ?
                AND placeShopShopId = shopId) = 0
                ORDER BY shopName");
                $shopQuery->execute([$adminplaceShopPlaceId]);
                $shopRow = $shopQuery->rowCount();
                //S'il existe un ou plusieurs magasin on affiche le menu déroulant pour proposer au joueur d'en ajouter
                if ($shopRow > 0) 
                {
                    ?>
                    
                    <form method="POST" action="addPlaceShop.php">
                        Magasins disponible : <select name="adminPlaceShopShopId" class="form-control">
                                
                                <?php
                                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                                while ($shop = $shopQuery->fetch())
                                {
                                    //On récupère les informations du magasin
                                    $adminPlaceShopShopId = $shop['shopId'];
                                    $adminPlaceShopShopName = $shop['shopName'];
                                    ?>
                                    <option value="<?php echo $adminPlaceShopShopId ?>"><?php echo "$adminPlaceShopShopName"; ?></option>
                                    <?php
                                }
                                ?>
                                
                            </select>
                        
                        <input type="hidden" name="adminplaceShopPlaceId" value="<?php echo $adminplaceShopPlaceId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="add" class="btn btn-secondary btn-lg" value="Ajouter le magasin">
                    </form>
                    
                    <?php
                }
                else
                {
                    echo "Il n'y a actuellement aucun magasin";
                }
                $shopQuery->closeCursor();
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