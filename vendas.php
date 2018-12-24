<?php

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid']) || !$_SESSION['isAdmin']) {
    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Administração - Vendas | Knop</title>

		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/vendas.css" type="text/css" rel="stylesheet"/>

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
                <div id="tabela">

                    <?php
                        if (!isset($_GET['cf'])) {
                            $sql = "SELECT cf FROM vendas GROUP BY cf ORDER BY cf;";
                            $query = pg_query($con, $sql);

                        if (pg_num_rows($query) == 0) { ?>
                        <div>Você ainda não possui compras registradas</div>
                <?php
                        } else {
                            while ($array = pg_fetch_array($query)) {
                                $cf = $array['cf'];
                                ?>

                                <div class="tabela_cf">
                                    <div id="titulo"><a href="vendas.php?cf=<?php echo $cf; ?>">Cupom fiscal
                                            nº <?php echo $cf; ?></a></div>

                                    <div id="headers">
                                        <div>Venda nº</div>
                                        <div>Usuário</div>
                                        <div>Produto nº</div>
                                        <div>Nome</div>
                                        <div>Preço unitário</div>
                                        <div>Qtde</div>
                                        <div>Preço total</div>
                                        <div>Data da compra</div>
                                    </div>

                                    <div id="compras">
                <?php
                                        $sql2 = "SELECT id_venda, id_usuario, vendas.id_produto, nome, preco_u, preco_t, qtde, data_compra FROM vendas INNER JOIN produtos ON produtos.id_produto = vendas.id_produto WHERE cf = $cf ORDER BY vendas.id_venda;";
                                        $query2 = pg_query($con, $sql2);

                                        while ($array = pg_fetch_array($query2)) { ?>
                                            <div class="tabela_prods">
                                                <div><?php echo $array['id_venda']; ?></div>
                                                <div><?php echo $array['id_usuario'] ?></div>
                                                <div><?php echo $array['id_produto']; ?></div>
                                                <div><?php echo $array['nome']; ?></div>
                                                <div>R$ <?php echo number_format($array['preco_u'], 2, ",", "."); ?></div>
                                                <div>x <?php echo $array['qtde']; ?></div>
                                                <div>R$ <?php echo number_format($array['preco_t'], 2, ",", "."); ?></div>
                                                <div><?php echo date_format(date_create($array['data_compra']), "d/m/Y"); ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                <?php
                            }
                        }
                    } else {
                        $cf = htmlspecialchars($_GET['cf']);
                        ?>

                        <div class="tabela_cf">
                            <div id="titulo"><a href="cupom.php?cf=<?php echo $cf; ?>">Cupom fiscal
                                    nº <?php echo $cf; ?></a></div>

                            <div id="headers">
                                <div>Venda nº</div>
                                <div>Usuário</div>
                                <div>Produto nº</div>
                                <div>Nome</div>
                                <div>Preço unitário</div>
                                <div>Qtde</div>
                                <div>Preço total</div>
                                <div>Data da compra</div>
                            </div>

                            <div id="compras">
                                <?php
                                $sql2 = "SELECT id_venda, id_usuario, vendas.id_produto, nome, preco_u, preco_t, qtde, data_compra FROM vendas INNER JOIN produtos ON produtos.id_produto = vendas.id_produto WHERE cf = $cf;";
                                $query2 = pg_query($con, $sql2);

                                while ($array = pg_fetch_array($query2)) { ?>
                                    <div class="tabela_prods">
                                        <div><?php echo $array['id_venda']; ?></div>
                                        <div><?php echo $array['id_usuario']; ?></div>
                                        <div><?php echo $array['id_produto']; ?></div>
                                        <div><?php echo $array['nome']; ?></div>
                                        <div>R$ <?php echo number_format($array['preco_u'], 2, ",", "."); ?></div>
                                        <div>x <?php echo $array['qtde']; ?></div>
                                        <div>R$ <?php echo number_format($array['preco_t'], 2, ",", "."); ?></div>
                                        <div><?php echo date_format(date_create($array['data_compra']), "d/m/Y"); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                <?php
                    }
                    ?>

                        <div id="end">Não há mais nada para mostrar</div>

                    </div>
                </div>
                
                <?php require_once("footer.php"); ?>
                
			</div>

			

		</div>

		<!-- JS -->
		<script type="text/javascript" src="scripts/main.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>