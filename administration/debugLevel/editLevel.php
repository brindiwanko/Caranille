<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminCharacterLevel'])
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
        if (ctype_digit($_POST['adminCharacterLevel']) 
        && $_POST['adminCharacterLevel'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminCharacterLevel = htmlspecialchars($_POST['adminCharacterLevel']);
            ?>
            
            <p>ATTENTION</p> 

            Vous êtes sur le point de modifier votre niveau par <em><?php echo $adminCharacterLevel ?></em>.<br />
            Confirmez-vous ?

            <hr>
                
            <form method="POST" action="editLevelEnd.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="adminCharacterLevel" value="<?php echo $adminCharacterLevel ?>">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" class="btn btn-secondary btn-lg" name="finalEdit" value="Je confirme">
            </form>
            
            <hr>

            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
        
        <?php
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