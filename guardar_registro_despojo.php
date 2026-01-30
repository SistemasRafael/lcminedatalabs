<?php

include "connections/config.php";
function valueOrDefault($array, $key, $default)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

$folio = 0;
$valor = $_POST["data"];
$trn_id = $_POST["trn_id"];

$valores = array_map(function ($value) {
    return $value === "" ? 0 : $value;
}, $valor);

$respuesta_id = $mysqli->query("SELECT MAX(trn_id) as trn_id
FROM  arg_ordenes_despojos");
$resp_id = $respuesta_id->fetch_assoc();
$resp = $resp_id['trn_id'];
$r_id = $resp + 1;

$num = 0;

if ($trn_id == 0) {
    $insert_query = "INSERT INTO `arg_ordenes_despojos` (`trn_id`, `trn_id_rel`, `hora`, `m3_hr`, `totalizador`, `strip_presion_ent`, `strip_presion_sal`, `caldera_temp`, `caldeta_temp_ent`, `caldeta_temp_sal`, `celda_temp`, `interc_presion_ent`, `interc_presion_sal`, `rect1_volt`, `rect1_amp`, `rect2_volt`, `rect2_amp`, `rect3_volt`, `rect3_amp`, `sosa_ppm`, `nacn_ppm`, `ph_eluyente`, `abs_au_cabeza`, `abs_au_cola`, `abs_ag_cabeza`, `abs_ag_cola`) VALUES ($r_id, " . $valores[$num++] . ", '" . $valores[$num++] . "' , " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . "," . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", " . $valores[$num++] . ", '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "')";
    mysqli_multi_query($mysqli, $insert_query);

    $insert_recup = "INSERT INTO `arg_ordenes_recuperacion` (`trn_id`, `trn_id_rel`, `au_oz`, `au_acum`, `ag_oz`, `ag_acum`, `ag/au`, `au`, `ag`) VALUES ($r_id, " . $valores[0] . ", '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "', '" . $valores[$num++] . "')";
    mysqli_multi_query($mysqli, $insert_recup);

    $check_query = $mysqli->query("SELECT trn_id FROM arg_ordenes_despojos WHERE trn_id = $r_id") or die(mysqli_error($mysqli));

    if (mysqli_num_rows($check_query) != 0) {
        echo "Registro guardado correctamente.";
    }
} else {
    $update_query = "UPDATE `arg_ordenes_despojos` SET `trn_id_rel` = '" . $valores[$num++] . "', `hora` = '" . $valores[$num++] . "', `m3_hr` = '" . $valores[$num++] . "', `totalizador` = '" . $valores[$num++] . "', `strip_presion_ent` = '" . $valores[$num++] . "', `strip_presion_sal` = '" . $valores[$num++] . "', `caldera_temp` = '" . $valores[$num++] . "', `caldeta_temp_ent` = '" . $valores[$num++] . "', `caldeta_temp_sal` = '" . $valores[$num++] . "', `celda_temp`= '" . $valores[$num++] . "', `interc_presion_ent` = '" . $valores[$num++] . "', `interc_presion_sal` = '" . $valores[$num++] . "', `rect1_volt` = '" . $valores[$num++] . "', `rect1_amp` = '" . $valores[$num++] . "', `rect2_volt` = '" . $valores[$num++] . "', `rect2_amp` = '" . $valores[$num++] . "', `rect3_volt` = '" . $valores[$num++] . "', `rect3_amp` = '" . $valores[$num++] . "', `sosa_ppm` = '" . $valores[$num++] . "', `nacn_ppm` = '" . $valores[$num++] . "', `ph_eluyente` = '" . $valores[$num++] . "', `abs_au_cabeza` = '" . $valores[$num++] . "', `abs_au_cola` = '" . $valores[$num++] . "', `abs_ag_cabeza` = '" . $valores[$num++] . "', `abs_ag_cola` = '" . $valores[$num++] . "' WHERE `arg_ordenes_despojos`.`trn_id` = $trn_id";
    mysqli_multi_query($mysqli, $update_query);
    
    $update_recup = "UPDATE `arg_ordenes_recuperacion` SET `trn_id_rel` = '" . $valores[0] . "', `au_oz` = '" . $valores[$num++] . "', `au_acum` = '" . $valores[$num++] . "', `ag_oz` = '" . $valores[$num++] . "', `ag_acum` = '" . $valores[$num++] . "', `ag/au` = '" . $valores[$num++] . "', `au` = '" . $valores[$num++] . "', `ag` = '" . $valores[$num++] . "' WHERE `arg_ordenes_recuperacion`.`trn_id` = $trn_id";
    mysqli_multi_query($mysqli, $update_recup);

    $check_query = $mysqli->query("SELECT trn_id FROM arg_ordenes_despojos WHERE trn_id = $trn_id") or die(mysqli_error($mysqli));

    if (mysqli_num_rows($check_query) != 0) {
        echo "Registro guardado correctamente.";
    }
}
