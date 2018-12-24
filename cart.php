<?php

/*
 * @todo Melhorar os estilos visuais dos elementos desta página (alguns não possuem, como os botões)
 * @todo Confirmar que o subtotal funciona
 * @todo Realizar todas as verificações
 * */

session_start();

ob_start();

require_once("config.php");

if (isset($_SESSION['userid'])) {
	$userid = $_SESSION['userid'];
}

if (isset($_POST['query'])) {
    $querymode = $_POST['query'];
    if ($querymode == "qtde") {
        if (isset($_SESSION['userid'])) {
            $sql = "SELECT * FROM carrinho WHERE id_usuario = $userid;";
            $query = pg_query($con, $sql);
            $qtde = pg_num_rows($query);
            print_r($qtde);
        }
        exit;
    }

}

if (isset($_POST['add'])) {
    $prodId = $_POST['id'];
    $qtde = $_POST['qtde'];

    $sql = "SELECT id_item FROM carrinho WHERE id_produto = $prodId AND id_usuario = $userid;";
    $query = pg_query($con, $sql);

    if (pg_num_rows($query) == 0) {
        $sql = "SELECT max_qtde FROM produtos WHERE id_produto = $prodId;";
        $query = pg_query($con, $sql);
        $array = pg_fetch_array($query);

        if ($qtde <= $array['max_qtde']) {
            $sql = "INSERT INTO carrinho VALUES (DEFAULT, $userid, $prodId, $qtde);";
            if ($query = pg_query($con, $sql)) {
                echo "add";
            }
        }
    }

    exit;
}

if (isset($_POST['del'])) {
    $itemId = $_POST['id'];

    $sql = "DELETE FROM carrinho WHERE id_item = $itemId;";
    $query = pg_query($con, $sql);
}

if (isset($_POST['upd'])) {
    $itemId = $_POST['id'];
    $qtde = $_POST['qtde'];

    $sql = "SELECT max_qtde FROM carrinho INNER JOIN produtos ON carrinho.id_produto = produtos.id_produto WHERE id_item = $itemId ORDER BY id_item;";
    $query = pg_query($con, $sql);
    $array = pg_fetch_array($query);

    if ($qtde <= $array['max_qtde']) {
        $sql = "UPDATE carrinho SET qtde = $qtde WHERE id_item = $itemId;";
        $query = pg_query($con, $sql);
    }
}

if (isset($_POST['finish'])) {
    $sql = "SELECT id_item, carrinho.id_produto, qtde, max_qtde, preco FROM carrinho INNER JOIN produtos ON carrinho.id_produto = produtos.id_produto WHERE id_usuario = $userid ORDER BY id_item;";
    $query = pg_query($con, $sql);

    $sql2 = "SELECT nextval('cf_seq'::regclass)";
    $query2 = pg_query($con, $sql2);
    $array2 = pg_fetch_array($query2);
    $cf = $array2['nextval'];

    while ($array = pg_fetch_array($query)) {
        $prodId = $array['id_produto'];
        $precoU = $array['preco'];
        $qtde = $array['qtde'];
        $precoT = $precoU * $qtde;
        $date = date("Y-m-d");

        $sql2 = "INSERT INTO vendas VALUES (DEFAULT, $userid, $prodId, '$precoU', '$precoT', $qtde, '$date', $cf) RETURNING id_venda;";
        $query2 = pg_query($con, $sql2);
        $array2 = pg_fetch_array($query2);
        $vendaId = $array2['id_venda'];

        $qtde = $array['max_qtde'] - $qtde;
        $sql2 = "UPDATE produtos SET max_qtde = $qtde WHERE id_produto = $prodId;";
        $query2 = pg_query($con, $sql2);

        $itemId = $array['id_item'];
        $sql2 = "UPDATE carrinho SET id_venda = $vendaId WHERE id_item = $itemId;";
        $query2 = pg_query($con, $sql2);
    }

    $sql = "DELETE FROM carrinho WHERE id_usuario = $userid;";
    $query = pg_query($con, $sql);

    header("Location: compras.php?cf=$cf");
}

$subtotal = 0.00;
$sql = "SELECT sum(preco * qtde) FROM carrinho INNER JOIN produtos ON carrinho.id_produto = produtos.id_produto WHERE id_usuario = $userid GROUP BY id_item ORDER BY id_item;";
$query = pg_query($con, $sql);
while ($array = pg_fetch_array($query)) {
    $subtotal += $array['sum'];
}

$sql = "SELECT * FROM carrinho INNER JOIN produtos ON carrinho.id_produto = produtos.id_produto WHERE id_usuario = $userid ORDER BY id_item;";
$query = pg_query($con, $sql);

?>

<!DOCTYPE html>
	<html lang="pt-br">
	<head>
		<title>Carrinho | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/cart.css" type="text/css" rel="stylesheet"/>

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
					<div id="top">
                        <div>Carrinho</div>

                        <?php if (isset($_SESSION['userid'])) { ?>

                        <div id="divBtn">
                            <span>Subtotal: R$ <?php echo number_format($subtotal, 2, ",", "."); ?></span>
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input class="buttonSend" type="submit" name="finish" value="Finalizar compra" />
                            </form>
                        </div>

                        <?php } ?>

                    </div>
                    <div id="cart-content">
                        <?php if (pg_num_rows($query) > 0) {
                            while ($array = pg_fetch_array($query)) { ?>

                            <div class="cart-item">
                                <div class="item-img">
                                    <img src="res/catalogo/<?php echo $array['id_produto']; ?>.png" alt="">
                                </div>
                                <span class="item-title">
                                    <div><?php echo $array['nome'] ?></div>
                                    <div>
                                        <form id="frmQtde<?php echo $array['id_item']; ?>" class="frmQtde" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                            <input type="text" value="<?php echo $array['id_item']; ?>" name="id" style="display: none;" />

                                            <label for="qtde">Quantidade:</label>
                                            <input id="qtde" type="number" name="qtde" min="1" max="<?php echo $array['max_qtde'] ?>" value="<?php echo $array['qtde'] ?>" />

                                            <input type="submit" name="upd" value="Atualizar" />
                                        </form>
                                    </div>
                                </span>
                                <div class="price-remove">
                                    <span>R$ <?php echo number_format($array['qtde'] * $array['preco'], 2, ",", "."); ?></span>
                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                        <input type="text" value="<?php echo $array['id_item']; ?>" name="id" style="display: none;" />
                                        <input type="submit" value="Remover" name="del" />
                                    </form>
                                </div>
                            </div>

                            <?php }
                        } else { ?>

                            <div id="info">
                                <img id="lasainha" src="res/cart_dark.svg"/>
                                <span>Carrinho vazio :/ </span>
                            </div>

                        <?php } ?>

                    </div>
				</div>
			</div>

			<?php require_once("footer.php"); ?>

		</div>

		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
		<script type="text/javascript" src="scripts/cart.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>
