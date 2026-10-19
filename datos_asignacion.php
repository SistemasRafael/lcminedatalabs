<?php 
include "connections/config.php";

$u_id = $_SESSION['u_id'];
$metodo_id = $_POST['metodo_id'];
$fase_id = $_POST['fase_id'];
$orden = $_POST['orden'];
    
if (isset($u_id)){
    $query = "INSERT INTO arg_metodos_fases (metodo_id, orden, fase_id, u_id)
            VALUES ($metodo_id, $orden, $fase_id, $u_id)";
    $mysqli->query($query);
    $resultado = $mysqli->query("SELECT
                                metodo_id
                                FROM 
                                arg_metodos_fases
                                WHERE metodo_id = ".$metodo_id." AND fase_id = ".$fase_id) or die(mysqli_error($mysqli));

    if ($resultado->num_rows > 0) {
        $html = 'Se registro exitosamente.';
    }
    else{
        $html = 'Hubo un error, reintente por favor.';
    }
}

$mysqli -> set_charset("utf8");
echo utf8_encode($html);
?>