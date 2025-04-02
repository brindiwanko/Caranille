<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['generate']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On récupère les informations du formulaire précédent
        $adminQuantityMonsterGenerate = htmlspecialchars(addslashes($_POST['adminQuantityMonsterGenerate']));

        //Si il y a plus d'un monstre
        if ($adminQuantityMonsterGenerate > 1)
        {
            $generateMonsterSQL = "INSERT INTO car_monsters VALUES";

            for ($i = 0; $i < $adminQuantityMonsterGenerate -1; $i++)
            {
                $generateMonsterSQL = $generateMonsterSQL . "(null, '1', '../../img/empty.png', 'Empty', 'Empty', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'No', 0, 0, 0, 0, 0, 0),";
            }
            $generateMonsterSQL = $generateMonsterSQL . "(null, '1', '../../img/empty.png', 'Empty', 'Empty', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'No', 0, 0, 0, 0, 0, 0);";
        }
        //Si il n'y a qu'un monstre
        else
        {
            $generateMonsterSQL = "INSERT INTO car_monsters VALUES";
            $generateMonsterSQL = $generateMonsterSQL . "(null, '1', '../../img/empty.png', 'Empty', 'Empty', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'No', 0, 0, 0, 0, 0, 0);";
        }

        $bdd->query($generateMonsterSQL);
        ?>

        <br />Vous venez de générer <?php echo $adminQuantityMonsterGenerate ?> monstre(s) vierge.

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