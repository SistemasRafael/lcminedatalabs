<?php

include "connections/config.php";


$folio = $_POST["folio"];
$circuito = $_POST["circuito"];

$query = $mysqli->query("SELECT DISTINCT * FROM `arg_ordenes_despojos` o INNER JOIN arg_ordenes_recuperacion r ON o.trn_id_rel=r.trn_id_rel WHERE o.trn_id_rel = $folio AND o.trn_id = r.trn_id ORDER BY o.hora;");
$html = '';
//<input type="text" class="form-control" readonly="readonly" style="width:100%" value="'.$col['hora'].'">
while ($col = $query->fetch_assoc()) {
    $html .= '<tr data-id="' . $col['trn_id'] . '"data-circ="' . $circuito . '">
                <td><select class="form-control" name="hora_sel" readonly="readonly" id="hora_sel"><option selected value="'. $col['hora'] .'">'. $col['hora'] .'</option></select></td>
                <td><input type="text" class="form-control" style="width:100%" value="' . $col['m3_hr'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%" value="' . $col['totalizador'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['strip_presion_ent'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['strip_presion_sal'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['caldera_temp'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['caldeta_temp_ent'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['caldeta_temp_sal'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['celda_temp'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['interc_presion_ent'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['interc_presion_sal'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['rect1_volt'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['rect1_amp'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['rect2_volt'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['rect2_amp'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['rect3_volt'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['rect3_amp'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['sosa_ppm'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['nacn_ppm'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['ph_eluyente'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['abs_au_cabeza'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['abs_au_cola'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['abs_ag_cabeza'] . '"></td>
                <td><input type="text" class="form-control" style="width:100%"value="' . $col['abs_ag_cola'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['au_oz'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['au_acum'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['ag_oz'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['ag_acum'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['ag/au'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['au'] . '"></td>
                <td><input type="text" class="form-control" readonly="readonly" style="width:100%" value="' . $col['ag'] . '"></td>
                <td><button class="btn btn-primary" onclick="guardarFila(this)"><i class="fa fa-save"></i></button></td>
            </tr>';
}
echo $html;
