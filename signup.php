<?php

/*
 * @todo Adicionar uma informação solicitando ao usuário para que o mesmo insira seus dados pessoais
 * @todo Melhorar o estilo visual desta página (principalmente da aba endereços)
 * @todo Fazer os devidos testes
 * */

session_start();

ob_start();

require_once("credentials/credentials.php");
require_once("config.php");

require_once("phpmailer/class.phpmailer.php");
require_once("phpmailer/class.smtp.php");

/**
 * Rotina para enviar email de confirmação
 * @param $email : O email desejado
 * @return true caso o email for autorizado para envio
 */
function sendmail($email) {
    $sent = false;

    $c = hash("sha256", $email);

    $mail = new PHPMailer(true);
    try {
        $mail->Host = "smtp.gmail.com";
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        $mail->Username = $GM_USERNAME;
        $mail->Password = $GM_PASSWORD;

        $mail->SetFrom("marcosviniciuslira05@gmail.com", "Marcos Vinicius Lira");
        $mail->AddReplyTo("marcosviniciuslira05@gmail.com", "Marcos Vinicius Lira");
        $mail->Subject = "Confirmar email";

        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";
        $mail->AddAddress($email);
        $mail->IsHTML(true);
        $mail->Body = '<html>
	<head>
		<style>
			
		</style>
	</head>
	<body>
		<h1></h1>
		<p><a href="http://200.145.153.175/dhiegobarbosa/anual/confirmEmail.php?email=' . $email . '&c=' . $c . '">Clique aqui</a> para confirmar esse email.</p>
		<p>Ou cole o código abaixo na barra de endereços do seu navegador: http://200.145.153.175/dhiegobarbosa/anual/confirmEmail.php?email=' . $email . '&c=' . $c . '</p>
	</body>
</html>';

        $sent = $mail->Send();
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
    } catch (phpmailerException $e) {
        $sent = false;
        //echo $e->getMessage();
    }

    return $sent;
}

/* Caso seja solicitado por Ajax o reenvio do email, o código if abaixo será executado */
if (isset($_POST['resend'])) {
    $email = $_POST['resend'];

    if (sendmail($email)) {
        echo "Sent";
        exit;
    }
}

$emailError = "";
$loginError = "";
$dateError = "";
$phoneError = "";
$cellphoneError = "";

