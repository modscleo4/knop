<?php

/*
 * @todo Implementar um sistema que escolhe o produto mais vendido para mostrar sua imagem de sua categoria
 * */

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid']) || !$_SESSION['isAdmin']) {
    header("Location: login.php");
}

$userid = $_SESSION['userid'];
$passwordDError = "";

if (isset($_POST['del'])) {
    $password = $_POST['password'];
    $sql = "SELECT senha FROM usuario WHERE id_usuario = $userid AND excluido = 'n';";
    $query = pg_query($con, $sql);
    $array = pg_fetch_array($query);

    if (md5($password) == $array['senha']) {
        $id_prod = $_POST['id_prod'];
        $data = date("Y-m-d");
        $sql = "UPDATE produtos SET excluido = TRUE, data_exclusao = '$data' WHERE id_produto = $id_prod;";
        $query = pg_query($con, $sql);
    } else {
        $passwordDError = "Senha incorreta";
    }
} else if (isset($_POST['edit'])) {
    $id_prod = $_POST['id_prod'];
    $preco = $_POST['preco'];
    $sql = "UPDATE produtos SET preco = '$preco' WHERE id_produto = $id_prod;";
    $query = pg_query($con, $sql);
}

$sql = "SELECT p.id_produto, p.nome, p.descricao, p.max_qtde, p.preco, c2.nome AS categoria FROM produtos p INNER JOIN categorias c2 on p.categoria = c2.id_cat WHERE excluido = 'n' ORDER BY id_produto;";
$query = pg_query($con, $sql);

?>

    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <title>Administração - Produtos | Knop</title>

        <!-- Icon -->
        <link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

        <!-- CSS -->
        <link href="styles/main.css" type="text/css" rel="stylesheet"/>
        <link href="styles/produtos.css" type="text/css" rel="stylesheet"/>

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
            <a id="cadProds" href="cad_produtos.php">Cadastrar produtos</a>
            <span <?php if ($passwordDError == "") { ?>style="display: none" <?php } ?>><?php echo $passwordDError; ?></span>

            <?php
                while ($array = pg_fetch_array($query)) {
                ?>
                <div class="prod">
                    <div id="img"><img src="res/catalogo/<?php echo $array['id_produto']; ?>.png" width="150"></div>

                    <div id="info">
                        <div id="nome">Nome: <?php echo $array['nome']; ?></div>
                        <div id="desc">Descrição: <?php echo $array['descricao']; ?></div>

                        <div id="preco">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <input type="text" style="display: none" name="id_prod" value="<?php echo $array['id_produto']; ?>" />
                                <label for="txtPreco">Preço: </label>
                                <input type="number" id="txtPreco" name="preco" step="0.01" value="<?php echo $array['preco']; ?>" />
                                <input type="submit" name="edit" value="Editar preço" />
                            </form>
                        </div>

                        <div id="cat">Categoria: <?php echo $array['categoria']; ?></div>

                        <div id="del">
                            <button id="btnDel" onclick="jQuery('#btnDel ~ #frmDel').css('display', 'block')">Excluir</button>
                            <form id="frmDel" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: none">
                                <input type="text" style="display: none" name="id_prod" value="<?php echo $array['id_produto']; ?>" />
                                <label for="txtPassword">Senha: </label>
                                <input type="password" id="txtPassword" name="password" />
                                <input type="submit" name="del" value="Confirmar" />
                            </form>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>
        </div>

        <?php require_once('footer.php'); ?>
    </div>

    <!-- JS -->
    <script type="text/javascript" src="scripts/main.js"></script>
    </body>
    </html>

<?php ob_end_flush(); ?>