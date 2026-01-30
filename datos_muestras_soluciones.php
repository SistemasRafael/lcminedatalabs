<? include "connections/config.php"; ?>
<?php

$unidad_id = $_POST['unidad_mina'];
$area = $_POST['area'];
$string = "AND area_id = $area";

if ($area == 0){
    $string = "";
}

$datos_bancos_detalle = $mysqli->query(
    "SELECT ba.*
              FROM `arg_ordenes_muestrasSoluciones` ba 
              LEFT JOIN arg_empr_unidades un 
              ON un.unidad_id = ba.unidad_id WHERE ba.unidad_id = $unidad_id ".$string
) or die(mysqli_error($mysqli));
$html_det = "";
$area_nombre = [
    1 => "Planta",
    2 => "Metalurgia",
    3 => "Metalurgia Mineral",
];
$datos_unidades = $mysqli->query(
    "SELECT nombre FROM arg_empr_unidades WHERE unidad_id = " . $unidad_id
) or die(mysqli_error($mysqli));

$nombre_uni = $datos_unidades->fetch_assoc();
$nombre = $nombre_uni['nombre'];
while ($fila = $datos_bancos_detalle->fetch_assoc()) {
    $texto = "";
    $css = "";
    if ($fila['activo'] == '1') {
        $texto = "Desactivar";
        $css = "btn-secondary";
        $estado = 0;
    } else {
        $texto = "Activar";
        $css = "btn-primary";
        $estado = 1;
    }
    $num = 1;
    $html_det .= "<tr>";
    $html_det .= "<td>" . $nombre . "</td>";
    $html_det .= "<td>" . $fila['folio'] . "</td>";
    $html_det .= "<td>" . $area_nombre[$fila['area_id']] . "</td>";    
    $html_det .= "<td>" . $fila['ciclica'] . "</td>";
    $html_det .= "<td> <button type='button' class='btn $css'";
    $html_det .= "onclick = setActivo(" . $fila['id'] . "," . $estado . ")";
    $html_det .= "><span> $texto </span>
                                                  </button>
                                            </td>";
    if ($fila['area_id'] == 1) {
        $html_det .= "<td> <input id='orden_box' class='form-control' type='text' value='" . $fila['orden'] . "'></input></td>";
        $html_det .= "<td> <button type='button' class='btn btn-info'";
        $html_det .= "onclick = cambiarOrden(" . $fila['id'] . ")";
        $html_det .= "><span> Cambiar </span>
                                                  </button>
                                            </td>";
        $html_det .= "</tr>";
    }else{
        $html_det .= "<td> ". $fila['orden'] . "</td>";
        $html_det .= "<td> <button type='button' class='btn btn-secondary' disabled style='pointer-eventes:none'";
        $html_det .= "onclick = cambiarOrden(" . $fila['id'] . ")";
        $html_det .= "><span> Cambiar </span>
                                                  </button>
                                            </td>";
        $html_det .= "</tr>";
    }
}

echo $html_det;
