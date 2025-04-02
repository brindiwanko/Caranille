<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../modules/login/index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//On vérifie si le joueur à jamais crée sa question secrête
if ($accountSecretQuestion == "" && $accountSecretAnswer == "")
{
    ?>

    Veuillez choisir une question et une réponse secrète

    <hr>

    <form method="POST" action="addSecretQuestionEnd.php">
        Question : <input type="text" class="form-control" name="secretQuestion" maxlength="100" required>
        Réponse : <input type="text" class="form-control" name="secretAnswer" maxlength="100" required>
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="add" class="btn btn-secondary btn-lg" value="Créer la question secrête"><br>
    </form>

    <?php
}
else
{
    ?>

    Vous avez déjà une question secrête

    <hr>

    <form method="POST" action="index.php">
        <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
        <input type="submit" name="back" class="btn btn-secondary btn-lg" value="Retour"><br>
    </form>

    <?php
}

require_once("../../html/footer.php"); ?>