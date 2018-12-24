<?php

/*
 * @todo Implementar o sistema de páginas (não resetando os filtros => sugestão: enviar o form dos filtros quando alterar a página, alterando o valor do campo page)
 * @todo Remover o botão Pesquisar novamente (?)
 * @todo Realizar todas as verificações
 * */

session_start();

ob_start();

require_once("config.php");

if (isset($_SESSION['userid'])) {
	$userid = $_SESSION['userid'];
}

if ((isset($_GET['botons']) || isset($_GET['chaveiros']) || isset($_GET['cartoes']) || isset($_GET['combos'])) && !isset($_GET['todos'])) {
	$specific = true;
} else {
	$specific = false;
}
if (isset($_GET['order'])) {
	$order = $_GET['order'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Catálogo | Knop</title>


		<!-- Icon -->
		<link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

		<!-- CSS -->
		<link href="styles/main.css" type="text/css" rel="stylesheet"/>
		<link href="styles/catalogo.css" type="text/css" rel="stylesheet"/>

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
                <div id="store">
                    <form id="frmFiltros" name="filtros" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="text" id="txtSearch" name="q" style="display: none" value="<?php echo $_GET['q']; ?>"/>
                        <input type="number" id="txtPage" name="page" min="1" style="display: none" value="<?php echo $_GET['page']; ?>" />

                        <div id="sum">
                            <div id="filter-radios">
                                <span>Filtrar por</span>
                                <div class="indice" id="all">
                                    <div class="main_filter">
                                        <input id="radio1" type="radio" name="todos" <?php if (!$specific) {
                                            echo 'checked';
                                        } ?> />
                                        <label for="radio1">Tudo</label>
                                    </div>
                                </div>

                                <div id="specific">
                                    <div class="indice botons">
                                        <div class="main_filter">
                                            <input id="radio2" type="checkbox" name="botons" <?php if (isset($_GET['botons'])) {
                                                echo 'checked';
                                            } ?> />
                                            <label for="radio2">Bottons</label>
                                        </div>
                                    </div>

                                    <div class="indice botons">
                                        <div class="main_filter">
                                            <input id="radio3" type="checkbox" name="chaveiros" <?php if (isset($_GET['chaveiros'])) {
                                                echo 'checked';
                                            } ?> />
                                            <label for="radio3">Chaveiros</label>
                                        </div>
                                    </div>

                                    <div class="indice botons">
                                        <div class="main_filter">
                                            <input id="radio4" type="checkbox" name="cartoes" <?php if (isset($_GET['cartoes'])) {
                                                echo 'checked';
                                            } ?> />
                                            <label for="radio4">Cartões</label>
                                        </div>
                                    </div>

                                    <div class="indice botons">
                                        <div class="main_filter">
                                            <input id="radio5" type="checkbox" name="combos" <?php if (isset($_GET['combos'])) {
                                                echo 'checked';
                                            } ?> />
                                            <label for="radio5">Combos</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="order-radios">
                                <span>Ordenar</span>
                                <div class="indice botons">
                                    <div class="main_filter">
                                        <input id="radio6" type="radio" name="order"
                                               value="az" <?php if ($order == "az" || !isset($_GET['order'])) {
                                            echo 'checked';
                                        } ?> />
                                        <label for="radio6">A-Z</label>
                                    </div>
                                </div>

                                <div class="indice botons">
                                    <div class="main_filter">
                                        <input id="radio7" type="radio" name="order" value="za" <?php if ($order == "za") {
                                            echo 'checked';
                                        } ?> />
                                        <label for="radio7">Z-A</label>
                                    </div>
                                </div>

                                <div class="indice botons">
                                    <div class="main_filter">
                                        <input id="radio8" type="radio" name="order" value="za-p" <?php if ($order == "za-p") {
                                            echo 'checked';
                                        } ?> />
                                        <label for="radio8">Maior Preço</label>
                                    </div>
                                </div>

                                <div class="indice botons">
                                    <div class="main_filter">
                                        <input id="radio9" type="radio" name="order" value="az-p" <?php if ($order == "az-p") {
                                            echo 'checked';
                                        } ?> />
                                        <label for="radio9">Menor Preço</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div id="cat">
					<?php
					$sql = "SELECT * FROM produtos WHERE excluido = 'n'";

					if (isset($_GET['q']) && $_GET['q'] != "") {
						$q = htmlspecialchars($_GET['q']);
						$sql .= " AND upper(nome) LIKE upper('%$q%') ";
					}

					if ($specific) {
						$sql .= " AND (";
						if (isset($_GET['botons'])) {
							$sql .= "categoria = 1 OR ";
						}

						if (isset($_GET['chaveiros'])) {
							$sql .= "categoria = 2 OR ";
						}

						if (isset($_GET['cartoes'])) {
							$sql .= "categoria = 3 OR ";
						}

						if (isset($_GET['combos'])) {
							$sql .= "categoria = 4 OR";
						}
						$sql .= " 1 = 0) ";
					}

					if ($order == "az" || !isset($_GET['order'])) {
						$sql .= "ORDER BY nome ASC ";
					} else if ($order == "za") {
						$sql .= "ORDER BY nome DESC ";
					} else if ($order == "az-p") {
						$sql .= "ORDER BY preco ASC ";
					} else if ($order == "za-p") {
						$sql .= "ORDER BY preco DESC ";
					}

					$results = pg_num_rows(pg_query($con, $sql));
					$max_pages =  ceil($results / 10);

					$page = $_GET['page'];
					if ($page == "") {
					    $page = 1;
                    }

                    $sql2 = $sql;
                    do {
                        $sql = $sql2;
                        $sql .= "LIMIT 10 OFFSET " . ($page - 1) * 10;
                        $query = pg_query($con, $sql);
                        if (pg_num_rows($query) == 0) {
                            $page--;
                        }
                    } while ($page > 0 && pg_num_rows($query) == 0);

					if (pg_num_rows($query) == 0) {
						?>

                    <div>
                        <p>Nenhum produto foi encontrado com os filtros selecionados</p>
                    </div>

                    <?php
					} else {
                        if (isset($q)) {
                            ?>

                    <p class="lblResultados">Resultados da pesquisa para "<?php echo $q; ?>".</p>

                    <?php
                        }
                        ?>

                    <p class="lblResultados">Exibindo <?php echo $results; ?> produtos em <?php echo $max_pages; ?> página(s).</p>

                    <?php
					    while ($array = pg_fetch_array($query)) { ?>
						<div class="square">
							<img src="res/catalogo/<?php echo $array['id_produto']; ?>.png" width="200px" alt="">
							<div class="info">
								<div class="id_prod">
                                    <div class="left">Código: <?php echo $array['id_produto']; ?></div>
                                    <div class="right">Disponíveis: <?php echo $array['max_qtde'] ?></div>
                                </div>
								<div class="nome_prod"><?php echo $array['nome']; ?></div>
								<div class="descricao"><?php echo $array['descricao']; ?></div>
								<div class="preco">
                                    <div class="lblPreco">R$ <?php echo number_format($array['preco'], 2, ",", "."); ?></div>
                                    <div class="botao">
                                        <?php
                                        if (isset($_SESSION['userid'])) {
                                            $prodId = $array['id_produto'];
                                            $sql2 = "SELECT id_item FROM carrinho WHERE id_produto = $prodId AND id_usuario = $userid;";
                                            $query2 = pg_query($con, $sql2);

                                            if (pg_num_rows($query2) == 0 && $array['max_qtde'] > 0) {
                                                ?>

                                                <button id="btnProd<?php echo $array['id_produto']; ?>" class="btnProd">Adicionar ao carrinho</button>

                                                <div id="prod<?php echo $array['id_produto']; ?>" class="prodQtde">
                                                    <input id="numQtde" type="number" name="qtde" min="1" max="<?php echo $array['max_qtde']; ?>" value="1" required />
                                                    <button id="prodQtdeSend">Confirmar</button>

                                                    <input id="numId" type="text" name="id" value="<?php echo $array['id_produto']; ?>" style="display: none;" />
                                                </div>
                                                <?php
                                            } else if ($array['max_qtde'] == 0) {
                                                ?>

                                                <button>Indisponível</button>

                                                <?php
                                            } else {
                                                ?>

                                                <button onclick="window.location = 'cart.php'">Já no carrinho</button>

                                                <?php
                                            }
                                        } else {
                                            ?>

                                            <button id="btnProd<?php echo $array['id_produto']; ?>" class="btnProd">Fazer login</button>

                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
							</div>
						</div>

					<?php
					    }
					}
					    ?>

				</div>
                </div>

                <div id="pageSelector">
                    <?php
                    if ($max_pages > 1) {
                        $start = $page - 2;
                        if ($page <= 2) {
                            $start = 1;
                        }

                        $end = $page + 2;
                        if ($page > $max_pages - 2) {
                            $end = $max_pages;
                        }
                        for ($i = $start; $i <= $end; $i++) {
                        ?>

                    <button onclick="jQuery('#txtPage').val('<?php echo $i; ?>').change()"><?php echo $i; ?></button>

                    <?php
                        }
                    }
                        ?>
                </div>
			</div>

			<?php require_once("footer.php"); ?>

		</div>

		<!-- JS -->
        <script type="text/javascript">
            let isLogado = <?php if (isset($_SESSION['userid'])) { echo "true"; } else {echo "false"; }; ?>;
            let page = parseInt(<?php echo $page; ?>);
        </script>

		<script type="text/javascript" src="scripts/main.js"></script>
		<script type="text/javascript" src="scripts/catalogo.js"></script>
	</body>
</html>

<?php ob_end_flush(); ?>