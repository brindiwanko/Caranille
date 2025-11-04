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
&& isset($_POST['manage']))
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
                    $adminclasseName = $classe['classeName'];
                }
                ?>
                
                Que souhaitez-vous faire de la classe <em><?php echo $adminclasseName ?></em> ?

                <hr>
                    
                <form method="POST" action="editClasse.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="adminclasseId" value="<?php echo $adminclasseId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="edit" value="Afficher/Modifier la classe">
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