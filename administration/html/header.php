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
			<a class="navbar-brand" href="index.php">Panel Administration</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Ressources</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/accounts/index.php">Comptes/Personnages</a></li>
							<li><a class="dropdown-item" href="../../administration/equipments/index.php">Equipements</a></li>
							<li><a class="dropdown-item" href="../../administration/items/index.php">Objets</a></li>
							<li><a class="dropdown-item" href="../../administration/monsters/index.php">Monstres</a></li>
							<li><a class="dropdown-item" href="../../administration/parchments/index.php">Parchemins</a></li>
							<li><a class="dropdown-item" href="../../administration/places/index.php">Lieux</a></li>
							<li><a class="dropdown-item" href="../../administration/classes/index.php">Classes</a></li>
							<li><a class="dropdown-item" href="../../administration/shops/index.php">Magasins</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Scénario</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/chapters/index.php">Chapitres</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Communication</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/news/index.php">News</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Evénement</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/battlesInvitationsNominative/index.php">Invitation de combat nominative</a></li>
							<li><a class="dropdown-item" href="../../administration/battlesInvitationsRandom/index.php">Invitation de combat aléatoire</a></li>
							<li><a class="dropdown-item" href="../../administration/offerExperience/index.php">Offrir expérience</a></li>
							<li><a class="dropdown-item" href="../../administration/offerGold/index.php">Offrir pièce(s) d'or</a></li>
							<li><a class="dropdown-item" href="../../administration/offerItem/index.php">Offrir objet</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Configuration</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/configuration/index.php">Jeu</a></li>
							<li><a class="dropdown-item" href="../../administration/itemsTypes/index.php">Types d'objets</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Debug</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/debugLevel/index.php">Modifier mon niveau</a></li>
							<li><a class="dropdown-item" href="../../administration/debugMonster/index.php">Tester un monstre</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Galerie</a>
						<ul class="dropdown-menu" aria-labelledby="dropdown08">
							<li><a class="dropdown-item" href="../../administration/picturesEquipments/index.php">Equipements</a></li>
							<li><a class="dropdown-item" href="../../administration/picturesItems/index.php">Objets</a></li>
							<li><a class="dropdown-item" href="../../administration/picturesMonsters/index.php">Monstres</a></li>
							<li><a class="dropdown-item" href="../../administration/picturesParchments/index.php">Parchemins</a></li>
							<li><a class="dropdown-item" href="../../administration/picturesPlaces/index.php">Lieux</a></li>
							<li><a class="dropdown-item" href="../../administration/picturesClasses/index.php">Classes</a></li>
							<li><a class="dropdown-item" href="../../administration/picturesShops/index.php">Magasins</a></li>
						</ul>
					</li>
				</ul>
				<ul class="navbar-nav pull-right"> 
					<li class="nav-item dropdown">
						 <a class="nav-link" href="../../index.php">Retour au jeu</a>
					</li>
				</ul>
			</div>
		</nav>

		<main class="container">
		  <div class="bg-light p-5 rounded">