if (isset($_POST['send'])) {
    $email = htmlspecialchars($_POST['email']);
    $login = htmlspecialchars($_POST['login']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password != $password_confirm) {
        $passwordError = "As senhas não coincidem!";
    } else {
        $sql = "SELECT email, login, id_usuario, excluido FROM usuario WHERE (email = '$email' OR login = '$login') AND excluido = 's';";
        $query = pg_query($con, $sql);

        if (pg_num_rows($query) == 0) {
            /* O email e login informados não foram cadastrados */

            /* Criptografa a senha */
            $password = md5($password);

            if (sendmail($email)) {
                /* O email foi autorizado para envio */
                $_SESSION['confirmEmail'] = $email;

                /* Insere os dados no banco */
                $sql = "INSERT INTO usuario VALUES (DEFAULT, '$login', '$email', '$password', DEFAULT, NULL, FALSE, FALSE) RETURNING id_usuario;";
                $query = pg_query($con, $sql);
                $userid = pg_fetch_array($query)['id_usuario'];

                // Dados pessoais
                $nome = htmlspecialchars($_POST['nome']);
                $sobrenome = htmlspecialchars($_POST['sobrenome']);
                $sexo = htmlspecialchars($_POST['sexo']);
                $dt_nasc = htmlspecialchars($_POST['dt_nasc']);
                $telefone = htmlspecialchars($_POST['telefone']);
                $celular = htmlspecialchars($_POST['celular']);

                $sql = "INSERT INTO cliente VALUES ($userid, '$nome', '$sobrenome', '$sexo', '$dt_nasc', '$telefone', '$celular', 'n', NULL)";
                $query = pg_query($con, $sql);

                // Endereço
                $cep = htmlspecialchars($_POST['cep']);
                $rua = htmlspecialchars($_POST['rua']);
                $numero = htmlspecialchars($_POST['numero']);
                $complemento = htmlspecialchars($_POST['complemento']);
                $bairro = htmlspecialchars($_POST['bairro']);
                $cidade = htmlspecialchars($_POST['cidade']);
                $uf = htmlspecialchars($_POST['uf']);
                $pais = htmlspecialchars($_POST['pais']);

                $sql = "INSERT INTO endereco VALUES (DEFAULT, $userid, '$rua', '$numero', '$complemento', '$bairro', '$cep', '$cidade', '$uf', '$pais', 'n', NULL);";
                $query = pg_query($con, $sql);

                header("Location: login.php");
            } else {
                $emailError = "Endereço de email inválido";
            }
        } else {
            $array = pg_fetch_array($query);

            if ($array['excluido'] == 's') {
                if ($password != $password_confirm) {
                    $passwordError = "As senhas não coincidem!";
                } else {
                    $password = md5($password);
                    $uemail = $array['email'];
                    $sql = "UPDATE usuario SET excluido = 'n', senha = '$password' WHERE email = '$uemail'";
                    $query = pg_query($con, $sql);
                    header("Location: login.php?m=1");
                }

            }

            if ($email == $array['email']) {
                $emailError = "Já existe um cadastro com esse email.";
            }

            if ($login == $array['login']) {
                $loginError = "Já existe um cadastro com esse nome de usuário.";
            }
        }
    }

    $query = null;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Finalize sua conta | Knop</title>

        <!-- Icon -->
        <link href="res/favicon.png" type="image/png" rel="shortcut icon"/>

        <!-- CSS -->
        <link href="styles/main.css" type="text/css" rel="stylesheet"/>
        <link href="styles/signup.css" type="text/css" rel="stylesheet"/>

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
                <div id="a">
                    <div id="info">
                        <p>Com uma conta Knop, você pode:</p>
                        <ul>
                            <li>Utilizar o carrinho;</li>
                            <li>Realizar compras;</li>
                            <li>Usar o cadastro único.</li>
                        </ul>

                        <p id="req">Os campos com * são obrigatórios</p>
                    </div>

                    <div id="mvl">
                        <div id="logo">
                            <img id="logo_img" src="res/logo.svg" alt="">
                        </div>

                        <div id="fields">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <div id="header">
                                    <div><h2>Login/nome de usuário</h2></div>
                                    <div>Esses são os dados necessários para você fazer o login no nosso sistema</div>
                                </div>

                                <div id="dadosLogin">
                                    <div id="email" class="div_fields">
                                        <div class="field">
                                            <input class="textInput input" type="email" name="email" id="txtEmail" required maxlength="40" /><br/>
                                            <label for="txtEmail">* Email</label>
                                            <p <?php if ($emailError == "") { ?>style="display: none;" <?php } ?>><?php echo $emailError; ?></p>
                                        </div>
                                    </div>

                                    <div id="login" class="div_fields">
                                        <div class="field">
                                            <input class="textInput input" type="text" name="login" id="txtLogin" required maxlength="40" /><br/>
                                            <label for="txtLogin">* Login</label>
                                            <p <?php if ($loginError == "") { ?>style="display: none;" <?php } ?>><?php echo $loginError; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div id="header">
                                    <div><h2>Senhas</h2></div>
                                    <div>Junto com o email/nome de usuário, a senha é um requisito para o login no sistema. Utilize uma senha segura.</div>
                                </div>

                                <div id="dadosSenhas">
                                    <div id="passwords" class="div_fields">
                                        <div id="password" class="field">
                                            <input class="textInput input" type="password" name="password" id="txtPassword" required maxlength="16" /><br/>
                                            <label for="txtPassword">* Senha</label>
                                            <p <?php if ($passwordError == "") { ?>style="display: none;" <?php } ?>><?php echo $passwordError; ?></p>
                                        </div>

                                        <div id="password_confirm" class="field">
                                            <input class="textInput input" type="password" name="password_confirm" id="txtPassword_confirm" required maxlength="16" /><br/>
                                            <label for="txtPassword_confirm">* Confirmar senha</label>
                                            <p <?php if ($passwordError == "") { ?>style="display: none;" <?php } ?>><?php echo $passwordError; ?></p>
                                        </div>
                                    </div>
                                </div>


                                <div id="header">
                                    <div><h2>Dados pessoais</h2></div>
                                    <div>Esses são os seus dados pessoais</div>
                                </div>

                                <div id="dadosPessoais">
                                    <div id="ns" class="div_fields">
                                        <div id="nome" class="field">
                                            <input class="textInput input" type="text" name="nome" id="txtNome" required maxlength="30" /><br/>
                                            <label for="txtNome">* Nome</label>
                                        </div>

                                        <div id="sobrenome" class="field">
                                            <input class="textInput input" type="text" name="sobrenome" id="txtSobrenome" required maxlength="40" /><br/>
                                            <label for="txtSobrenome">* Sobrenome</label>
                                        </div>
                                    </div>

                                    <div id="sexo" class="div_fields">
                                        <div class="field">
                                            <div id="sexos" class="input">
                                                <div>
                                                    <input id="radMasc" type="radio" name="sexo" value="m" required />
                                                    <label for="radMasc">Masculino</label>
                                                </div>

                                                <div>
                                                    <input id="radFem" type="radio" name="sexo" value="f" />
                                                    <label for="radFem">Feminino</label>
                                                </div>
                                            </div>

                                            <label id="lblSexo" for="sexo">* Sexo</label>
                                        </div>
                                    </div>

                                    <div id="data" class="div_fields">
                                        <div class="field">
                                            <input class="dateInput input" type="date" name="dt_nasc" id="txtData" required /><br/>
                                            <label id="lblData" for="txtData">* Data de nascimento</label>
                                            <p <?php if ($dateError == "") { ?>style="display: none;" <?php } ?>><?php echo $dateError; ?></p>
                                        </div>
                                    </div>

                                    <div id="tc" class="div_fields">
                                        <div id="telefone" class="field">
                                            <input class="textInput input" type="text" name="telefone" id="numTelefone" maxlength="14" /><br/>
                                            <label for="numTelefone">Telefone</label>
                                            <p <?php if ($phoneError == "") { ?>style="display: none;" <?php } ?>><?php echo $phoneError; ?></p>
                                        </div>

                                        <div id="celular" class="field">
                                            <input class="textInput input" type="text" name="celular" id="numCelular" maxlength="14" /><br/>
                                            <label for="numCelular">Celular</label>
                                            <p <?php if ($cellphoneError == "") { ?>style="display: none;" <?php } ?>><?php echo $cellphoneError; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div id="header">
                                    <div><h2>Endereços</h2></div>
                                    <div>Insira seu endereço, colocando o CEP primeiro para os dados serem preenchidos automaticamente.</div>
                                </div>

                                <div id="dadosEndereco">
                                    <div id="cep" class="div_fields">
                                        <div class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="cep" id="txtCep" value="" required maxlength="10" /><br/>
                                            <label for="txtCep">* CEP</label>
                                            <p style="display: none;">CEP não encontrado!</p>
                                        </div>
                                    </div>

                                    <div id="rn" class="div_fields">
                                        <div id="rua" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="rua" id="txtRua" required maxlength="255" disabled /><br/>
                                            <label for="txtRua">* Rua</label>
                                            <p style="display: none;"></p>
                                        </div>

                                        <div id="n" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="numero" id="txtNumero" required maxlength="10" disabled /><br/>
                                            <label for="txtNumero">* Número</label>
                                            <p style="display: none;"></p>
                                        </div>
                                    </div>

                                    <div id="complemento" class="div_fields">
                                        <div class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="complemento" id="txtComplemento" maxlength="40" disabled /><br/>
                                            <label for="txtComplemento">Complemento</label>
                                            <p style="display: none;"></p>
                                        </div>
                                    </div>

                                    <div id="bairro" class="div_fields">
                                        <div class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="bairro" id="txtBairro" required maxlength="255" disabled /><br/>
                                            <label for="txtBairro">* Bairro</label>
                                            <p style="display: none;"></p>
                                        </div>
                                    </div>

                                    <div id="cup" class="div_fields">
                                        <div id="cidade" class="field input" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="cidade" id="txtCidade" required maxlength="40" disabled /><br/>
                                            <label for="txtCidade">* Cidade</label>
                                            <p style="display: none;"></p>
                                        </div>

                                        <div id="uf" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="uf" id="txtUF" required maxlength="2" disabled /><br/>
                                            <label for="txtUF">* UF</label>
                                            <p style="display: none;"></p>
                                        </div>

                                        <div id="pais" class="field" onclick="this.getElementsByTagName('p')[0].style.display = 'none';">
                                            <input class="textInput input" type="text" name="pais" id="txtPais" required maxlength="30" value="Brasil" readonly /><br/>
                                            <label for="txtPais">* País</label>
                                            <p style="display: none;"></p>
                                        </div>
                                    </div>
                                </div>

                                <input type="submit" class="buttonSend" name="send" value="Criar conta" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once("footer.php"); ?>

        </div>

        <!-- JS -->
        <script type="text/javascript" src="scripts/main.js"></script>
        <script type="text/javascript" src="scripts/signup.js"></script>
    </body>
</html>

<?php ob_end_flush(); ?>
