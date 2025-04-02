<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");
?>

Afin de réinitialiser votre mot de passe, veuillez saisir le code que vous avez reçu par Email
        
<hr>

<form method="POST" action="changePassword.php">
    Code reçu : <input type="text" class="form-control" name="accountCode" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="resetPassword" class="btn btn-secondary btn-lg" value="Se connecter">
</form>

<?php require_once("../../html/footer.php"); ?>