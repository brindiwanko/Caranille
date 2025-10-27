<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../modules/login/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['secretQuestion']) 
&& isset($_POST['secretAnswer'])
&& isset($_POST['token'])
&& isset($_POST['editEnd']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //On récupère les valeurs du formulaire dans une variable
        $secretQuestion = htmlspecialchars($_POST['secretQuestion']);
        $secretAnswer = htmlspecialchars($_POST['secretAnswer']);

        //On vérifie si le joueur à jamais crée sa question secrête
        if ($accountSecretQuestion != "" && $accountSecretAnswer != "")
        {
            //On met à jour la question secrête dans la base de donnée
            $updateAccount = $bdd->prepare("UPDATE car_accounts SET 
            accountSecretQuestion = :secretQuestion,
            accountSecretAnswer = :secretAnswer
            WHERE accountId = :accountId");
            $updateAccount->execute(array(
            'secretQuestion' => $secretQuestion,
            'secretAnswer' => $secretAnswer,
            'accountId' => $accountId));
            $updateAccount->closeCursor();
            ?>
            
            La question secrète a bien été mise à jour.

            <hr>

            <form method="POST" action="index.php">
                <input type="submit" name="continue" class="btn btn-secondary btn-lg" value="Continuer">
            </form>
                
            <?php
        }
        else
        {
            ?>
        
            Vous n'avez actuellement aucune question secrète
        
            <hr>
        
            <form method="POST" action="index.php">
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" name="back" class="btn btn-secondary btn-lg" value="Retour"><br>
            </form>
        
            <?php
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
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>