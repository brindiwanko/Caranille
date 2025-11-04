<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminclasseId'])
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

        //On vérifie si l'id de la classe récupéré dans le formulaire est en entier positif
        if (ctype_digit($_POST['adminclasseId'])
        && $_POST['adminclasseId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminclasseId = htmlspecialchars($_POST['adminclasseId']);

            //On fait une requête pour vérifier si le compte choisit existe
            $classeQuery = $bdd->prepare("SELECT * FROM car_classes 
            WHERE classeId = ?");
            $classeQuery->execute([$adminclasseId]);
            $classeRow = $classeQuery->rowCount();

            //Si la classe existe
            if ($classeRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($classe = $classeQuery->fetch())
                {
                    //On récupère les informations de la classe
                    $adminclassePicture = $classe['classePicture'];
                    $adminclasseName = $classe['classeName'];
                    $adminclasseDescription = $classe['classeDescription'];
                    $adminclasseHpBonus = $classe['classeHpBonus'];
                    $adminclasseMpBonus = $classe['classeMpBonus'];
                    $adminclassestrengthBonus = $classe['classestrengthBonus'];
                    $adminclasseMagicBonus = $classe['classeMagicBonus'];
                    $adminclasseAgilityBonus = $classe['classeAgilityBonus'];
                    $adminclasseDefenseBonus = $classe['classeDefenseBonus'];
                    $adminclasseDefenseMagicBonus = $classe['classeDefenseMagicBonus'];
                    $adminclasseWisdomBonus = $classe['classeWisdomBonus'];
                    $adminclasseProspectingBonus = $classe['classeProspectingBonus'];
                }
                ?>

                <p><img src="<?php echo $adminclassePicture ?>" height="100" width="100"></p>

                <p>Informations de la classe</p>

                <form method="POST" action="editClasseEnd.php">
                    Image : <input type="text" name="adminclassePicture" class="form-control" placeholder="Nom" value="<?php echo $adminclassePicture ?>" required>
                    Nom : <input type="text" name="adminclasseName" class="form-control" placeholder="Nom" value="<?php echo $adminclasseName ?>" required>
                    Description : <br> <textarea class="form-control" name="adminclasseDescription" id="adminclasseDescription" rows="3" required><?php echo $adminclasseDescription; ?></textarea>
                    HP par niveau : <input type="number" name="adminclasseHpBonus" class="form-control" placeholder="HP par niveau" value="<?php echo $adminclasseHpBonus ?>" required>
                    MP par niveau : <input type="number" name="adminclasseMpBonus" class="form-control" placeholder="MP par niveau" value="<?php echo $adminclasseMpBonus ?>" required>
                    Force par niveau : <input type="number" name="adminclassestrengthBonus" class="form-control" placeholder="Force par niveau" value="<?php echo $adminclassestrengthBonus ?>" required>
                    Magie par niveau : <input type="number" name="adminclasseMagicBonus" class="form-control" placeholder="Magie par niveau" value="<?php echo $adminclasseMagicBonus ?>" required>
                    Agilité par niveau : <input type="number" name="adminclasseAgilityBonus" class="form-control" placeholder="Agilité par niveau" value="<?php echo $adminclasseAgilityBonus ?>" required>
                    Défense par niveau : <input type="number" name="adminclasseDefenseBonus" class="form-control" placeholder="Défense par niveau" value="<?php echo $adminclasseDefenseBonus ?>" required>
                    Défense Magique par niveau : <input type="number" name="adminclasseDefenseMagicBonus" class="form-control" placeholder="Défense Magique par niveau" value="<?php echo $adminclasseDefenseMagicBonus ?>" required>
                    Sagesse par niveau : <input type="number" name="adminclasseWisdomBonus" class="form-control" placeholder="Sagesse par niveau" value="<?php echo $adminclasseWisdomBonus ?>" required>
                    Prospection par niveau : <input type="number" name="adminclasseProspectingBonus" class="form-control" placeholder="Prospection par niveau" value="<?php echo $adminclasseProspectingBonus ?>" required>
                    <input type="hidden" name="adminclasseId" value="<?php echo $adminclasseId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input name="finalEdit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
                </form>
                
                <hr>

                <form method="POST" action="index.php">
                    <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                </form>
                
                <?php
            }
            //Si la classe n'existe pas
            else
            {
                echo "Erreur : Cette classe n'existe pas";
            }
            $classeQuery->closeCursor();
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