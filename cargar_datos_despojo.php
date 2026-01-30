<?php

include "connections/config.php";
if (isset($_POST['fecha'])){
    $fecha = $_POST['fecha'];
} else {
    $fecha = date("Y-m-d");
}

$hora = date("H:i:s");
$fecha_hora = $fecha;
$fecha_hora2 = $_POST['fecha'] + " 00:00:00";
$u_id = $_POST['u_id'];
$unidad_id = $_POST['unidad_id'];
$check_select_query = $mysqli->query("SELECT DISTINCT o.folio, d.circuito_id, d.estado_id 
                                      FROM arg_ordenes_plantas o
                                      INNER JOIN arg_ordenes_detallePlantas d 
                                        ON o.trn_id = d.trn_id_rel 
                                      WHERE 
                                         d.unidad_id = $unidad_id
                                         AND o.fecha = '$fecha_hora'
                                      ORDER BY d.circuito_id");
$return = [];
if (mysqli_num_rows($check_select_query) != 0) {
    $num = 1;
    while ($col = $check_select_query->fetch_assoc()) {
        $folio = $col['folio'];
        $circuito = $col['circuito_id'];
        $activo = $col['estado_id'];

        $return["folio".$circuito] = $folio;
        $return["circuito".$circuito] = $circuito;
        $return["activo".$circuito] = $activo;
        $num++;
    }

    echo json_encode($return);
} else {
    echo $fecha;
}