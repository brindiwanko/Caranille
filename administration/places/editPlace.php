<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminPlaceId'])
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
        if (ctype_digit($_POST['adminPlaceId'])
        && $_POST['adminPlaceId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminPlaceId = htmlspecialchars($_POST['adminPlaceId']);

            //On fait une requête pour vérifier si le lieu choisit existe
            $placeQuery = $bdd->prepare("SELECT * FROM car_places 
            WHERE placeId = ?");
            $placeQuery->execute([$adminPlaceId]);
            $placeRow = $placeQuery->rowCount();

            //Si le lieu existe
            if ($placeRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($place = $placeQuery->fetch())
                {
                    //On récupère les informations du lieu
                    $adminplacePicture = $place['placePicture'];
                    $adminplaceName = $place['placeName'];
                    $adminplaceDescription = $place['placeDescription'];
                    $adminplacePriceInn = $place['placePriceInn'];
                    $adminplaceChapter = $place['placeChapter'];
                    $adminplaceAccess = $place['placeAccess'];
                }
                ?>

                <p><img src="<?php echo $adminplacePicture ?>" height="100" width="100"></p>

                <p>Informations du lieu</p>
                
                <form method="POST" action="editPlaceEnd.php">
                    Image : <input type="text" name="adminplacePicture" class="form-control" placeholder="Image" value="<?php echo $adminplacePicture ?>" required>
                    Nom : <input type="text" name="adminplaceName" class="form-control" placeholder="Nom" value="<?php echo $adminplaceName ?>" required>
                    Description : <br> <textarea class="form-control" name="adminplaceDescription" id="adminplaceDescription" rows="3"><?php echo $adminplaceDescription; ?></textarea>
                    Prix de l'auberge : <input type="number" name="adminplacePriceInn" class="form-control" placeholder="Prix de l'auberge" value="<?php echo $adminplacePriceInn ?>" required>
                    lieu disponible au chapitre : <input type="number" name="adminplaceChapter" class="form-control" placeholder="lieu disponible au chapitre" value="<?php echo $adminplaceChapter ?>" required>
                    Lieu accessible aux autres joueurs : <select name="adminplaceAccess" class="form-control">
        
                        <?php
                        switch ($adminplaceAccess)
                        {
                            case "Yes":
                                ?>
                                <option selected="selected" value="Yes">Oui</option>
                                <option value="No">Non</option>
                                <?php
                            break;
                    
                            case "No":
                                ?>
                                <option value="Yes">Oui</option>
                                <option selected="selected" value="No">Non</option>
                                <?php
                            break;
                        }
                        ?>
                    
                    </select>
                    <input type="hidden" name="adminPlaceId" value="<?php echo $adminPlaceId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
                </form>
                
                <hr>
                
                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si le lieu n'exite pas
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
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../html/footer.php");