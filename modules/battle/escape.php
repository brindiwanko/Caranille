<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il n'y a actuellement pas de combat on redirige le joueur vers l'accueil
if ($battleRow == 0) { exit(header("Location: ../../modules/main/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token'])
&& isset($_POST['escape']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Si il s'agit d'un combat d'arène on ajoute 1 point de défaite lié à la fuite
        if ($battleType == "Arena")
        {
            //On ajoute un point de défaite au joueur
            $updateCharacter = $bdd->prepare("UPDATE car_characters
            SET characterArenaDefeate = characterArenaDefeate + 1
            WHERE characterId = :characterId");
            $updateCharacter->execute([
            'characterId' => $characterId]);
            $updateCharacter->closeCursor();
        }
        //Si il ne s'agit pas d'un combat d'arène on met à jours les stats du combat
        else
        {
            //On met à jour les stats du monstre
            $updateMonsterStats = $bdd->prepare("UPDATE car_monsters 
            SET monsterQuantityEscaped = monsterQuantityEscaped + 1
            WHERE monsterId = :opponentId");
            $updateMonsterStats->execute(['opponentId' => $opponentId]);
            $updateMonsterStats->closeCursor();  

            //On définit une date
            $date = date('Y-m-d H:i:s');

            //Insertion des stats du combat dans la base de donnée avec les données
            $addBattleStats = $bdd->prepare("INSERT INTO car_monsters_battles_stats VALUES(
            NULL,
            :monsterBattleStatsMonsterId,
            :monsterBattleStatsCharacterId,
            'EscapeBattle',
            :monsterBattleStatsDateTime)");
            $addBattleStats->execute([
            'monsterBattleStatsMonsterId' => $opponentId,
            'monsterBattleStatsCharacterId' => $characterId,
            'monsterBattleStatsDateTime' => $date]);
            $addBattleStats->closeCursor();
        }
        
        //On détruit le combat en cours
        $deleteBattle = $bdd->prepare("DELETE FROM car_battles 
        WHERE battleId = :battleId");
        $deleteBattle->execute(array('battleId' => $battleId));
        $deleteBattle->closeCursor();
        ?>
        
        Vous avez fuit le combat !
            
        <hr>
    
        <form method="POST" action="../../modules/place/index.php">
            <input type="submit" class="btn btn-secondary btn-lg" name="escape" value="Continuer"><br />
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>