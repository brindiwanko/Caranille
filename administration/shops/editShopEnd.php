<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminShopId'])
&& isset($_POST['adminShopPicture'])
&& isset($_POST['adminShopName'])
&& isset($_POST['adminShopDescription'])
&& isset($_POST['token'])
&& isset($_POST['finalEdit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminShopId'])
        && $_POST['adminShopId'] >= 1)
        {
            //On récupère les informations du formulaire
            $adminShopId = htmlspecialchars($_POST['adminShopId']);
            $adminShopPicture = htmlspecialchars($_POST['adminShopPicture']);
            $adminShopName = htmlspecialchars($_POST['adminShopName']);
            $adminShopDescription = htmlspecialchars($_POST['adminShopDescription']);

            //On fait une requête pour vérifier si le magasin choisit existe
            $shopQuery = $bdd->prepare("SELECT * FROM car_shops 
            WHERE shopId = ?");
            $shopQuery->execute([$adminShopId]);
            $shopRow = $shopQuery->rowCount();

            //Si le magasin existe
            if ($shopRow == 1) 
            {
                //On met à jour l'objet dans la base de donnée
                $updateShop = $bdd->prepare("UPDATE car_shops 
                SET shopPicture = :adminShopPicture,
                shopName = :adminShopName,
                shopDescription = :adminShopDescription
                WHERE shopId = :adminShopId");
                $updateShop->execute([
                'adminShopPicture' => $adminShopPicture,
                'adminShopName' => $adminShopName,
                'adminShopDescription' => $adminShopDescription,
                'adminShopId' => $adminShopId]);
                $updateShop->closeCursor();
                ?>

                Le magasin a bien été mit à jour

                <hr>
                    
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le magasin n'exite pas
            else
            {
                echo "Erreur : Ce magasin n'existe pas";
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
    echo "Erreur : Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php");