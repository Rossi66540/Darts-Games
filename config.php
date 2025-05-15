<?php

$DB_HOST = 'localhost';
$DB_USER = 'fvasnmcf_root';
// $DB_PORT = 3306;
$DB_PASS = 'Darts66540!';
$DB_NAME = 'fvasnmcf_darts-games';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);


if ($mysqli->connect_error) {    
    die("Échec de la connexion à la base de données : " . $mysqli->connect_error);
}else {    
}
?>
