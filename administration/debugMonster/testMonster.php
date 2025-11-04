<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminMonsterId'])
&& isset($_POST['token'])
&& isset($_POST['test']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminMonsterId'])
        && $_POST['adminMonsterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminMonsterId = htmlspecialchars($_POST['adminMonsterId']);

            //On fait une requête pour vérifier si le monstre choisit existe
            $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters 
            WHERE monsterId = ?");
            $monsterQuery->execute([$adminMonsterId]);
            $monsterRow = $monsterQuery->rowCount();

            //Si le monstre existe
            if ($monsterRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($monster = $monsterQuery->fetch())
                {
                    //On récupère les informations du formulaire
                    $adminMonsterName = $monster['monsterName'];
                    $adminMonsterHp = $monster['monsterHp'];
                    $adminMonsterMp = $monster['monsterMp'];
                }
                
                //On vérifie si on a pas déjà un combat en cours
                if ($battleRow == 1)
                {
                    ?>
                    
                    Erreur : vous avez déjà un combat en cours
                    
                    <hr>
                        
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="retour">
                    </form>
                    
                    <?php
                }
                else
                {
                    ?>
                    
                    <p>ATTENTION</p> 

                    Vous êtes sur le point de lancer un combat contre le monstre <em><?php echo $adminMonsterName ?></em>.<br />
                    Confirmez-vous ?
            
                    <hr>
                        
                    <form method="POST" action="testMonsterEnd.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="adminMonsterId" value="<?php echo $adminMonsterId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="finalTest" value="Je confirme">
                    </form>
                    
                    <hr>
            
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
            }
            //Si le monstre n'exite pas
            else
            {
                echo "Erreur : Ce monstre n'existe pas";
            }
            $monsterQuery->closeCursor();
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