<?php

/*
 * @todo Adicionar uma informação solicitando ao usuário para que o mesmo insira seus dados pessoais
 * @todo Melhorar o estilo visual desta página (principalmente da aba endereços)
 * @todo Fazer os devidos testes
 * */

session_start();

ob_start();

require_once("config.php");

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
}

$userid = $_SESSION['userid'];
$isAdmin = $_SESSION['isAdmin'];

if ($isAdmin) {
    if (isset($_GET['userid'])) {
        $userid = $_GET['userid'];
    } else if ($_POST['userid']) {
        $userid = $_POST['userid'];
    }
}

$emailError = "";
$loginError = "";
$dateError = "";
$phoneError = "";
$cellphoneError = "";

if (isset($_POST['tabLogin'])) {
    $email = htmlspecialchars($_POST['email']);
    $login = htmlspecialchars($_POST['login']);

    $sql = "UPDATE usuario SET email = '$email', login = '$login' WHERE id_usuario = $userid;";
    $query = pg_query($con, $sql);
} else if (isset($_POST['tabPasswords'])) {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password != $password_confirm) {
        $passwordError = "As senhas não coincidem!";
    } else {
        /* Criptografa a senha */
        $password = md5($password);

        $sql = "UPDATE usuario SET senha = '$password' WHERE id_usuario = $userid;";
        $query = pg_query($con, $sql);
    }
} else if (isset($_POST['tabUserdata'])) {
    $nome = htmlspecialchars($_POST['nome']);
    $sobrenome = htmlspecialchars($_POST['sobrenome']);
    $sexo = htmlspecialchars($_POST['sexo']);
    $dt_nasc = htmlspecialchars($_POST['dt_nasc']);
    $telefone = htmlspecialchars($_POST['telefone']);
    $celular = htmlspecialchars($_POST['celular']);

    $sql = "SELECT * FROM cliente WHERE id_usuario = $userid;";
    $query = pg_query($con, $sql);

    if (pg_num_rows($query) > 0) {
        $sql = "UPDATE cliente SET nome = '$nome', sobrenome = '$sobrenome', sexo = '$sexo', data_nasc = '$dt_nasc', telefone = '$telefone', celular = '$celular' WHERE id_usuario = $userid;";
    } else {
        $sql = "INSERT INTO cliente VALUES ($userid, '$nome', '$sobrenome', '$sexo', '$dt_nasc', '$telefone', '$celular', 'n', NULL)";
    }

    $query = pg_query($con, $sql);
} else if (isset($_POST['tabAddress'])) {
    $addressId = htmlspecialchars($_POST['addressId']);
    $isNew = htmlspecialchars($_POST['isNew']);
    $delete = htmlspecialchars($_POST['delete']);

    if ($delete == 't') {
        $data = date('Y-m-d');

        $sql = "UPDATE endereco SET excluido = 's', data_exclusao = '$data' WHERE id_endereco = $addressId AND id_usuario = $userid;";
        $query = pg_query($con, $sql);
    } else {
        $cep = htmlspecialchars($_POST['cep']);
        $rua = htmlspecialchars($_POST['rua']);
        $numero = htmlspecialchars($_POST['numero']);
        $complemento = htmlspecialchars($_POST['complemento']);
        $bairro = htmlspecialchars($_POST['bairro']);
        $cidade = htmlspecialchars($_POST['cidade']);
        $uf = htmlspecialchars($_POST['uf']);
        $pais = htmlspecialchars($_POST['pais']);

        if ($isNew == 't') {
            $sql = "INSERT INTO endereco VALUES (DEFAULT, $userid, '$rua', '$numero', '$complemento', '$bairro', '$cep', '$cidade', '$uf', '$pais', 'n', NULL);";
        } else {
            $sql = "UPDATE endereco SET endereco = '$rua', numero = '$numero', complemento = '$complemento', bairro = '$bairro', cep = '$cep', cidade = '$cidade', estado = '$uf', pais = '$pais' WHERE id_endereco = $addressId AND id_usuario = $userid;";
        }

        $query = pg_query($con, $sql);
    }
} else if (isset($_POST['tabExcluir'])) {
    $password = $_POST['password'];
    $uid = $_SESSION['userid'];
    $sql = "SELECT senha FROM usuario WHERE id_usuario = $uid AND excluido = 'n';";
    $query = pg_query($con, $sql);
    $array = pg_fetch_array($query);

    if (md5($password) == $array['senha']) {
        $data = date("Y-m-d");
        $sql = "UPDATE usuario SET excluido = 's', data_exclusao = '$data' WHERE id_usuario = $userid;";
        $query = pg_query($con, $sql);
        header("Location: logout.php");
    } else {
        $passwordDError = "Senha incorreta";
    }
}

