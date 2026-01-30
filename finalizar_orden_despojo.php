<?php

include "connections/config.php";

$trn_id_rel = $_POST['trn_id_rel'];
$circuito = $_POST['circuito'];
$u_id = $_POST['u_id']; 

$query = "CALL arg_prc_finalizarOrdenPlanta ($trn_id_rel, $circuito, $u_id)";
mysqli_multi_query($mysqli, $query);

$check_query = $mysqli->query("SELECT estado_id FROM arg_ordenes_plantas WHERE usuario_id = $u_id AND trn_id_rel = $trn_id_rel") or die(mysqli_error($mysqli));

if (mysqli_num_rows($check_query) != 0){
    $check = $check_query->fetch_assoc();
    $estado_id = $check['estado_id'];
    if ($estado_id == 1){
        echo "Registro finalizado correctamente.";
    } else {
        echo "Ocurrió un error, contacte a un administrador.";
    }
} else {
    echo "Error, por favor contacte a un administrador.";
}