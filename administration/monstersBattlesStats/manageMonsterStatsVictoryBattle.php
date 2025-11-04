<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['adminMonsterStatsMonsterId'])
&& isset($_POST['token'])
&& isset($_POST['viewStats']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['adminMonsterStatsMonsterId'])
        && $_POST['adminMonsterStatsMonsterId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $adminMonsterStatsMonsterId = htmlspecialchars($_POST['adminMonsterStatsMonsterId']);

            //On fait une requête pour vérifier si le monstre choisit existe
            $monsterQuery = $bdd->prepare("SELECT * FROM car_monsters 
            WHERE monsterId = ?");
            $monsterQuery->execute([$adminMonsterStatsMonsterId]);
            $monsterRow = $monsterQuery->rowCount();

            //Si le monstre existe
            if ($monsterRow == 1) 
            {
                ?>
                
                <h2>Statistiques complète du monstre</h2>

                <hr>

                <p>Victoire de combat :</p>

                <?php
                //On fait une requête pour récupérer les stats
                $monsterBattlesStatsQuery = $bdd->prepare("SELECT * FROM car_monsters_battles_stats , car_characters
                WHERE monsterBattleStatsMonsterId = ?
                AND monsterBattleStatsCharacterId = characterId
                AND monsterBattleStatsType = 'VictoryBattle'
                ORDER BY monsterBattleStatsId desc");
                $monsterBattlesStatsQuery->execute([$adminMonsterStatsMonsterId]);
                $monsterBattlesStatsRow = $monsterBattlesStatsQuery->rowCount();

                if ($monsterBattlesStatsRow >= 1)
                {
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($monsterBattlesStats = $monsterBattlesStatsQuery->fetch())
                    {
                        //On récupère les informations du monstre
                        $adminMonsterBattleStatsCharacterName = $monsterBattlesStats['characterName'];
                        $adminMonsterBattleStatsDateTime = $monsterBattlesStats['monsterBattleStatsDateTime'];
                        
                        $dateFr = (new DateTime($monsterBattlesStats['monsterBattleStatsDateTime']))->format('d-m-Y H:i');
                        echo "($dateFr) - $adminMonsterBattleStatsCharacterName<br />";
                    }
                    $monsterBattlesStatsQuery->closeCursor();
                }
                else
                {
                    echo "Aucune victoire de combat<br />";
                }
                ?>

                <hr>
                
                <form method="POST" action="manageMonsterStatsLight.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="adminMonsterStatsMonsterId" value="<?php echo $adminMonsterStatsMonsterId ?>">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" class="btn btn-secondary btn-lg" name="viewStats" value="Retour">
                </form>

                <?php
            }
            //Si le monstre n'exite pas
            else
            {
            echo "Erreur : Ce monstre n'existe pas";
            }
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