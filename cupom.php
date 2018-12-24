<?php

/*
 * @todo Finalizar o front-end desta página (será a partir do CF apresentado aqui que o usuário irá retirar os produtos no caixa)
 * @todo Realizar todas as verificações
 * */

session_start();

ob_start();

require_once("config.php");

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
}

$sql = "SELECT vendas.id_produto, nome, vendas.qtde, preco, vendas.id_venda FROM vendas INNER JOIN produtos ON vendas.id_produto = produtos.id_produto WHERE id_usuario = $userid AND cf = $cf ORDER BY id_venda;";
$query = pg_query($con, $sql);

$i = 0;
$arr = [];

$num_rows = pg_num_rows($query);

while ($array = pg_fetch_array($query)) {
    $cf = $array['cf'];
    $vendaId = $array['id_venda'];
    $prodId = $array['id_produto'];
    $nome = $array['nome'];
    $precoU = $array['preco'];
    $qtde = $array['qtde'];
    $precoT = $precoU * $qtde;

    $arr[$i] = [
        "cf" => $cf,
        "idVenda" => $vendaId,
        "idProduto" => $prodId,
        "nomeProduto" => $nome,
        "precoU" => number_format($precoU, 2, ",", "."),
        "qtde" => $qtde,
        "precoT" => number_format($precoT, 2, ",", ".")
    ];
    $i++;
}

$cupom = json_encode($arr);

$sql = "DELETE FROM carrinho WHERE id_usuario = $userid;";
$query = pg_query($con, $sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Cupom fiscal | Knop</title>

        <!-- Icon -->
        <link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

        <!-- CSS -->
        <link href="styles/main.css" type="text/css" rel="stylesheet"/>
        <link href="styles/cupom.css" type="text/css" rel="stylesheet"/>

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
                <?php if ($num_rows == 0) { ?>

                <div>Desculpe, não foram encontrados produtos comprados por este usuário com o cupom fical fornecido</div>

                <?php } else { ?>

                <div>
                    <div></div>
                </div>

                <script type="text/javascript">
                    let cupom = JSON.parse(`<?php echo $cupom; ?>`);

                    for (let i = 0; i < cupom.length; i++) {
                        for (let j = 0; j < cupom[0].length; j++) {
                            console.log(cupom[i][j]);
                        }
                    }
                </script>

                <?php } ?>
            </div>

            <?php require_once("footer.php"); ?>

        </div>

        <!-- JS -->
        <script type="text/javascript" src="scripts/main.js"></script>
        <script type="text/javascript" src="scripts/cupom.js"></script>
    </body>
</html>

<?php ob_end_flush(); ?>

