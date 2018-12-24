<?php
session_start();

ob_start();

require_once("config.php");

if (isset($_SESSION['userid'])) {
	$userid = $_SESSION['userid'];
	$isAdmin = $_SESSION['isAdmin'];
}

if ($_POST['query'] == "getAddress") {
	$addressId = htmlspecialchars($_POST['addressId']);
	$sql = "SELECT * FROM endereco WHERE id_endereco = $addressId;";
    $query = pg_query($con, $sql);
	/*if ($isAdmin) {
        $query = pg_query($con, $sql);
	} else {
		$query = pg_query($public_con, $sql);
	}*/
	$array = pg_fetch_array($query);
	for ($i = 0; $i < pg_num_fields($query); $i++) {
		unset($array[$i]);
	}
	print_r(json_encode($array));
}
