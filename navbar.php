<?php

/*
 * @todo Adicionar outro texto caso o usuário ainda não cadastrou seus dados pessoais (tabela cliente)
 * */

require_once("config.php");

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
}

$sql_nome = "SELECT login FROM usuario WHERE id_usuario = $userid;";
$query_nome = pg_query($con, $sql_nome);
$array_nome = pg_fetch_array($query_nome);

?>

<div id="navbar" class="navbar">
	<div id="content-left">
		<a name="top"></a>
		<span id="logo">
			<img id="logo-img" src="res/logo.svg"/>
		</span>

		<span id="link_home" class="dropdown">
			<span><a href="index.php">Home</a></span>
		</span>

		<span id="link_produtos" class="dropdown">
			<span><a href="catalogo.php">Produtos</a></span>
			<div class="dropdown-content">
				<a href="catalogo.php?botons">Botons</a>
				<a href="catalogo.php?chaveiros">Chaveiros</a>
				<a href="catalogo.php?cartoes">Cartões</a>
			</div>
		</span>

		<span id="link_sobre" class="dropdown">
			<span>Sobre</span>
			<div class="dropdown-content">
				<a href="aboutUs.php">Sobre nós</a>
				<a href="knop.php">Sobre a Knop</a>
			</div>
		</span>

		<?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) { ?>

        <span id="link_admin" class="dropdown">
			<span>Admin</span>
			<div class="dropdown-content">
				<a href="clientes.php">Consulta de clientes</a>
				<a href="vendas.php">Consulta de vendas</a>
				<a href="produtos.php">Consulta de produtos</a>
			</div>
		</span>

		<?php } ?>

		<button onclick="changeTheme();" id="theme-changer"></button>

	</div>

	<div id="content-center">
		<form name="search" id="search" action="catalogo.php" method="get">
			<input type="text" id="search-bar" name="q" placeholder="Pesquisar" value="<?php echo $_GET['q']; ?>" />
			<span id="search-icon">
				<img id="search-icon-img" src="res/search.svg"/>
			</span>
		</form>
	</div>

	<div id="content-right">
		<a href="cart.php" id="cart">
			<img id="cart-img" src="res/cart.svg"/>
		</a>

		<?php if (!isset($_SESSION['userid'])) { ?>
			
			<span class="dropdown link_login">
				<a href="login.php" id="user">Entrar</a>
			</span>

		<?php } else { ?>
			
			<span id="link_user" class="dropdown link_user">
				<span><?php echo $array_nome['login']; ?></span>
				<div class="dropdown-content">
                    <a href="compras.php">Histórico de compras</a>
					<a href="account.php">Alterar Dados</a>
					<a href="logout.php" id="user">Sair</a>
				</div>
			</span>

		<?php } ?>
	</div>
</div>