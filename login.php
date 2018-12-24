          <?php

/*
 * @melhorar o estilo visual
 * */

session_start();

ob_start();

require_once("config.php");

/* Verifica se o usuário já está conectado, e, caso esteja, redireciona para a homepage */
if (isset($_SESSION['userid'])) {
	header("Location: index.php");
}

$emailError = "";
$passwordError = "";

if (isset($_GET['m'])) {
    $m = $_GET['m'];
    if ($m == 1) {
        $emailError = 'Bem vindo de volta!';
    }
}

if (isset($_SESSION['confirmEmail'])) {
    $uemail = $_SESSION['confirmEmail'];

    $emailError = "Confirme seu endereço de email antes. " . "<a href=\"javascript:resend('$uemail'); \">Reenviar</a>";
}

/* Quando o form for enviado, o input[type=submit] também será e o código do if será executado */
if (isset($_POST['send'])) {
	/* Para evitar erros com ' ou `, utiliza-se a função htmlspecialchars() */
	$email = htmlspecialchars($_POST['email']);
	$password = htmlspecialchars($_POST['password']);

	$sql = "SELECT email, senha, id_usuario, valido, admin FROM usuario WHERE (email = '$email' OR login = '$email') AND excluido = 'n';";
	$query = pg_query($con, $sql);
	if (pg_num_rows($query) == 0) {
		// Não é um email/username cadastrado

        /* O username não está nem no admin nem no público, logo não existe */
        $emailError = "Não existe um usuário com esse email/nome de usuário.";
	} else {
		$array = pg_fetch_array($query);

		/* Caso a senha informada seja a mesma cadastrada no banco, o usuário será autenticado (utiliza-se a função password_verify() para esta veriificação */
		if (password_verify($password, $array['senha']) || md5($password) == $array['senha']) {
			// Logado como admin

			/* Verifica-se se o usuário já confirmou o endereço de email */
			if ($array['valido'] != 'f') {
				$_SESSION['userid'] = $array['id_usuario'];
				$_SESSION['isAdmin'] = $array['admin'] == 't';
                unset($_SESSION['confirmEmail']);
                if (isset($_POST['redirect']) && $_POST['redirect'] != "") {
                    header("Location: " . $_POST['redirect']);
                } else {
                    header("Location: index.php");
                }
			} else {
				$uemail = $array['email'];
				$emailError = "Confirme seu endereço de email antes. " . "<a href=\"javascript:resend('$uemail'); \">Reenviar</a>";
			}
		} else {
			$passwordError = "Senha incorreta";
		}
	}
}
?>

<!doctype html>
<html lang="pt-br">
	<head>
		<title>Fazer login | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/login.css" type="text/css" rel="stylesheet"/>

		<!-- Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
	</head>

	<body>
		<!-- JS Lib -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui.min.js"></script>

		<div id="main" class="main">
			<?php require_once("navbar.php"); ?>

			<div id="content" onclick="jQuery('#emailConfirm').css('display', 'none')">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="form-login" name="form_login" method="post">
                    <input name="redirect" type="text" style="display: none;" value="<?php echo $_GET['redirect']; ?>" />
                    
                    <div id="div_login">
						<div id="logo">
							<img id="logo_img" src="res/logo.svg" alt="">
						</div>

						<div id="fields">
							<div class="field">
								<input class="textInput" type="text" name="email" id="txtEmail" value="<?php echo $email; ?>" required autofocus maxlength="40" /><br/>
								<label for="txtEmail">Email ou nome de usuário</label>
								<p <?php if ($emailError == "") { ?>style="display: none;" <?php } ?>><?php echo $emailError; ?></p>
							</div>

							<div class="field">
								<input class="textInput" type="password" name="password" id="txtPassword" required maxlength="16" /><br/>
								<label for="txtPassword">Senha</label>
								<p <?php if ($passwordError == "") { ?>style="display: none;" <?php } ?>><?php echo $passwordError; ?></p>
							</div>
						</div>

						<div id="actions">
							<div id="login_actions">
								<div>
									<input type="checkbox" id="keepLogged" name="keepLogged"/>
									<label for="keepLogged">Mantenha-me conectado</label>
								</div>
								<input class="buttonSend" type="submit" name="send" value="Fazer login"/>
							</div>

							<div id="links">
								<div id="signin"><a href="signup.php">Criar conta</a></div>
								<div id="forgot"><a href="forgot.php">Esqueci minha senha</a></div>
							</div>

							<div><a href="javascript:window.history.back();">Voltar</a></div>
						</div>
					</div>
				</form>
			</div>

			<?php require_once("footer.php"); ?>

		</div>

		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
		<script type="text/javascript" src="scripts/login.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>