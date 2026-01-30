<?php
session_start();
$mysqli = new mysqli('localhost', 'root', 'Axioma$3112$', 'arg_minedata');
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
$mysqli->set_charset("utf8"); }
?>