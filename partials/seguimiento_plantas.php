<? // include "../connections/config.php";
$unidad_id = $_GET['unidad_id'];
$fecha_i = $_GET['fecha_i'];
$fecha_f = $_GET['fecha_f'];
if (is_null($fecha_i)) {
    $fecha_i = date('Y-m-d', strtotime("-30 days")); //date('d/m/y');
}
if (is_null($fecha_f)) {
    $fecha_f = date('Y-m-d');
}

$_SESSION['unidad_id'] = $unidad_id;
$u_id = $_SESSION['u_id']
//echo $trn_id;
?>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style type="text/css">
    .izq {
        background-color: ;
    }

    .derecha {
        background-color: ;
    }

    .btnSubmit {
        width: 50%;
        border-radius: 1rem;
        padding: 1.5%;
        border: none;
        cursor: pointer;
    }

    .circulos {
        padding-top: 5em;
    }

    img {
        max-width: 100%;
    }

    /*
    table {
  position: relative;
  border-collapse: collapse; 
}

 
th {  
  position: sticky;
  top: 0; 
}*/
</style>

<script>
    function actualizar() {
        var unidad_id = document.getElementById('mina_secado').value;
        var unidad_id = document.getElementById('mina').value;
        var direccionar = '<? echo "\seguimiento_plantas.php?unidad_id=" ?>' + unidad_id;
        window.location.href = direccionar;
    }


    function iniciar_seg(trnid_batch, fecha, uid, unidadid) {
        var fecha_batch = fecha;
        var u_id = uid;
        //alert(fecha_batch);
        var trn_id_batch = trnid_batch;
        var unidad_id = unidadid // document.getElementById('mina_seleccionada').value;
        var direccionar = '<? echo "\ formato_despojo.php?unidad_id=" ?>' + unidad_id + '&fecha=' + "'" + fecha_batch + "'" + '&u_id=' + u_id;
        window.location.href = direccionar;

    }

    function actualizar_fechas() {
        var fecha_i = document.getElementById('fecha_inicial').value;
        var fecha_f = document.getElementById('fecha_final').value;
        var unid = <? echo $unidad_id; ?>;
        var exportars = '<?php echo "\ seguimiento_plantas.php?unidad_id=" ?>' + unid + '&fecha_i=' + fecha_i + '&fecha_f=' + fecha_f;
        window.location.href = exportars;
    }
