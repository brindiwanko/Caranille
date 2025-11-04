<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['addMp']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;
        
        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //Si le personnage a suffisamment de PC
        if ($characterSkillPoints > 0)
        {
            ?>
            
            <p>ATTENTION</p> 
            Vous êtes sur le point d'attribuer 1 point en MP à votre personnage pour 1 PC.<br />
            Confirmez-vous ?

            <hr>
                
            <form method="POST" action="addMpEnd.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" class="btn btn-secondary btn-lg" name="finalAddMp" value="Je confirme">
            </form>
            
            <hr>
                
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
            </form>
            
            <?php
        }
        //Si le personnage n'a pas suffisamment de PC
        else
        {
            ?>
            
            Vous n'avez pas assez de points de compétences
                    
            <hr>
            
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" value="Retour">
            </form>
            
            <?php
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>