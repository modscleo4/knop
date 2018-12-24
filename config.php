<?php

require_once("credentials/credentials.php");

$con = pg_connect("host=$SRV_HOST port=$SRV_PORT user=$SRV_USERNAME password=$SRV_PASSWORD dbname=$SRV_DB");

if ($con === false) {
    die("ERRO: impossível conectar. " . pg_last_error());
}
