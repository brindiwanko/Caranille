<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['add']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        ?>
    
        <p>Informations de la classe</p>
        
        <form method="POST" action="addClasseEnd.php">
            Image : <input type="text" name="adminclassePicture" class="form-control" placeholder="Nom" value="../../img/empty.png" required autofocus>
            Nom : <input type="text" name="adminclasseName" class="form-control" placeholder="Nom" required>
            Description : <br> <textarea class="form-control" name="adminclasseDescription" id="adminclasseDescription" rows="3" required></textarea>
            HP par niveau : <input type="number" name="adminclasseHpBonus" class="form-control" placeholder="HP par niveau" required>
            MP par niveau : <input type="number" name="adminclasseMpBonus" class="form-control" placeholder="MP par niveau" required>
            Force par niveau : <input type="number" name="adminclassestrengthBonus" class="form-control" placeholder="Force par niveau" required>
            Magie par niveau : <input type="number" name="adminclasseMagicBonus" class="form-control" placeholder="Magie par niveau" required>
            Agilité par niveau : <input type="number" name="adminclasseAgilityBonus" class="form-control" placeholder="Agilité par niveau" required>
            Défense par niveau : <input type="number" name="adminclasseDefenseBonus" class="form-control" placeholder="Défense par niveau" required>
            Défense Magique par niveau : <input type="number" name="adminclasseDefenseMagicBonus" class="form-control" placeholder="Défense Magique par niveau" required>
            Sagesse par niveau : <input type="number" name="adminclasseWisdomBonus" class="form-control" placeholder="Sagesse par niveau" required>
            Prospection par niveau : <input type="number" name="adminclasseProspectingBonus" class="form-control" placeholder="Prospection par niveau" required>
            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
            <input name="finalAdd" class="btn btn-secondary btn-lg" type="submit" value="Ajouter">
        </form>
        
        <hr>

        <form method="POST" action="index.php">
            <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
        </form>
        
        <?php
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