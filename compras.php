<?php

/*
* @todo Melhorar o estilo visual desta página
 * */

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
} else {
    $userid = $_SESSION['userid'];
}

?>

<!doctype html>
    <html lang="pt-br">
    <head>
        <title>Histórico de compras | Knop</title>

        <!-- Icon -->
        <link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

        <!-- CSS -->
        <link href="styles/main.css" type="text/css" rel="stylesheet"/>
        <link href="styles/compras.css" type="text/css" rel="stylesheet"/>

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

            <div id="content">

            <?php
                if (!isset($_GET['cf'])) {
                    $sql = "SELECT cf FROM vendas WHERE id_usuario = $userid GROUP BY cf ORDER BY cf;";
                    $query = pg_query($con, $sql);

                    if (pg_num_rows($query) == 0) { ?>

                <span id="nobuy">Você ainda não possui compras registradas</span>

                <?php
                    } else {
                    ?>

                <div id="tabela">

                    <?php
                        while ($array = pg_fetch_array($query)) {
                            $cf = $array['cf'];
                        ?>

                    <div class="tabela_cf">
                        <div id="titulo"><a href="compras.php?cf=<?php echo $cf; ?>">Cupom fiscal nº <?php echo $cf; ?></a></div>

                        <div id="headers">
                            <div>Venda nº</div>
                            <div>Produto nº</div>
                            <div>Nome</div>
                            <div>Preço unitário</div>
                            <div>Qtde</div>
                            <div>Preço total</div>
                            <div>Data da compra</div>
                        </div>

                        <div id="compras">
                        <?php
                            $sql2 = "SELECT id_venda, vendas.id_produto, nome, preco_u, preco_t, qtde, data_compra FROM vendas INNER JOIN produtos ON produtos.id_produto = vendas.id_produto WHERE vendas.id_usuario = $userid AND cf = $cf ORDER BY vendas.id_venda;";
                            $query2 = pg_query($con, $sql2);

                            while ($array = pg_fetch_array($query2)) { ?>
                                <div class="tabela_prods">
                                    <div><?php echo $array['id_venda']; ?></div>
                                    <div><?php echo $array['id_produto']; ?></div>
                                    <div><?php echo $array['nome']; ?></div>
                                    <div>R$ <?php echo number_format($array['preco_u'], 2, ",", "."); ?></div>
                                    <div>x <?php echo $array['qtde']; ?></div>
                                    <div>R$ <?php echo number_format($array['preco_t'], 2, ",", "."); ?></div>
                                    <div><?php echo date_format(date_create($array['data_compra']), "d/m/Y"); ?></div>
                                </div>
                            <?php
                            }
                                ?>
                        </div>
                    </div>

                        <?php
                        }
                            ?>

            <?php
                    }
                } else {
                    $cf = htmlspecialchars($_GET['cf']);
                    ?>
                    <div id="tabela">
                        <div class="tabela_cf">
                            <div id="titulo"><a href="compras.php?cf=<?php echo $cf; ?>">Cupom fiscal nº <?php echo $cf; ?></a></div>

                            <div id="headers">
                                <div>Venda nº</div>
                                <div>Produto nº</div>
                                <div>Nome</div>
                                <div>Preço unitário</div>
                                <div>Qtde</div>
                                <div>Preço total</div>
                                <div>Data da compra</div>
                            </div>

                            <div id="compras">
                                <?php
                                $sql2 = "SELECT id_venda, vendas.id_produto, nome, preco_u, preco_t, qtde, data_compra FROM vendas INNER JOIN produtos ON produtos.id_produto = vendas.id_produto WHERE vendas.id_usuario = $userid AND cf = $cf;";
                                $query2 = pg_query($con, $sql2);

                                while ($array = pg_fetch_array($query2)) { ?>
                                    <div class="tabela_prods">
                                        <div><?php echo $array['id_venda']; ?></div>
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

                </div>

            </div>

            <?php require_once("footer.php"); ?>

        </div>

        <!-- JS -->
        <script type="text/javascript" src="scripts/main.js"></script>
        <script type="text/javascript" src="scripts/compras.js"></script>
    </body>
</html>
