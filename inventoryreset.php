<?php

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid']) || !$_SESSION['isAdmin']) {
    header("Location: login.php");
}

$sql = "SELECT id_produto FROM produtos;";
$query = pg_query($con, $sql);
while ($array = pg_fetch_array($query)) {
    unlink("anual/res/catalogo/" . $array['id_produto'] . ".png");
}

$sql = "DELETE FROM vendas;
DELETE FROM carrinho;
DELETE FROM produtos;

ALTER SEQUENCE produtos_codigo_seq RESTART WITH 1;
ALTER SEQUENCE carrinho_id_item_seq RESTART WITH 1;
ALTER SEQUENCE vendas_id_venda_seq RESTART WITH 1;
ALTER SEQUENCE cf_seq RESTART WITH 1;
";

$query = pg_query($con, $sql);

echo pg_last_error();

