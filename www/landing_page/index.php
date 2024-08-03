<!DOCTYPE HTML>
<!--
	Helios by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>

<head>
	<title>festPOS start page</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
	<link rel="stylesheet" href="assets/css/main.css" />
	<link rel="icon" href="favicon.png" type="image/png" />
	<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
</head>

<body class="homepage">
	<div id="page-wrapper">

		<!-- Header -->
		<div id="header">

			<!-- Inner -->
			<div class="inner">
				<header>
					<h1><a href="index.html" id="logo">festPOS</a></h1>
					<hr />
					<p>Il POS su misura per la tua sagra</p>
				</header>
				<footer>
					<a href="../fest_pos_engine/" class="button circled scrolly">Avvia</a>
				</footer>
			</div>

			<!-- Nav -->
			<nav id="nav">
				<ul>
					<li><a href="../fest_pos_engine/index.php">Avvia cassa</a></li>
					<li>
						<a href="#">Statistiche</a>
						<ul>
							<?php 
								$var = "?a=" . strval(floor(microtime(true) * 1000));
							?>
							<li><a href="../fest_pos_engine/statistiche/scontrini.csv<?= $var ?>">Scarica database scontrini</a></li>
							<li><a href="../fest_pos_engine/statistiche/prodotti_venduti.json<?= $var ?>">Visualizza quantita' prodotti venduti</a></li>
							<li><a href="../fest_pos_engine/statistiche/incasso.txt<?= $var ?>">Visualizza totale incasso</a></li>
							<li><a href="../fest_pos_engine/statistiche/counter.txt<?= $var ?>">Visualizza numero scontrini emessi</a></li>
							<li><a href="../fest_pos_engine/chiusura_cassa.php">Chiusura cassa</a></li>
						</ul>
					</li>
					<li>
						<a href="#">Debug</a>
						<ul>
							<li><a href="../fest_pos_engine/print_demo.php">Lancia una stampa di prova</a></li>
							<li><a href="../fest_pos_engine/php_info.php">PHP info</a></li>
							<li><a href="../fest_pos_engine/secret">Secret</a></li>
						</ul>
					</li>
					<li>
						<a href="#">Website</a>
						<ul>
							<li><a href="../fest_pos_website">Il sito di FestPOS</a></li>
							<li><a href="https://github.com/thefax/festpos">FestPOS su github</a></li>
						</ul>
					</li>

				</ul>
			</nav>

		</div>


	</div>

	<!-- Scripts -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/jquery.dropotron.min.js"></script>
	<script src="assets/js/jquery.scrolly.min.js"></script>
	<script src="assets/js/jquery.onvisible.min.js"></script>
	<script src="assets/js/skel.min.js"></script>
	<script src="assets/js/util.js"></script>
	<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
	<script src="assets/js/main.js"></script>

</body>

</html>