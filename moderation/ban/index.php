<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits modérateur (Accès 1) on le redirige vers l'accueil
if ($accountAccess < 1) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//On fait une recherche dans la base de donnée de tous les comptes et personnages
$accountUnBanQuery = $bdd->query("SELECT * FROM car_accounts, car_characters
WHERE accountId = characterAccountId
AND accountStatus = 0
AND accountAccess = 0
ORDER by characterName");
$accountUnBanRow = $accountUnBanQuery->rowCount();

//Si il y a des comptes non banni
if ($accountUnBanRow > 0)
{
    ?>
        <form method="POST" action="banAccount.php">
            Liste des joueurs non banni <select name="modoAccountId" class="form-control">
                
                <?php
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($accountUnBan = $accountUnBanQuery->fetch())
                {
                    $modoAccountId = $accountUnBan['accountId'];
                    $modoAccountPseudo = $accountUnBan['accountPseudo'];
                    $modoAccountCharacterName =  $accountUnBan['characterName'];
                    ?>
                    <option value="<?php echo $modoAccountId ?>"><?php echo "$modoAccountCharacterName ($modoAccountPseudo)"; ?></option>
                    <?php
                }
                $accountQuery->closeCursor();
                ?>
            
            </select>
            Raison du ban <input type="text" name="modoBanReason" class="form-control" required>
            <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
            <input type="submit" class="btn btn-secondary btn-lg" name="ban" value="Bannir le compte">
        </form>
        
    <?php
}
else
{
    echo "Aucun joueur ne peut être banni pour le moment.";
}

echo "<hr>";

//On fait une recherche dans la base de donnée de tous les comptes et personnages
$accountBanQuery = $bdd->query("SELECT * FROM car_accounts, car_characters
WHERE accountId = characterAccountId
AND accountStatus = 1
ORDER by characterName");
$accountBanRow = $accountBanQuery->rowCount();

//Si il y a des comptes banni
if ($accountBanRow > 0)
{
    ?>

    <form method="POST" action="unbanAccount.php">
        Liste des joueurs banni <select name="modoAccountId" class="form-control">
            
            <?php
            //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
            while ($accountBan = $accountBanQuery->fetch())
            {
                $modoAccountId = $accountBan['accountId'];
                $modoAccountPseudo = $accountBan['accountPseudo'];
                $modoAccountCharacterName = $accountBan['characterName'];
                $modoAccountCharacterReason = $accountBan['accountReason'];
                ?>
                <option value="<?php echo $modoAccountId ?>"><?php echo "$modoAccountCharacterName ($modoAccountPseudo) - ($modoAccountCharacterReason)"; ?></option>
                <?php
            }
            $accountQuery->closeCursor();
            ?>
        
        </select>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="unban" class="btn btn-secondary btn-lg" value="Débannir le compte">
    </form>

    <?php
}
else
{
    echo "Aucun joueur n'est actuellement banni.";
}

require_once("../html/footer.php");
