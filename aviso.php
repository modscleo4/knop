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
		<title>confirma email</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/knop.css" type="text/css" rel="stylesheet"/>

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
					<div id="top">confirmacao do email</div>
					<div id="info">
                        <p>Uma mensagem foi enviada para seu e-mail, confirme-o para ativar sua conta</p>
						<img src="res/svg/mail_white.svg" width="250px">
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