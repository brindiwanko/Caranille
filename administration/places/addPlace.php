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
        
        <p>Informations du lieu</p>

        <form method="POST" action="addPlaceEnd.php">
            Image : <input type="text" name="adminplacePicture" class="form-control" placeholder="Image" value="../../img/empty.png" required>
            Nom : <input type="text" name="adminplaceName" class="form-control" placeholder="Nom" required>
            Description : <br> <textarea class="form-control" name="adminplaceDescription" id="adminplaceDescription" rows="3"></textarea>
            Prix de l'auberge : <input type="number" name="adminplacePriceInn" class="form-control" placeholder="Prix de l'auberge" value="0" required>
            lieu disponible au chapitre : <input type="number" name="adminplaceChapter" class="form-control" placeholder="lieu disponible au chapitre" value="1" required>
            Lieu accessible aux autres joueurs : <select name="adminplaceAccess" class="form-control">
                <option value="Yes">Oui</option>
                <option value="No">Non</option>
            </select>
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