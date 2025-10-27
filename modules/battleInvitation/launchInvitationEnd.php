<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

//Si les variables $_POST suivantes existent
if (isset($_POST['battleInvitationCharacterId'])
&& isset($_POST['launch']))
{
    //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
    if (ctype_digit($_POST['battleInvitationCharacterId'])
    && $_POST['battleInvitationCharacterId'] >= 1)
    {
        //On récupère l'id du formulaire précédent
        $battleInvitationCharacterId = htmlspecialchars($_POST['battleInvitationCharacterId']);

        //On fait une requête pour vérifier si l'invitation de combat choisit existe
        $battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations, car_battles_invitations_characters, car_monsters
		WHERE battleInvitationId = battleInvitationCharacterBattleInvitationId
		AND battleInvitationMonsterId = monsterId
		AND battleInvitationCharacterId = ?
		AND battleInvitationCharacterCharacterId = ?");
        $battleInvitationQuery->execute([$battleInvitationCharacterId, $characterId]);
        $battleInvitationRow = $battleInvitationQuery->rowCount();

        //Si l'invitation de combat existe
        if ($battleInvitationRow == 1) 
        {
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($battleInvitation = $battleInvitationQuery->fetch())
            {
                //On récupère les informations de l'invitation de combat
            	$battleInvitationId = $battleInvitation['battleInvitationId'];
                $battleInvitationName = $battleInvitation['battleInvitationName'];
                $battleInvitationMonsterId = $battleInvitation['monsterId'];
                $battleInvitationMonsterName = $battleInvitation['monsterName'];
            }
    
            //On fait une requête pour vérifier si l'adversaire est bien disponible dans le lieu du joueur
            $opponentQuery = $bdd->prepare("SELECT * FROM car_monsters
            WHERE monsterId = ?");
            $opponentQuery->execute([$battleInvitationMonsterId]);
            $opponentRow = $opponentQuery->rowCount();
    
            //Si le monstre existe
            if ($opponentRow == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($opponent = $opponentQuery->fetch())
                {
                    //On récupère les informations de l'adversaire
                    $opponentHp = $opponent['monsterHp'];
                    $opponentMp = $opponent['monsterMp'];
                    $monsterLimited = $opponent['monsterLimited'];
                    $monsterQuantity = $opponent['monsterQuantity'];
                }
                $opponentQuery->closeCursor();

                //Insertion du combat dans la base de donnée avec les données
                $addBattle = $bdd->prepare("INSERT INTO car_battles VALUES(
                NULL,
                :characterId,
                :battleInvitationMonsterId,
                'battleInvitation',
                :opponentHp,
                :opponentMp)");
                $addBattle->execute([
                'characterId' => $characterId,
                'battleInvitationMonsterId' => $battleInvitationMonsterId,
                'opponentHp' => $opponentHp,
                'opponentMp' => $opponentMp]);
                $addBattle->closeCursor();

                //On met à jour les stats du monstre
                $updateMonsterStats = $bdd->prepare("UPDATE car_monsters 
                SET monsterQuantityBattle = monsterQuantityBattle + 1
                WHERE monsterId = :battleInvitationCharacterId");
                $updateMonsterStats->execute(['battleInvitationCharacterId' => $battleInvitationCharacterId]);
                $updateMonsterStats->closeCursor();  
                
                //On définit une date
                $date = date('Y-m-d H:i:s');

                //Insertion des stats du combat dans la base de donnée avec les données
                $addBattleStats = $bdd->prepare("INSERT INTO car_monsters_battles_stats VALUES(
                NULL,
                :monsterBattleStatsMonsterId,
                :monsterBattleStatsCharacterId,
                'LaunchBattle',
                :monsterBattleStatsDateTime)");
                $addBattleStats->execute([
                'monsterBattleStatsMonsterId' => $battleInvitationCharacterId,
                'monsterBattleStatsCharacterId' => $characterId,
                'monsterBattleStatsDateTime' => $date]);
                $addBattleStats->closeCursor();
                
                //On supprime l'invitation
			    $deleteBattleInvitationCharacter = $bdd->prepare("DELETE FROM car_battles_invitations_characters 
			    WHERE battleInvitationCharacterId = :battleInvitationCharacterId");
			    $deleteBattleInvitationCharacter->execute(array('battleInvitationCharacterId' => $battleInvitationCharacterId));
			    $deleteBattleInvitationCharacter->closeCursor();
			    
			    //On vérifie s'il reste encore des joueurs en attente pour cette invitation
				$battleInvitationQuery = $bdd->prepare("SELECT * FROM car_battles_invitations, car_battles_invitations_characters
				WHERE battleInvitationId = battleInvitationCharacterBattleInvitationId
				AND battleInvitationId = ?");
				$battleInvitationQuery->execute([$battleInvitationId]);
				$battleInvitationRow = $battleInvitationQuery->rowCount();
				
				//S'il n'existe plus d'invitation de combat pour ce adversaire
				if ($battleInvitationRow == 0) 
				{
                    //On supprime l'invitation
					$deleteBattleInvitation = $bdd->prepare("DELETE FROM car_battles_invitations 
				    WHERE battleInvitationId = :battleInvitationId");
				    $deleteBattleInvitation->execute(array('battleInvitationId' => $battleInvitationId));
				    $deleteBattleInvitation->closeCursor();
				}
			    
                //On redirige le joueur vers le combat
                header("Location: ../../modules/battle/index.php");
            }
            //Si le monstre n'existe pas
            else
            {
            	echo "Erreur : Le adversaire n'existe pas";
            }
        }
        //Si l'invitation de combat n'exite pas
        else
        {
            echo "Erreur : Cette invitation de combat n'existe pas";
        }
        $placeQuery->closeCursor();
    }
    //Si tous les champs numérique ne contiennent pas un nombre
    else
    {
        echo "Erreur : Les champs de type numérique ne peuvent contenir qu'un nombre entier";
    }
}
//Si toutes les variables $_POST n'existent pas
else
{
    echo "Erreur : Tous les champs n'ont pas été remplis";
}

require_once("../../html/footer.php");