</script>
<?php
if (isset($_GET['unidad_id'])) {
    $mysqli->set_charset("utf8");
    /* echo $fecha_i; 
    echo $fecha_f; */
    /* $datos_orden_detalle = $mysqli->query("SELECT distinct(ord.folio),
                                                        DATE_FORMAT(ord.fecha, '%Y-%m-%d') AS fecha,
                                                        ord.trn_id,
                                                        opl.trn_id AS trn_id_batch,
                                                        opl.folio_interno,
                                                        opl.estado_id AS estado_id,
                                                        (CASE opl.estado_id WHEN 0 THEN 'Iniciada' 
                                                                            WHEN 1 THEN 'Iniciada' WHEN 2 THEN 'Finalizada'
                                                        END) AS estado
                                                    FROM
                                                    	`arg_ordenes_plantas` ord
                                                        LEFT JOIN arg_ordenes_detallePlantas AS opl
                                                        	ON opl.trn_id_rel = ord.trn_id
                                                    WHERE
                                                        estado_id <> 99 
                                                        AND ord.unidad_id = 99 
                                                        AND DATE_FORMAT(ord.fecha, '%Y-%m-%d') BETWEEN '$fecha_i' AND '$fecha_f'
                                                    ORDER BY
                                                        ord.fecha DESC"
                                            ) or die(mysqli_error()); */

    $datos_orden_detalle = $mysqli->query("SELECT DISTINCT
                                                        DATE_FORMAT(ord.fecha, '%Y-%m-%d') AS fecha,
                                                        ord.trn_id,
                                                        (CASE ord.fecha_final WHEN '' THEN 'Iniciada' 
                                                                            ELSE 'Finalizada'
                                                        END) AS estado
                                                        ,d.folio AS folio
                                                        ,u.nombre, u.u_id, d.circuito_id, d.estado_id, ord.fecha as fecha2
                                                        ,um.nombre AS mina
                                                    FROM
                                                    	`arg_ordenes_plantas` ord
                                                        LEFT JOIN arg_ordenes_detallePlantas d
                                                        	ON ord.folio = d.trn_id_rel
                                                        LEFT JOIN arg_usuarios u
                                                            ON u.u_id = ord.usuario_id
                                                        LEFT JOIN arg_empr_unidades AS um
                                                            ON um.unidad_id = d.unidad_id
                                                    WHERE
                                                        ord.unidad_id = 99 
                                                        AND DATE_FORMAT(ord.fecha, '%Y-%m-%d') BETWEEN '$fecha_i' AND '$fecha_f'
                                                    ORDER BY
                                                        ord.fecha DESC"
    ) or die(mysqli_error());

    $unidad_mi = $mysqli->query("SELECT nombre FROM arg_empr_unidades WHERE unidad_id = " . $unidad_id) or die(mysqli_error());
    $unidad_min = $unidad_mi->fetch_assoc();
    $unidad_mina = $unidad_min['nombre'];

?>
    <div class="container-fluid">
        <br /><br /><br /><br /><br />

        <div class="col-md-2 col-lg-2">
            <label for="fecha_inicial"><b>DESDE:</b></label>
            <input type="date" name="fecha_inicial" class="form-control" id="fecha_inicial" value="<? echo $fecha_i; ?>" />
        </div>
        <div class="col-md-2 col-lg-2">
            <label for="fecha_final"><b>HASTA:</b></label><br />
            <input type="date" name="fecha_final" class="form-control" id="fecha_final" value="<? echo $fecha_f; ?>" />
        </div>
        <div class="col-md-2 col-lg-4">
            <label for="print"></label><br /><br />
            <button type='button' class='btn btn-success' onclick='actualizar_fechas();' name='print' id='print'>VER</button>
        </div>
        <br /><br /><br /><br /><br />

        <?
        $html_det = "<table class='table table-striped' id='motivos'>
                                <thead>                                
                                     <tr class='table-info'>      
                                        <th colspan='5'>Ordenes de Despojo</th>";
        $html_det .= "<th></th>
                                     </tr>
                                    <tr class='table-info' justify-content: center;>            
                                        <th scope='col1'>Mina</th>                                        
                                        <th scope='col1'>Fecha</th> 
                                        <th scope='col1'>Folio</th>  
                                        <th scope='col1'>Circuito</th>
                                        <th scope='col1'>Estado</th>      
                                        <th scope='col1'>Usuario</th>";
        $html_det .= "</tr>
                               </thead>
                               <tbody>";//                                        <th scope='col1'>Circuito</th>

        while ($fila = $datos_orden_detalle->fetch_assoc()) {
            $num = 1;
            $variable_img = $fila['etapa_img'];
            $html_det .= "<tr>";
            
            $html_det .= "<td>" . $fila['mina'] . "</td>";
            
            $html_det .= "<td>" . $fila['fecha'] . "</td>";
            $html_det .= "<td>" . $fila['folio'] . "</td>";
            
            $html_det .= "<td>" . $fila['circuito_id'] . "</td>";
        //    $html_det .= "<td>" . $fila['circuito_id'] . "</td>";
            if ($fila['estado_id'] == '0') {

                $html_det .= "<td> <button type='button' class='btn btn-info'";
                // if ($fila['boton_acceso'] <> 0){                                                                
                //  $html_det.="<td> <a href='orden_planta.php?trn_id=".$fila['trn_id']."' target='_blank'></td>";
                $html_det .= "onclick = iniciar_seg(" . $fila['trn_id'] . ",'" . $fila['fecha'] . "','" . $fila['u_id'] . "'," . $unidad_id . ")";
                // }
                $html_det .= "><span class='fa fa-hourglass-start fa-2x'>Iniciada</span>
                                                                </button></td>";
            } else {
                $html_det .= "<td> <button type='button' class='btn btn-warning'";
                // if ($fila['boton_acceso'] <> 0){                                                                
                //  $html_det.="<td> <a href='orden_planta.php?trn_id=".$fila['trn_id']."' target='_blank'></td>";
                $html_det .= "onclick = iniciar_seg(" . $fila['trn_id'] . ",'" . $fila['fecha'] . "','" . $fila['u_id'] . "'," . $unidad_id . ")";
                // }
                $html_det .= "><span class='fa fa-hourglass-start fa-2x'>Finalizada</span>
                                                                </button></td>";
            }

            $html_det .= "<td>" . $fila['nombre'] . "</td>";
        }
        $html_det .= "</tbody></table>";

        echo ("$html_en");
        echo ("$html_det");
        ?>
    </div>
<?
}
?>