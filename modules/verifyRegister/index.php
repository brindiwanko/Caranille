<?php
require_once("../../kernel/kernel.php");
require_once("../../html/header.php");
?>

<p>Veuillez remplir le formulaire ci-dessous pour finaliser votre inscription et commencer à jouer<br />

Ses informations vous ont été envoyée par Email à l'adresse indiquée lors de votre inscription</p>

<form method="POST" action="verifyRegistration.php">
    Adresse Email : <input type="email" class="form-control" name="accountEmail" required>
    Code reçu : <input type="number" class="form-control" name="codeAccountVerification" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="register" value="Je valide mon inscription">
</form>

<?php require_once("../../html/footer.php"); ?>