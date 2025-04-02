<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php"); ?>
   
<form method="POST" action="startConnection.php">
    Pseudo : <input class="form-control" type="text" name="accountPseudo" required>
    Mot de passe : <input class="form-control" type="password" name="accountPassword" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="login" class="btn btn-secondary btn-lg" value="Se connecter"></center>
</form>

<hr>

<form method="POST" action="../../modules/forgetPassword/index.php">
    <input type="submit" name="forgetPassword" class="btn btn-secondary btn-lg" value="Mot de passe oublié ?"></center>
</form>
                
<?php require_once("../../html/footer.php"); ?>