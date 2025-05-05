<?php

$DB_HOST = 'sql300.infinityfree.com';
$DB_USER = 'if0_38909992';
$DB_PORT = 3306;
$DB_PASS = 'Darts66540';
$DB_NAME = 'if0_38909992_darts';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME,$DB_PORT);

if ($mysqli->connect_error) {
    die("Échec de la connexion à la base de données : " . $mysqli->connect_error);
}else {    
}
?>
