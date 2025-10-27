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
&& isset($_POST['finalTest']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

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
                    //Insertion du combat dans la base de donnée avec les données
                    $addBattle = $bdd->prepare("INSERT INTO car_battles VALUES(
                    NULL,
                    :characterId,
                    :adminMonsterId,
                    'Dungeon',
                    :adminMonsterHp,
                    :adminMonsterMp)");
                    $addBattle->execute([
                    'characterId' => $characterId,
                    'adminMonsterId' => $adminMonsterId,
                    'adminMonsterHp' => $adminMonsterHp,
                    'adminMonsterMp' => $adminMonsterMp]);
                    $addBattle->closeCursor();
                    ?>
                    
                    Le combat est sur le point de commencer avec <?php echo $adminMonsterName ?>.

                    <hr>
                        
                    <form method="POST" action="../../modules/battle/index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="continue" value="continuer">
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