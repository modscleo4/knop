<?php

/*
 * @todo Melhorar o estido visual desta página
 * */

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid']) || !$_SESSION['isAdmin']) {
    header("Location: login.php");
}

$nome = $_POST['nome_prod'];
$desc = $_POST['desc_prod'];
$preco = (float)$_POST['preco_prod'];
$max = $_POST['max_prod'];
$cat = $_POST['cat_prod'];


$target_dir = "res/catalogo/";
$uploadOk = 1;

if( isset($_POST["submit"]) ) {
    $sql = "INSERT INTO produtos VALUES (DEFAULT, '$nome', '$desc', '$preco', DEFAULT, NULL, $max, $cat) RETURNING id_produto";
    $query = pg_query($con, $sql);
    $array = pg_fetch_array($query);

    $target_file = $target_dir . $array['id_produto'] . "." . pathinfo($_FILES['image']['name'])['extension'];

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check == false) {
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "O arquivo já existe";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) echo "Falha no upload";
    else {
        echo $target_file;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "imagem enviada";
        }
        else {
            echo "Falha no envio da imagem";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Administração - Produtos | Knop</title>

        <!-- Icon -->
        <link href="res/favicon.png" type="image/png" rel="shortcut icon" />

        <!-- CSS -->
        <link href="styles/cad_produtos.css" type="text/css" rel="stylesheet" />
        <link href="styles/main.css" type="text/css" rel="stylesheet"/>

        <!-- Meta -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
    </head>

    <body>

        <!-- JS Lib -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="scripts/jquery-ui.min.js"></script>

        <div id="main">
            <?php require_once("navbar.php"); ?>

            <div id="content">
                <div id="produtao">
                    <h2>Cadastro de produtos</h2>
                </div>
                <div id="form_prod">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                        <div>
                            <p><label for="name_prod">Nome do produto:</label></p>
                        </div>

                        <div class="cad_input">
                            <input type="text" id="name_prod" name="nome_prod">
                        </div> 

                        <div>
                            <p><label for="desc_prod">Descrição:</label></p>
                        </div>

                        <div class="cad_input">
                            <input type="text" id="desc_prod" name="desc_prod">
                        </div>

                        <div>
                            <p><label for="preco_prod">Preço:</label></p>
                        </div>

                        <div class="cad_input">
                            <input type="text" id="preco_prod" name="preco_prod">
                        </div>

                        <div>
                            <p><label for="max_prod">Quantidade máxima:</label></p>
                        </div>

                        <div class="cad_input">
                            <input type="text" id="max_prod" name="max_prod">
                        </div>

                        <div><p>Categoria:</p></div>
                        <div id="cad_radios">
                            <?php
                            $sql = "SELECT * FROM categorias;";
                            $query = pg_query($con, $sql);
                            while ($array = pg_fetch_array($query)) {
                                ?>

                                <input type="radio" name="cat_prod" id="cat<?php echo $array['id_cat'] ?>" value="<?php echo $array['id_cat'] ?>" />
                                <label for="cat<?php echo $array['id_cat'] ?>"><?php echo $array['nome'] ?></label>

                            <?php
                            } ?>
                        </div>

                        <div><p>Imagem:</p></div>
                        <div class="cad_input">
                            <input type="file" name="image" id="image">
                        </div>

                        <div><input type="submit" value="adicionar" name="submit"></div>
                    </form>
                </div>
                
            </div>

            <?php require_once("footer.php"); ?>

            <!-- JS -->
            <script type="text/javascript" src="scripts/main.js"></script>
        </div>
    </body>
</html>
