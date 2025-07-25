<?php
require_once("../html/header.php");
require_once("../../config.php");
require_once("../../security/passwordManager.php");

//Si tous les champs ont bien été rempli
if (isset($_POST['accountPseudo']) 
&& isset($_POST['accountPassword'])
&& isset($_POST['accountPasswordConfirm'])
&& isset($_POST['accountEmail'])
&& isset($_POST['accountEmailConfirm'])
&& isset($_POST['characterRaceId'])
&& isset($_POST['characterSex'])
&& isset($_POST['characterName'])
&& isset($_POST['token'])
&& isset($_POST['register']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = uniqid();
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['characterRaceId'])
        && ctype_digit($_POST['characterSex'])
        && $_POST['characterRaceId'] >= 1
        && $_POST['characterSex'] >= 0
        && $_POST['characterSex'] <= 1)
        {
            //On récupère les valeurs du formulaire dans une variable
            $accountPseudo = htmlspecialchars(addslashes($_POST['accountPseudo']));
            $accountPassword = $_POST['accountPassword'];
            $accountPasswordConfirm = $_POST['accountPasswordConfirm'];
            $accountEmail = htmlspecialchars(addslashes($_POST['accountEmail']));
            $accountEmailConfirm = htmlspecialchars(addslashes($_POST['accountEmailConfirm']));
            $characterRaceId = htmlspecialchars(addslashes($_POST['characterRaceId']));
            $characterSex = htmlspecialchars(addslashes($_POST['characterSex']));
            $characterName = htmlspecialchars(addslashes($_POST['characterName']));
    
            //On vérifie si les deux mots de passes sont identiques (avant hash)
            if ($accountPassword == $accountPasswordConfirm) 
            {
                //On vérifie si les deux adresses emails sont identique
                if ($accountEmail == $accountEmailConfirm) 
                {
                    //On fait une requête pour vérifier si le pseudo est déjà utilisé
                    $pseudoQuery = $bdd->prepare("SELECT * FROM car_accounts 
                    WHERE accountPseudo= ?");
                    $pseudoQuery->execute([$accountPseudo]);
                    $pseudoRow = $pseudoQuery->rowCount();
                    $pseudoQuery->closeCursor();
        
                    //Si le pseudo est disponible
                    if ($pseudoRow == 0) 
                    {
                        //On fait une requête pour vérifier si l'adresse email est déjà utilisé
                        $emailQuery = $bdd->prepare("SELECT * FROM car_accounts 
                        WHERE accountEmail= ?");
                        $emailQuery->execute([$accountEmail]);
                        $emailRow = $emailQuery->rowCount();
                        $emailQuery->closeCursor();

                        //Si l'adresse email est disponible
                        if ($emailRow == 0) 
                        {
                            //On fait une requête pour vérifier si le nom du personnage est déjà utilisé
                            $characterQuery = $bdd->prepare("SELECT * FROM car_characters 
                            WHERE characterName= ?");
                            $characterQuery->execute([$characterName]);
                            $characterRow = $characterQuery->rowCount();
                            $characterQuery->closeCursor();
            
                            //Si le personnage existe
                            if ($characterRow == 0) 
                            {
                                //On fait une requête pour vérifier si le nom du personnage est déjà utilisé
                                $raceQuery = $bdd->prepare("SELECT * FROM car_races 
                                WHERE raceId = ?");
                                $raceQuery->execute([$characterRaceId]);
                                $raceRow = $raceQuery->rowCount();
                                $raceQuery->closeCursor();
            
                                //Si la race du personnage existe
                                if ($raceRow >= 1) 
                                {
                                    //Hash du mot de passe une seule fois après validation
                                    $accountPasswordHash = PasswordManager::hashPassword($accountPassword);
                                    
                                    //Variables pour la création d'un compte
                                    $date = date('Y-m-d H:i:s');
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                    $timeStamp = strtotime("now");
            
                                    /*
                                    Add account model
                                    NULL, //accountId
                                    :accountPseudo, //accountPseudo
                                    :accountPassword, //accountPassword
                                    :accountEmail, //accountEmail
                                    '2', //accountAccess
                                    '0', //accountStatus
                                    'None', //accountReason
                                    :accountLastAction, //accountLastAction
                                    :accountLastConnection, //accountLastConnection
                                    :accountIp, //accountLastIp
                                    */
            
                                    //Insertion du compte dans la base de donnée
                                    $addAccount = $bdd->prepare("INSERT INTO car_accounts VALUES(
                                    NULL,
                                    :accountPseudo,
                                    :accountPassword,
                                    :accountEmail,
                                    '',
                                    '',
                                    '2',
                                    '0',
                                    'None',
                                    :accountLastAction,
                                    :accountLastConnection,
                                    :accountIp)");
                                    $addAccount->execute([
                                    'accountPseudo' => $accountPseudo,
                                    'accountPassword' => $accountPasswordHash,
                                    'accountEmail' => $accountEmail,
                                    'accountLastAction' => $date,
                                    'accountLastConnection' => $date,
                                    'accountIp' => $ip]);
                                    $addAccount->closeCursor();
            
                                    //On recherche l'id du personnage
                                    $accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
                                    WHERE accountPseudo = ?");
                                    $accountQuery->execute([$accountPseudo]);
            
                                    while ($account = $accountQuery->fetch())
                                    {
                                        //On Stock l'id du compte
                                        $accountId = $account['accountId'];
                                    }
                                    $accountQuery->closeCursor();
            
                                    /*
                                    Add character model
                                    NULL, //characterId
                                    :accountId, //characterAccountId
                                    '0', //characterGuildId
                                    :characterRaceId, //characterRaceId
                                    '0', //characterPlaceId
                                    'http://localhost/character.png', //characterPicture
                                    :characterName, //characterName
                                    '1', //characterLevel
                                    :characterSex, //characterSex
                                    '100', //characterHpMin
                                    '100', //characterHpMax
                                    '0', //characterHpSkillPoints
                                    '0', //characterHpParchment
                                    '0', //characterHpEquipments
                                    '0', //characterHpGuild
                                    '100', //characterHpTotal
                                    '10', //characterMpMin
                                    '10', //characterMpMax
                                    '0', //characterMpSkillPoints
                                    '0', //characterMpParchment
                                    '0', //characterMpEquipments
                                    '0', //characterMpGuild
                                    '10', //characterMpTotal
                                    '1', //characterStrength
                                    '0', //characterStrengthSkillPoints
                                    '0', //characterStrengthParchment
                                    '0', //characterStrengthEquipments
                                    '0', //characterStrengthGuild
                                    '1', //characterStrengthTotal
                                    '1', //characterMagic
                                    '0', //characterMagicSkillPoints
                                    '0', //characterMagicParchment
                                    '0', //characterMagicEquipments
                                    '0', //characterMagicGuild
                                    '1', //characterMagicTotal
                                    '0', //characterAgility
                                    '0', //characterAgilitySkillPoints
                                    '0', //characterAgilityParchment
                                    '0', //characterAgilityEquipments
                                    '0', //characterAgilityGuild
                                    '0', //characterAgilityTotal
                                    '0', //characterDefense
                                    '0', //characterDefenseSkillPoints
                                    '0', //characterDefenseParchment
                                    '0', //characterDefenseEquipments
                                    '0', //characterDefenseGuild
                                    '0', //characterDefenseTotal
                                    '0', //characterDefenseMagic
                                    '0', //characterDefenseMagicSkillPoints
                                    '0', //characterDefenseMagicParchment
                                    '0', //characterDefenseMagicEquipments
                                    '0', //characterDefenseMagicGuild
                                    '0', //characterDefenseMagicTotal
                                    '0', //characterWisdom
                                    '0', //characterWisdomSkillPoints
                                    '0', //characterWisdomParchment
                                    '0', //characterWisdomEquipments
                                    '0', //characterWisdomGuild
                                    '0', //characterWisdomTotal
                                    '0', //characterProspecting
                                    '0', //characterProspectingSkillPoints
                                    '0', //characterProspectingParchment
                                    '0', //characterProspectingEquipments
                                    '0', //characterProspectingGuild
                                    '0', //characterProspectingTotal
                                    '0', //characterArenaDefeate
                                    '0', //characterArenaVictory
                                    '0', //characterExperience
                                    '0', //characterExperienceTotal
                                    '0', //characterSkillPoints
                                    '0', //characterGold
                                    '1', //characterChapter
                                    '0', //characterOnBattle
                                    '1' //characterEnable
                                    */
            
                                    $addCharacter = $bdd->prepare("INSERT INTO car_characters VALUES(
                                    NULL,
                                    :accountId,
                                    '0',
                                    :characterRaceId,
                                    '0',
                                    '../../img/empty.png',
                                    :characterName,
                                    '1',
                                    :characterSex,
                                    '100',
                                    '100',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '100',
                                    '10',
                                    '10',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '10',
                                    '1',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '1',
                                    '1',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '1',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '0',
                                    '1',
                                    '0',
                                    '1'
                                    )");
                                    $addCharacter->execute([
                                    'accountId' => $accountId,
                                    'characterRaceId' => $characterRaceId,
                                    'characterName' => $characterName,
                                    'characterSex' => $characterSex]);
                                    $addCharacter->closeCursor();
                                    ?>
            
                                    Votre compte administrateur a bien été crée, vous pouvez commencer à développer votre jeu !
            
                                    <hr>
            
                                    <form method="POST" action="../../../index.php">
                                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Commencer">
                                    </form>
                                    
                                    <?php
                                }
                                //Si la classe choisie n'existe pas
                                else
                                {
                                    ?>

                                    La classe choisie existe pas
                
                                    <hr>
                
                                    <form method="POST" action="step-4.php">
                                        <input type="hidden" class="btn btn-secondary btn-lg" name="installationType" value="retry">
                                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                        <input type="submit" name="retry" class="btn btn-secondary btn-lg" value="Recommencer">
                                    </form>
                
                                    <?php
                                }
                                $raceQuery->closeCursor();  
                            }
                            //Si le nom du personnage a déjà été utilisé
                            else
                            {
                                ?>

                                Ce nom de personnage est déjà utilisé
            
                                <hr>
            
                                <form method="POST" action="step-4.php">
                                    <input type="hidden" class="btn btn-secondary btn-lg" name="installationType" value="retry">
                                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                    <input type="submit" name="retry" class="btn btn-secondary btn-lg" value="Recommencer">
                                </form>
            
                                <?php
                            }
                            $characterQuery->closeCursor();
                        }
                        else
                        {
                            ?>

                            L'adresse email est déjà utilisée

                            <hr>

                            <form method="POST" action="step-4.php">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="installationType" value="retry">
                                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Recommencer">
                            </form>

                            <?php
                        }
                    }
                    //Si le pseudo est déjà utilisé
                    else 
                    {
                        ?>

                        Le pseudo est déjà utilisé

                        <hr>

                        <form method="POST" action="step-4.php">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="installationType" value="retry">
                            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <input type="submit" name="retry" class="btn btn-secondary btn-lg" value="Recommencer">
                        </form>

                        <?php
                    }
                    $pseudoQuery->closeCursor();   
                }
                else
                {
                    ?>

                    Les deux adresses emails entrée ne sont pas identique
    
                    <hr>
    
                    <form method="POST" action="step-4.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="installationType" value="retry">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" name="retry" class="btn btn-secondary btn-lg" value="Recommencer">
                    </form>
    
                    <?php
                }
            }
            //Si les deux mots de passe ne sont pas identique
            else 
            {
                ?>

                Les deux mots de passe entrée ne sont pas identique

                <hr>

                <form method="POST" action="step-4.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="installationType" value="retry">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" name="retry" class="btn btn-secondary btn-lg" value="Recommencer">
                </form>

                <?php
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
//Si tous les champs n'ont pas été rmepli
else 
{
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../html/footer.php"); ?>
