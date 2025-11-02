<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <title><?php echo $gameName ?></title>

        <!-- Bootstrap core CSS -->
        <link href="../../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../../css/navbar-top-fixed.css" rel="stylesheet">
    </head>

    <body>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="../../modules/main/index.php"><?php echo $gameName ?></a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Accueil</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../modules/main/index.php">Actualité</a></li>
							<li><a class="dropdown-item" href="../../modules/presentation/index.php">Présentation</a></li>
							<li><a class="dropdown-item" href="../../modules/classe/index.php">Les classes</a></li>
							<li><a class="dropdown-item" href="../../modules/contact/index.php">Contact</a></li>
							<li><a class="dropdown-item" href="../../modules/about/index.php">A propos</a></li>
						</ul>
					</li>

					<?php
					//Si le joueur est connecté on affiche le menu du jeu
					if (isset($_SESSION['account']['id']))
					{
						?>

						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Personnage (<?php echo $battleInvitationRow ?>)</a>
							<ul class="dropdown-menu" aria-labelledby="dropdown08">
								<?php
								//Si le joueur possèdes une invtation de combaz
								if ($battleInvitationRow > 0)
								{
									?>
									<li><a class="dropdown-item" href="../../modules/battleInvitation/index.php">Invitation de combat (<?php echo $battleInvitationRow ?>)</a></li>
									<?php
								}
								?>
								<li><a class="dropdown-item" href="../../modules/character/index.php">Fiche complète</a></li>
								<li><a class="dropdown-item" href="../../modules/inventory/index.php">Inventaire</a></li>
								<li><a class="dropdown-item" href="../../modules/skillPoint/index.php">Points de compétences</a></li>
							</ul>
						</li>
								
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Aventure</a>
							<ul class="dropdown-menu" aria-labelledby="dropdown08">
								<li><a class="dropdown-item" href="../../modules/story/index.php">Continuer l'aventure</a></li>

								<?php
								//Si characterplaceId est supérieur ou égal à un le joueur est dans un lieu. On met le raccourcit vers le lieu
								if($characterPlaceId >= 1)
								{
									?>

									<li><a class="dropdown-item" href="../../modules/place/index.php">Lieu actuel</a></li>

									<?php
								}
								//Si characterplaceId n'est pas supérieur ou égal à un le joueur est dans aucun lieu. On met le raccourcit vers la carte du monde
								else
								{
									?>

									<li><a class="dropdown-item" href="../../modules/map/index.php">Carte du monde</a></li>

									<?php
								}
								?>
								<li><a class="dropdown-item" href="../../modules/bestiary/index.php">Bestiaire</a></li>
								<li><a class="dropdown-item" href="../../modules/travelogue/index.php">Carnet de voyage</a></li>
							</ul>
						</li>
								
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Communauté (<?php echo $privateConversationNumberRow + $tradeRequestRow + $tradeRow + $marketOfferQuantityRow ?>)</a>
							<ul class="dropdown-menu" aria-labelledby="dropdown08">
								<li><a class="dropdown-item" href="../../modules/arena/index.php">Arène (PVP)</a></li>
								<li><a class="dropdown-item" href="../../modules/chat/index.php">Chat</a></li>
								<li><a class="dropdown-item" href="../../modules/privateConversation/index.php">Messagerie privée (<?php echo $privateConversationNumberRow ?>)</a></li>
								<li><a class="dropdown-item" href="../../modules/tradeRequest/index.php">Place des échanges (<?php echo $tradeRequestRow + $tradeRow ?>)</a></li>
								<li><a class="dropdown-item" href="../../modules/market/index.php">Le marché (<?php echo $marketOfferQuantityRow ?>)</a></li>
							</ul>
						</li>      
					<?php
					}
					?>
				</ul>
				<ul class="navbar-nav pull-right"> 
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Mon compte <?php if(isset($_SESSION['account']['id'])) { echo "($totalNotification)"; } ?></a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<?php
							//Si le joueur est connecté on lui donne la possibilité de se déconnecter
							if (isset($_SESSION['account']['id']))
							{
								?>
								
								<li><a class="dropdown-item" href="../../modules/notification/index.php">Notifications (<?php echo $notificationNumberRow ?>)</a></li>
								<li><a class="dropdown-item" href="../../modules/security/index.php">Sécurité</a></li>
								
								<?php
								switch ($accountAccess)
								{
									case 0:
									
									break;

									case 1:
									?>

									<li><a class="dropdown-item" href="../../moderation/main/index.php">Modération</a></li>

									<?php
									break;

									case 2:
									?>

									<li><a class="dropdown-item" href="../../moderation/main/index.php">Modération</a></li>
									<li><a class="dropdown-item" href="../../administration/main/index.php">Administration</a></li>
									
									<?php
									break;
								}
								?>
															
								<li><a class="dropdown-item" href="../../modules/logout/index.php">Déconnexion</a></li>
									
								<?php
							}
							//Sinon on propose au joueur de s'inscrire ou se connecter
							else
							{
								?>

								<li><a class="dropdown-item" href="../../modules/login/index.php">Connexion</a></li>
								<li><a class="dropdown-item" href="../../modules/register/index.php">Inscription</a></li>
								<li><a class="dropdown-item" href="../../modules/forgetPassword/enterCode.php">Code</a></li>
									
								<?php
							}
							?>
						</ul>
					</li>
				</ul>
			</div>
		</nav>

		<main class="container">
		  <div class="bg-light p-5 rounded">