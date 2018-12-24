<?php

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid']) || !$_SESSION['isAdmin']) {
    header("Location: login.php");
}

if (isset($_GET['admin'])) {
    $uid = $_GET['admin'];
    $sql = "UPDATE usuario SET admin = NOT (SELECT admin FROM usuario WHERE id_usuario = $uid) WHERE id_usuario = $uid";
    $query = pg_query($con, $sql);
}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<!-- Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		
		<title>Administração - Clientes | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/clientes.css" type="text/css" rel="stylesheet"/>
		

		
	</head>
	<body>
		<!-- JS Lib -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui.min.js"></script>

		<div id="main">

			<?php require_once("navbar.php"); ?>

			<div id="content">
				<div id="tit">Clientes cadastrados na empresa</div>
				<?php
                $sql = "SELECT * FROM cliente c INNER JOIN usuario u on c.id_usuario = u.id_usuario WHERE u.excluido = 'n' order by c.id_usuario";
                $result = pg_query($con, $sql);
                $qtde = pg_num_rows($result);
            ?>
                <?php if ($qtde === 0) { ?>

                    <span>Nehmum cliente cadastrado</span>

                <?php } else { ?>

                <span><?php echo $qtde; ?> Clientes cadastrados</span>

                <div id="list">
                    <div id="header">
                        <div>ID</div>
                        <div>Login</div>
                        <div>Nome</div>
                        <div>Sobrenome</div>
                        <div>Sexo</div>
                        <div>Telefone</div>
                        <div>Celular</div>
                        <div>Opções</div>
                    </div>
                    <?php

                    while ($array = pg_fetch_array($result)) {

                        ?>
                        <div class="body">
                            <div><?php echo $array['id_usuario']; ?></div>
                            <div><?php echo $array['login']; ?></div>
                            <div><?php echo $array['nome']; ?></div>
                            <div><?php echo $array['sobrenome']; ?></div>
                            <div><?php echo $array['sexo']; ?></div>
                            <div><?php echo $array['telefone']; ?></div>
                            <div><?php echo $array['celular']; ?></div>
                            <div id="opt">
                                <a href="account.php?userid=<?php echo $array['id_usuario']; ?>">Editar</a>
                                <?php if ($array['id_usuario'] != $userid) { ?>
                                <a href="clientes.php?admin=<?php echo $array['id_usuario']; ?>">
                                    <?php

                                    $id = $array['id_usuario'];
                                    $sql = "SELECT admin FROM usuario WHERE id_usuario = $id;";
                                    $query = pg_query($con, $sql);
                                    $array = pg_fetch_array($query);

                                    if ($array['admin'] == 't') {
                                        ?>

                                    Padrão

                                    <?php

                                    } else {
                                        ?>

                                    Admin

                                    <?php
                                    }
                                        ?>
                                </a>
                                <?php } ?>
                            </div>
                        </div>

                        <?php
                    }
                }

                ?>
                </div>
			</div>

			<?php require_once("footer.php"); ?>

		</div>

		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>

