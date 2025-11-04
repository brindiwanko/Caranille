<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits modérateurs (Accès 1) on le redirige vers l'accueil
if ($accountAccess < 1) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['modoAccountId'])
&& isset($_POST['modoBanReason'])
&& isset($_POST['token'])
&& isset($_POST['ban']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        
        //On vérifie si tous les champs numérique contiennent bien un nombre entier positif
        if (ctype_digit($_POST['modoAccountId'])
        && $_POST['modoAccountId'] >= 1)
        {
            //On récupère l'id du formulaire précédent
            $modoAccountId = htmlspecialchars($_POST['modoAccountId']);
            $modoBanReason = htmlspecialchars($_POST['modoBanReason']);

            //On fait une requête pour vérifier si le compte choisit existe
            $accountQuery = $bdd->prepare("SELECT * FROM car_accounts 
            WHERE accountId = ?");
            $accountQuery->execute([$modoAccountId]);
            $account = $accountQuery->rowCount();

            //Si le compte existe
            if ($account == 1) 
            {
                //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                while ($account = $accountQuery->fetch())
                {
                    //On récupère les informations du compte
                    $modoAccountPseudo = $account['accountPseudo'];
                    $modoAccountStatus = $account['accountStatus'];
                    $modoAccountReason = $account['accountReason'];
                }

                //Si le compte n'est pas encore banni
                if ($modoAccountStatus == 0)
                {
                    ?>
                
                    <p>ATTENTION</p> 
    
                    Vous êtes sur le point de bannir le compte <em><?php echo $modoAccountPseudo ?></em>.<br />
                    Pour la raison suivante : <?php echo $modoBanReason ?><br />
                    Confirmez-vous ?
    
                    <hr>
                        
                    <form method="POST" action="banAccountEnd.php">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="modoAccountId" value="<?php echo $modoAccountId ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="modoBanReason" value="<?php echo $modoBanReason ?>">
                        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                        <input type="submit" class="btn btn-secondary btn-lg" name="banEnd" value="Je confirme le banissement">
                    </form>
                    
                    <hr>
    
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
                //Si le compte est déjà banni
                else
                {
                    ?>
                
                    <p>ATTENTION</p> 
    
                    Le compte <em><?php echo $modoAccountPseudo ?></em> est déjà banni<br />
                    Raison du ban : <?php echo $modoAccountReason ?><br />

                    <hr>
    
                    <form method="POST" action="index.php">
                        <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
                    </form>
                    
                    <?php
                }
            }
            //Si le compte n'existe pas
            else
            {
                echo "Erreur : Ce compte n'existe pas";
            }
            $accountQuery->closeCursor();
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