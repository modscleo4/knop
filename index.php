<?php

/*
 * @todo Implementar um sistema que escolhe o produto mais vendido para mostrar sua imagem de sua categoria
 * */

session_start();

ob_start();

require_once("config.php");

$sql_p = "SELECT v.id_produto, p.categoria FROM vendas v INNER JOIN produtos p on v.id_produto = p.id_produto INNER JOIN categorias c2 on p.categoria = c2.id_cat GROUP BY p.categoria, v.id_produto ORDER BY count(v.id_produto) DESC";
$query_p = pg_query($con, $sql_p);

$b = 0;
$ch = 0;
$ca = 0;

while ($array_p = pg_fetch_array($query_p)) {
    if ($b == 0 && $array_p['categoria'] == 1) {
        $b = $array_p['id_produto'];
    } else if ($ch == 0 && $array_p['categoria'] == 2) {
        $ch = $array_p['id_produto'];
    } else if ($ca == 0 && $array_p['categoria'] == 3) {
        $ca = $array_p['id_produto'];
    }
}

if ($b == 0) {
    $sql_p = "SELECT id_produto FROM produtos WHERE categoria = 1 ORDER BY id_produto DESC";
    $query_p = pg_query($con, $sql_p);
    $array_p = pg_fetch_array($query_p);

    $b = $array_p['id_produto'];
}

if ($ch == 0) {
    $sql_p = "SELECT id_produto FROM produtos WHERE categoria = 2 ORDER BY id_produto DESC";
    $query_p = pg_query($con, $sql_p);
    $array_p = pg_fetch_array($query_p);

    $b = $array_p['id_produto'];
}

if ($ca == 0) {
    $sql_p = "SELECT id_produto FROM produtos WHERE categoria = 3 ORDER BY id_produto DESC";
    $query_p = pg_query($con, $sql_p);
    $array_p = pg_fetch_array($query_p);

    $b = $array_p['id_produto'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Página inicial | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/index.css" type="text/css" rel="stylesheet"/>

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
			<?php require_once('navbar.php'); ?>

			<div id="content" class="content">
				<div id="logao">
					<div id="img-big"></div>
					<p>Knop, a companhia de acessórios que sempre pôe sua felicidade em primeiro lugar.</p>
				</div>

				<div id="produtos">
					<h2>Conheça alguns dos nossos produtos</h2>
					<div id="imagens">
						<div class="img-small">
							<a href="catalogo.php?botons"><img src="res/catalogo/<?php echo $b; ?>.png"/></a>
						</div>

						<div class="img-small">
							<a href="catalogo.php?chaveiros"><img src="res/catalogo/<?php echo $ch; ?>.png"/></a>
						</div>

						<div class="img-small">
                            <a href="catalogo.php?cartoes"><img src="res/catalogo/<?php echo $ca; ?>.png"/></a>
						</div>

						<div class="descricao">
							<p><a href="catalogo.php?botons">Botons de mídias variadas</a></p>
						</div>

						<div class="descricao">
							<p><a href="catalogo.php?chaveiros">Chaveiros para enfeitar sua mochila ou estojo.</a></p>
						</div>

						<div class="descricao">
							<p><a href="catalogo.php?cartoes">Temos cartões para presentear sua família ou amigos.</a></p>
						</div>
					</div>
				</div>

				<div id="video">
					<div>
						<p>Veja abaixo como é realizado o processo de fabricação de botons.</p>
					</div>
					<iframe width="854" height="480" src="https://www.youtube.com/embed/76hNms6ufXo?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</div>
			</div>

			<?php require_once('footer.php'); ?>
		</div>

		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>