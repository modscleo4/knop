<?php

session_start();

ob_start();

require_once("config.php");

if (isset($_SESSION['userid'])) {
	$userid = $_SESSION['userid'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Sobre | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/about.css" type="text/css" rel="stylesheet"/>

		<!-- Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
	</head>
	<body>
		<!-- JS Lib -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui.min.js"></script>

		<div id="main">
			<?php require_once("navbar.php"); ?>

			<div id="content">
				<div id="about">
					<div id="top">Alunos que desenvolveram o projeto</div>

					<div id="info">
						<div class="dev">
							<img src="res/about/07_dhiego.jpg" alt="">
							<div class="pic">
								<p class="devName">07 - Dhiego Cassiano Fogaça Barbosa</p>
								<p>Líder do projeto</p>
								<p>Revisor final</p>
							</div>
						</div>


						<div class="dev">
							<img src="res/about/10_francisco.jpg" alt="">
							<div class="pic">
								<p class="devName">10 - Francisco Pinheiro da Silveira</p>
								<p>Desenvolvedor front-end</p>
							</div>
						</div>

						<div class="dev">
							<img src="res/about/12_gabriel.jpg" alt="">
							<div class="pic">
								<p class="devName">12 - Gabriel Henrique Garcia</p>
								<p>Desenvolvedor front-end</p>
								<p>Designer vetorial</p>
							</div>
						</div>

						<div class="dev">
							<img src="res/about/21_lucas.jpg" alt="">
							<div class="pic">
								<p class="devName">21 - Lucas Henrique Hajzok Martins</p>
								<p>Desenvolvedor back-end</p>
								<p>Supervisor</p>
							</div>
						</div>

						<div class="dev">
							<img src="res/about/27_micaela.jpg" alt="">
							<div class="pic">
								<p class="devName">27 - Micaela de Deus Oliveira</p>
								<p>Desenvolvedora front-end</p>
								<p>Adminstradora</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php require_once("footer.php"); ?>
		</div>

		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>