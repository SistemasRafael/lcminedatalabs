<?php

include "connections/config.php";

 ?>
<script>
    function notificar_nuevaorden($unidad_id,$trn_id)
            {
                 alert('Se generó la orden de trabajo satisfactoriamente');
                 var trn_id = $trn_id;
                 var unidad_id = $unidad_id 
                 //var unidad_id = document.getElementById("mina_seleccionada").value;
            $.ajax({
            		url: 'notificacion_nuevaordenPlanta.php' ,
            		type: 'POST' ,
            		dataType: 'html',
            		data: {trn_id, trn_id},
            	})
            	.done(function(respuesta){
            	   //alert(respuesta);
                        //alert(print_d);
                 // var print_d = '<?php echo "\orden_trabajo_rep.php?trn_id="?>'+trn_id;
                //  window.location.href = print_d;		                  
              }) 
            }
 </script>
 <?php          

$circuito = $_POST['actual'];
$fecha = $_POST['fecha'];
$u_id = $_POST['u_id'];
$unidad_id = $_POST['unidad_id'];

$check_query1 = $mysqli->query("SELECT d.folio FROM arg_ordenes_detallePlantas d INNER JOIN arg_ordenes_plantas o ON d.trn_id_rel = o.trn_id
 WHERE o.fecha = '$fecha' AND d.circuito_id = $circuito AND d.estado_id = 0 AND d.unidad_id = $unidad_id") or die(mysqli_error($mysqli));

if (mysqli_num_rows($check_query1) != 0) {
    $check1 = $check_query1->fetch_assoc();
    $folio = $check['folio'];
    echo "Ya tiene una orden iniciada.";
} else {
    $query = "CALL arg_prc_crearOrdenPlantas ($unidad_id, '$fecha', $circuito, $u_id)";
    mysqli_multi_query($mysqli, $query);

    $check_query = $mysqli->query("SELECT trn_id, folio FROM arg_ordenes_plantas WHERE usuario_id = $u_id AND fecha = '$fecha'") or die(mysqli_error($mysqli));

    if (mysqli_num_rows($check_query) != 0) {
        $check = $check_query->fetch_assoc();
        $folio = $check['folio'];
        $trnid = $check['trn_id'];
        echo $folio;
       /// echo ("<script> notificar_nuevaorden ($unidad_id,$trnid)</script>");
        
    } else {
        echo "Error, por favor contacte a un administrador.";
    }
}
