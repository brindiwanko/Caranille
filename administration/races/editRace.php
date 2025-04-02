<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminRaceId'])
&& isset($_POST['token'])
&& isset($_POST['edit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = uniqid();

        //On vérifie si l'id de la race récupéré dans le formulaire est en entier positif
        if (ctype_digit($_POST['adminRaceId'])
        && $_POST['adminRaceId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminRaceId = htmlspecialchars(addslashes($_POST['adminRaceId']));

            //On fait une requête pour vérifier si le compte choisit existe
            $raceQuery = $bdd->prepare("SELECT * FROM car_races 
            WHERE raceId = ?");
            $raceQuery->execute([$adminRaceId]);
            $raceRow = $raceQuery->rowCount();

            //Si la classe existe
            if ($raceRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($race = $raceQuery->fetch())
                {
                    //On récupère les informations de la classe
                    $adminRacePicture = stripslashes($race['racePicture']);
                    $adminRaceName = stripslashes($race['raceName']);
                    $adminRaceDescription = stripslashes($race['raceDescription']);
                    $adminRaceHpBonus = stripslashes($race['raceHpBonus']);
                    $adminRaceMpBonus = stripslashes($race['raceMpBonus']);
                    $adminRaceStrengthBonus = stripslashes($race['raceStrengthBonus']);
                    $adminRaceMagicBonus = stripslashes($race['raceMagicBonus']);
                    $adminRaceAgilityBonus = stripslashes($race['raceAgilityBonus']);
                    $adminRaceDefenseBonus = stripslashes($race['raceDefenseBonus']);
                    $adminRaceDefenseMagicBonus = stripslashes($race['raceDefenseMagicBonus']);
                    $adminRaceWisdomBonus = stripslashes($race['raceWisdomBonus']);
                    $adminRaceProspectingBonus = stripslashes($race['raceProspectingBonus']);
                }
                ?>

                <p><img src="<?php echo $adminRacePicture ?>" height="100" width="100"></p>

                <p>Informations de la classe</p>

                <form method="POST" action="editRaceEnd.php">
                    Image : <input type="text" name="adminRacePicture" class="form-control" placeholder="Nom" value="<?php echo $adminRacePicture ?>" required>
                    Nom : <input type="text" name="adminRaceName" class="form-control" placeholder="Nom" value="<?php echo $adminRaceName ?>" required>
                    Description : <br> <textarea class="form-control" name="adminRaceDescription" id="adminRaceDescription" rows="3" required><?php echo $adminRaceDescription; ?></textarea>
                    HP par niveau : <input type="number" name="adminRaceHpBonus" class="form-control" placeholder="HP par niveau" value="<?php echo $adminRaceHpBonus ?>" required>
                    MP par niveau : <input type="number" name="adminRaceMpBonus" class="form-control" placeholder="MP par niveau" value="<?php echo $adminRaceMpBonus ?>" required>
                    Force par niveau : <input type="number" name="adminRaceStrengthBonus" class="form-control" placeholder="Force par niveau" value="<?php echo $adminRaceStrengthBonus ?>" required>
                    Magie par niveau : <input type="number" name="adminRaceMagicBonus" class="form-control" placeholder="Magie par niveau" value="<?php echo $adminRaceMagicBonus ?>" required>
                    Agilité par niveau : <input type="number" name="adminRaceAgilityBonus" class="form-control" placeholder="Agilité par niveau" value="<?php echo $adminRaceAgilityBonus ?>" required>
                    Défense par niveau : <input type="number" name="adminRaceDefenseBonus" class="form-control" placeholder="Défense par niveau" value="<?php echo $adminRaceDefenseBonus ?>" required>
                    Défense Magique par niveau : <input type="number" name="adminRaceDefenseMagicBonus" class="form-control" placeholder="Défense Magique par niveau" value="<?php echo $adminRaceDefenseMagicBonus ?>" required>
                    Sagesse par niveau : <input type="number" name="adminRaceWisdomBonus" class="form-control" placeholder="Sagesse par niveau" value="<?php echo $adminRaceWisdomBonus ?>" required>
                    Prospection par niveau : <input type="number" name="adminRaceProspectingBonus" class="form-control" placeholder="Prospection par niveau" value="<?php echo $adminRaceProspectingBonus ?>" required>
                    <input type="hidden" name="adminRaceId" value="<?php echo $adminRaceId ?>">
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
            $raceQuery->closeCursor();
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