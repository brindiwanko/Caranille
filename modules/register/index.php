<?php
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");
?>

<form method="POST" action="completeRegistration.php">
    Pseudo : <input type="text" class="form-control" name="accountPseudo" required>
    Mot de passe : <input type="password" class="form-control" name="accountPassword" required>
    Confirmez : <input type="password" class="form-control" name="accountPasswordConfirm" required>
    Email : <input type="email" class="form-control" name="accountEmail" required>
    Confirmez l'email : <input type="email" class="form-control" name="accountEmailConfirm" required>
    Classe <select class="form-control" name="characterRaceId">

        <?php
        //On rempli le menu déroulant avec la liste des classes disponible
        $raceListQuery = $bdd->query("SELECT * FROM car_races");

        $raceList = $raceListQuery->rowCount();
        
        //S'il y a au moins une classe de disponible on les affiches dans le menu déroulant
        if ($raceList >= 1)
        {
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($raceList = $raceListQuery->fetch())
            {
                //on récupère les valeurs de chaque classes qu'on va ensuite mettre dans le menu déroulant
                $raceId = stripslashes($raceList['raceId']); 
                $raceName = stripslashes($raceList['raceName']);
                ?>
                <option value="<?php echo $raceId ?>"><?php echo $raceName ?></option>
                <?php
            }
        }
        $raceListQuery->closeCursor();
        ?>
    
    </select>
    Sexe : <select class="form-control" name="characterSex">
        <option value="1">Homme</option>
        <option value="0">Femme</option>
    </select>
    Nom du personnage : <input type="text" class="form-control" name="characterName" required><br />
    <iframe src="../../CGU.txt" width="100%" height="100%"></iframe>
    En vous inscrivant vous acceptez le présent règlement !<br />
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="register" value="Je créer mon compte">
</form>

<?php require_once("../../html/footer.php"); ?>