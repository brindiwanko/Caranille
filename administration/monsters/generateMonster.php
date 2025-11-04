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

        //On récupère les informations du formulaire précédent et on valide que c'est un entier
        $adminQuantityMonsterGenerate = $_POST['adminQuantityMonsterGenerate'];

        //Validation stricte : doit être un nombre entier positif
        if (!ctype_digit($adminQuantityMonsterGenerate) || $adminQuantityMonsterGenerate < 1 || $adminQuantityMonsterGenerate > 1000)
        {
            echo "Erreur : La quantité doit être un nombre entre 1 et 1000";
            exit();
        }

        $adminQuantityMonsterGenerate = (int)$adminQuantityMonsterGenerate;

        //Utilisation d'une requête préparée pour insérer les monstres de manière sécurisée
        $insertMonsterStmt = $bdd->prepare("INSERT INTO car_monsters VALUES (null, '1', '../../img/empty.png', 'Empty', 'Empty', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'No', 0, 0, 0, 0, 0, 0)");

        //On insère le nombre de monstres demandé
        for ($i = 0; $i < $adminQuantityMonsterGenerate; $i++)
        {
            $insertMonsterStmt->execute();
        }
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