<?php

/*
 * @todo Realizar os devidos testes e verificações
 * */

session_start();

ob_start();

require_once("config.php");
$email = $_GET['email'];
$c = $_GET['c'];

$sql = "SELECT id_usuario, login, email, valido FROM usuario WHERE email = '$email';";
$query = pg_query($con, $sql);
$array = pg_fetch_array($query);

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Finalize sua conta | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/finishAccount.css" type="text/css" rel="stylesheet"/>

		<!-- Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
	</head>

	<body>
		<!-- JS Lib -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>

		<div id="main" class="main">
			<?php require_once("navbar.php"); ?>

			<div id="content">
				<?php
                if ($array['valido'] == 'f' && $c == hash("sha256", $array['email'])) {
                    $sql = "UPDATE usuario SET valido = TRUE WHERE email = '$email';";
                    $query = pg_query($con, $sql);
                    $userid = $_SESSION['userid'] = $array['id_usuario'];

                    unset($_SESSION['confirmEmail']);
                }

                header("Location: index.php");

				?>
			</div>

			<?php require_once("footer.php"); ?>

		</div>
		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
		<script type="text/javascript" src="scripts/login.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>