$query = null;

$query[0] = pg_query($con, "SELECT * FROM usuario WHERE id_usuario = $userid");
$query[1] = pg_query($con, "SELECT * FROM cliente WHERE id_usuario = $userid");
$query[2] = pg_query($con, "SELECT * FROM endereco WHERE id_usuario = $userid AND excluido = 'n'");

$array[0] = pg_fetch_array($query[0]);
$array[1] = pg_fetch_array($query[1]);

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Sua conta | Knop</title>

        <!-- Icon -->
        <link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

        <!-- CSS -->
        <link href="styles/main.css" type="text/css" rel="stylesheet"/>
        <link href="styles/account.css" type="text/css" rel="stylesheet"/>

        <!--External CSS-->
        <link href="styles/external/jquery-ui.min.css" type="text/css" rel="stylesheet"/>

        <!-- Meta -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset="UTF-8"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
    </head>

    <body>
        <!-- JS Lib -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="scripts/jquery-ui.min.js"></script>
        <script src="scripts/jquery.mask.min.js"></script>

        <div id="main" class="main">

            <?php require_once("navbar.php"); ?>

            <div id="content">
                <div id="mvl">
                    <div id="logo">
                        <img id="logo_img" src="res/logo.svg" alt="">
                    </div>

                    <div id="fields">
                        <form id="frmLogin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="text" style="display: none;" name="userid" value="<?php echo $_GET['userid']; ?>" />

                            <div id="header">
                                <div><h2>Login/nome de usuário</h2></div>
                                <div id="btnSend"><input id="btnLogin" class="buttonSend" type="submit" name="tabLogin" value="Salvar" /></div>
                            </div>

                            <div id="dadosLogin">
                                <div id="email" class="div_fields">
                                    <div class="field">
                                        <input class="textInput" type="email" name="email" id="txtEmail" value="<?php echo $array[0]['email']; ?>" required maxlength="40" /><br/>
                                        <label for="txtEmail">Email</label>
                                        <p <?php if ($emailError == "") { ?>style="display: none;" <?php } ?>><?php echo $emailError; ?></p>
                                    </div>
                                </div>

                                <div id="login" class="div_fields">
                                    <div class="field">
                                        <input class="textInput" type="text" name="login" id="txtLogin" value="<?php echo $array[0]['login']; ?>" required maxlength="40" /><br/>
                                        <label for="txtLogin">Login</label>
                                        <p <?php if ($loginError == "") { ?>style="display: none;" <?php } ?>><?php echo $emailError; ?></p>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form id="frmSenhas" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="text" style="display: none;" name="userid" value="<?php echo $_GET['userid']; ?>" />

                            <div id="header">
                                <div><h2>Senhas</h2></div>
                                <div id="btnSend"><input id="btnPasswords" class="buttonSend" type="submit" name="tabPasswords" value="Salvar"/></div>
                            </div>

                            <div id="dadosSenhas">
                                <div id="passwords" class="div_fields">
                                    <div id="password" class="field">
                                        <input class="textInput" type="password" name="password" id="txtPassword" required maxlength="16" /><br/>
                                        <label for="txtPassword">Senha</label>
                                        <p <?php if ($passwordError == "") { ?>style="display: none;" <?php } ?>><?php echo $passwordError; ?></p>
                                    </div>

                                    <div id="password_confirm" class="field">
                                        <input class="textInput" type="password" name="password_confirm" id="txtPassword_confirm" required maxlength="16" /><br/>
                                        <label for="txtPassword_confirm">Confirmar senha</label>
                                        <p <?php if ($passwordError == "") { ?>style="display: none;" <?php } ?>><?php echo $passwordError; ?></p>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form id="frmDP" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="text" style="display: none;" name="userid" value="<?php echo $_GET['userid']; ?>" />

                            <div id="header">
                                <div><h2>Dados pessoais</h2></div>
                                <div id="btnSend"><input id="btnUserdata" class="buttonSend" type="submit" name="tabUserdata"
                                                         value="Salvar"/></div>
                            </div>

                            <div id="dadosPessoais">
                                <div id="ns" class="div_fields">
                                    <div id="nome" class="field">
                                        <input class="textInput" type="text" name="nome" id="txtNome" value="<?php echo $array[1]['nome']; ?>" required maxlength="30" /><br/>
                                        <label for="txtNome">Nome</label>
                                    </div>

                                    <div id="sobrenome" class="field">
                                        <input class="textInput" type="text" name="sobrenome" id="txtSobrenome" value="<?php echo $array[1]['sobrenome']; ?>" required maxlength="40" /><br/>
                                        <label for="txtSobrenome">Sobrenome</label>
                                    </div>
                                </div>

                                <div id="sexo" class="div_fields">
                                    <div class="field">
                                        <div id="sexos">
                                            <div>
                                                <input id="radMasc" type="radio" name="sexo" value="m" <?php if ($array[1]['sexo'] == 'm') { ?>checked<?php } ?> />
                                                <label for="radMasc">Masculino</label>
                                            </div>

                                            <div>
                                                <input id="radFem" type="radio" name="sexo" value="f" <?php if ($array[1]['sexo'] == 'f') { ?>checked<?php } ?> />
                                                <label for="radFem">Feminino</label>
                                            </div>
                                        </div>

                                        <label id="lblSexo" for="sexo">Sexo</label>
                                    </div>
                                </div>

                                <div id="data" class="div_fields">
                                    <div class="field">
                                        <input class="dateInput" type="date" name="dt_nasc" id="txtData" value="<?php echo $array[1]['data_nasc']; ?>" required /><br/>
                                        <label id="lblData" for="txtData">Data de nascimento</label>
                                        <p <?php if ($dateError == "") { ?>style="display: none;" <?php } ?>><?php echo $dateError; ?></p>
                                    </div>
                                </div>

                                <div id="tc" class="div_fields">
                                    <div id="telefone" class="field">
                                        <input class="textInput" type="text" name="telefone" id="numTelefone" value="<?php echo $array[1]['telefone']; ?>" required maxlength="14" /><br/>
                                        <label for="numTelefone">Telefone</label>
                                        <p <?php if ($phoneError == "") { ?>style="display: none;" <?php } ?>><?php echo $phoneError; ?></p>
                                    </div>

                                    <div id="celular" class="field">
                                        <input class="textInput" type="text" name="celular" id="numCelular" value="<?php echo $array[1]['celular']; ?>" required maxlength="14" /><br/>
                                        <label for="numCelular">Celular</label>
                                        <p <?php if ($cellphoneError == "") { ?>style="display: none;" <?php } ?>><?php echo $cellphoneError; ?></p>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form id="frmEndereco" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="text" style="display: none;" name="userid" value="<?php echo $_GET['userid']; ?>" />

                            <div id="header">
                                <div><h2>Endereços</h2></div>
                                <div id="btnSend"><input id="btnAddress" class="buttonSend" type="submit" name="tabAddress" value="Salvar" /></div>
                            </div>

                            <div id="dadosEndereco">
                                <div id="multAddr">
                                    <?php if (pg_num_rows($query[2]) > 0) { ?>

                                        <input id="radExisting" type="radio" name="isNew" value="f" />
                                        <label for="radExisting">Existente:</label>

                                    <?php } ?>

                                    <?php
                                    while ($array[2] = pg_fetch_array($query[2])) {
                                        ?>

                                        <div class="address">
                                            <input id="addr<?php echo $array[2]['id_endereco']; ?>" class="radExistingAddress" type="radio" name="addressId" value="<?php echo $array[2]['id_endereco']; ?>" />
                                            <label for="addr<?php echo $array[2]['id_endereco']; ?>"><?php echo $array[2]['endereco']; ?></label>

                                            <div class="options">
                                                <input id="radEdit" type="radio" name="delete" value="f" checked />
                                                <label for="radEdit">Editar</label>
                                                <input id="radDelete" type="radio" name="delete" value="t" />
                                                <label for="radDelete">Excluir</label>
                                            </div>
                                        </div>

                                        <?php
                                    }
                                    ?>

                                    <input id="radNew" type="radio" name="isNew" value="t" checked>
                                    <label for="radNew">Novo:</label>
                                </div>

                                <div id="cep" class="div_fields">
                                    <div class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="cep" id="txtCep" required maxlength="10" /><br/>
                                        <label for="txtCep">CEP</label>
                                        <p style="display: none;">CEP não encontrado!</p>
                                    </div>
                                </div>

                                <div id="rn" class="div_fields">
                                    <div id="rua" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="rua" id="txtRua" required maxlength="255" /><br/>
                                        <label for="txtRua">Rua</label>
                                        <p style="display: none;"></p>
                                    </div>

                                    <div id="n" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="numero" id="txtNumero" required maxlength="10" /><br/>
                                        <label for="txtNumero">Número</label>
                                        <p style="display: none;"></p>
                                    </div>
                                </div>

                                <div id="complemento" class="div_fields">
                                    <div class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="complemento" id="txtComplemento" maxlength="40" /><br/>
                                        <label for="txtComplemento">Complemento</label>
                                        <p style="display: none;"></p>
                                    </div>
                                </div>

                                <div id="bairro" class="div_fields">
                                    <div class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="bairro" id="txtBairro" required maxlength="255" /><br/>
                                        <label for="txtBairro">Bairro</label>
                                        <p style="display: none;"></p>
                                    </div>
                                </div>

                                <div id="cup" class="div_fields">
                                    <div id="cidade" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="cidade" id="txtCidade" required maxlength="40" /><br/>
                                        <label for="txtCidade">Cidade</label>
                                        <p style="display: none;"></p>
                                    </div>

                                    <div id="uf" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="uf" id="txtUF" required maxlength="2" /><br/>
                                        <label for="txtUF">UF</label>
                                        <p style="display: none;"></p>
                                    </div>

                                    <div id="pais" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                        <input class="textInput" type="text" name="pais" id="txtPais" required maxlength="30" value="Brasil" /><br/>
                                        <label for="txtPais">País</label>
                                        <p style="display: none;"></p>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <form id="frmDelete" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="text" style="display: none;" name="userid" value="<?php echo $_GET['userid']; ?>" />

                            <div id="header">
                                <div><h2>Excluir conta</h2></div>
                                <div id="btnSend"><input id="btnExcluir" class="buttonSend" type="submit" name="tabExcluir" value="Confirmar" /></div>
                            </div>

                            <div id="dadosExcluir">
                                <div class="div_fields">
                                    <div id="password" class="field">
                                        <input class="textInput" type="password" name="password" id="txtPassword" required maxlength="16" /><br/>
                                        <label for="txtPassword">Senha</label>
                                        <p <?php if ($passwordDError == "") { ?>style="display: none;" <?php } ?>><?php echo $passwordDError; ?></p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php require_once("footer.php"); ?>

        </div>

        <!-- JS -->
        <script type="text/javascript" src="scripts/main.js"></script>
        <script type="text/javascript" src="scripts/account.js"></script>
    </body>
</html>

<?php ob_end_flush(); ?>
