<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");
?>

<p>Ici vous aller pouvoir changer votre niveau à des fins de teste.</p>

<form method="POST" action="editLevel.php">
    Niveau : <input type="number" name="adminCharacterLevel" class="form-control" placeholder="Niveau" value="1" required>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input name="edit" class="btn btn-secondary btn-lg" type="submit" value="Modifier">
</form>

<?php require_once("../html/footer.php"); ?>