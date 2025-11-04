<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../modules/login/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['secretAnswer'])
&& isset($_POST['token'])
&& isset($_POST['edit']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
        $_SESSION['token'] = NULL;

        //Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        //On récupère les valeurs du formulaire dans une variable
        $secretAnswer = htmlspecialchars($_POST['secretAnswer']);

        //On vérifie si le joueur à jamais crée sa question secrête
        if ($accountSecretQuestion != "" && $accountSecretAnswer != "")
        {
            //Si la réponse secrète entrée est correcte
            if ($secretAnswer == $accountSecretAnswer)
            {
                ?>

                Veuillez choisir une question et une réponse secrète
            
                <hr>
            
                <form method="POST" action="changeSecretQuestionVerifyEnd.php">
                    Question : <input type="text" class="form-control" name="secretQuestion" maxlength="100" required>
                    Réponse : <input type="text" class="form-control" name="secretAnswer" maxlength="100" required>
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" name="editEnd" class="btn btn-secondary btn-lg" value="Créer la question secrête"><br>
                </form>
            
                <?php
            }
            else
            {
                ?>

                La réponse entrée est incorrect
            
                <hr>
            
                <form method="POST" action="index.php">
                    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                    <input type="submit" name="back" class="btn btn-secondary btn-lg" value="Retour"><br>
                </form>
            
                <?php
            }
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