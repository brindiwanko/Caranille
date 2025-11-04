<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminShopId'])
&& isset($_POST['token'])
&& isset($_POST['edit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminShopId'])
        && $_POST['adminShopId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminShopId = htmlspecialchars($_POST['adminShopId']);

            //On fait une requête pour vérifier si le magasin choisit existe
            $shopQuery = $bdd->prepare("SELECT * FROM car_shops 
            WHERE shopId = ?");
            $shopQuery->execute([$adminShopId]);
            $shopRow = $shopQuery->rowCount();

            //Si le magasin existe
            if ($shopRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($shop = $shopQuery->fetch())
                {
                    //On récupère les informations du magasin
                    $adminShopPicture = $shop['shopPicture'];
                    $adminShopName = $shop['shopName'];
                    $adminShopDescription = $shop['shopDescription'];
                }
                ?>

                <p><img src="<?php echo $adminShopPicture ?>" height="100" width="100"></p>

                <p>Informations du magasin</p>

                <form method="POST" action="editShopEnd.php">
                    Image : <input type="text" name="adminShopPicture" class="form-control" placeholder="Image" value= "<?php echo $adminShopPicture ?>" required>
                    Nom : <input type="text" name="adminShopName" class="form-control" placeholder="Nom" value= "<?php echo $adminShopName ?>" required>
                    Description : <br> <textarea class="form-control" name="adminShopDescription" id="adminShopDescription" rows="3" required><?php echo $adminShopDescription; ?></textarea>
                    <input type="hidden" name="adminShopId" value="<?php echo $adminShopId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");