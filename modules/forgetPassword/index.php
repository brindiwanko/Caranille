<?php 
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");
?>

<center>
<h3>Vous avez oublié votre mot de passe ?</h3>
</center>

<hr>
Recevoir un nouveau mot de passe par Email
<hr>

<form method="POST" action="sendEmail.php">
    Nom de votre personnage : <input type="text" class="form-control" name="accountPseudo" maxlength="15" required>
    Email : <input type="email" class="form-control" name="accountEmail" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="continue" value="Recevoir un nouveau mot de passe">
</form>

<?php require_once("../../html/footer.php"); ?>