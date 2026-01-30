   <? // include "connections/config.php";
        $trn_id_abs = $_GET['trn_id_abs'];
        $metodo_id_abs = $_GET['metodo_id_abs'];
        $u_id_abs = $_SESSION['u_id'];
        $unidad_id = $_GET['unidad_id'];
        $_SESSION['unidad_id'] = $unidad_id;
   ?>
     <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">     
 <script>
 
       function enviar_correo($trn_id)
            {
                 var trn_id = $trn_id;
                 var enviar_email = '<?php echo "\ enviarEmail.php?trn_id="?>'+trn_id;
                 window.location.href = enviar_email;
            }
            
       function actualizar_lib(unidad_id)
            {
                var unidad_id = unidad_id;                
                var print_d = '<?php echo "\seguimiento_ordenes.php?unidad_id="?>'+unidad_id;                
                window.location.href = print_d;
            }
            
             
       function liberar_res(trn_id_l, metodo_id_l, u_id_l, unidad_id_l){           
            var trn_id_a    = trn_id_l;
            var metodo_id_a = metodo_id_l;
            var u_id_a      = u_id_l;
            var unidad_id_a = unidad_id_l;
            
            /*alert(trn_id_a);
            alert(metodo_id_l);
            alert(u_id_l);
            alert(unidad_id_l);*/
            $.ajax({
                		url: 'liberar_resultados_sobr.php' ,
                		type: 'POST' ,
                		dataType: 'html',
                		data: {trn_id_a:trn_id_a, metodo_id_a:metodo_id_a, u_id_a:u_id_a, unidad_id_a:unidad_id_a},
                	})
                	.done(function(respuesta){  
                           alert(respuesta);                     
                })
            actualizar_lib(unidad_id_l); 
        }
            
      /* function liberar_res(trn_id_l, metodo_id_l, u_id_l, unidad_id_l){           
            var trn_id_a    = trn_id_l;
            var metodo_id_a = metodo_id_l;
            var u_id_a      = u_id_l;
            var unidad_id_a = unidad_id_l;
            
           /* alert(trn_id_a);
            alert(metodo_id_l);
            alert(u_id_l);
            alert(unidad_id_l);*/
            /*$.ajax({
                		url: 'liberar_orden.php' ,
                		type: 'POST' ,
                		dataType: 'html',
                		data: {trn_id_a:trn_id_a, metodo_id_a:metodo_id_a, u_id_a:u_id_a, unidad_id_a:unidad_id_a},
                	})
                	.done(function(respuesta){  */            	   
                           //alert(respuesta);
                              
                           // var print_d = '<?php echo "\liberar_orden.php?trn_id_a="?>'+trn_id_a+'&metodo_id_a='+metodo_id_a+'&u_id_a='+u_id_a;                
                         //   window.location.href = print_d;
                                                
              //  })
            
       // }
 </script>
 
<style type="text/css">
	.izq{
		background-color:;
	}
	.derecha{
		background-color:;
	}
	.btnSubmit
    {
        width: 50%;
        border-radius: 1rem;
        padding: 1.5%;
        border: none;
        cursor: pointer;
    }
    .circulos{
    	padding-top: 5em;
    }    
    img{
      max-width: 100%;
    }
 
</style>

<?php
if (isset($_GET['trn_id_abs'])){
    $metodo_id = $_GET['metodo_id_abs'];    
?>
    <div class="container">
           <br />
           <br />                
                <div class="col-2 col-md-2 col-lg-2">
                    <button type='button' class='btn btn-primary' onclick='liberar_res(<? echo $trn_id_abs.", ".$metodo_id.", ".$u_id_abs.", ".$unidad_id?>)' >
                        <span class='fa fa-envelope-o fa-2x'> Liberar </span>
                    </button>
                </div>                
                  
                <div class="col-1 col-md-1 col-lg-1">
                    <button type='button' class='btn btn-success' onclick='exportar_xls(".$trn_id.", ".$metodo_id.", ".$u_id.")' >
                        <span class='fa fa-file-excel-o fa-2x'> Exportar </span>
                    </button>
                </div>
                <br />
             
    <?
        $datos_orden = $mysqli->query("SELECT
                                            un.nombre AS unidad, ord.folio, ord.fecha_inicio, ord.hora, us.nombre AS usuario, det.folio_interno
                                            ,(CASE WHEN ord.trn_id_rel <> 0 THEN 1 ELSE 0 END) AS reensaye
                                       FROM 
                                       `arg_ordenes_detalle` det
                                       LEFT JOIN arg_ordenes ord
                                            ON ord.trn_id = det.trn_id_rel                                     
                                       LEFT JOIN arg_empr_unidades AS un
                                            ON un.unidad_id = ord.unidad_id
                                       LEFT JOIN arg_usuarios us
                                            ON us.u_id = ord.usuario_id
                                       WHERE det.trn_id = ".$trn_id_abs
                                   ) or die(mysqli_error());               
        $orden_encabezado = $datos_orden->fetch_assoc(); 
    
        $datos_metodo = $mysqli->query("SELECT nombre FROM `arg_metodos` WHERE metodo_id = ".$metodo_id) or die(mysqli_error());               
        $metodo = $datos_metodo->fetch_assoc(); 
        $metodo_nombre = $metodo['nombre'];
    
        $existe_revisi = $mysqli->query("SELECT COUNT(*) AS existe FROM `temp_controles` WHERE trn_id_batch = ".$trn_id_abs) or die(mysqli_error());               
        $existe_revision = $existe_revisi->fetch_assoc(); 
        $existe = $existe_revision['existe'];
            
        $result = $mysqli->query("SELECT 
            ot.muestra_geologia
           ,sl.`trn_id_rel`
           ,sl.`trn_id_muestra`
           ,ot.folio_interno   
           ,sl.`metodo_id`
           ,ROUND((`peso`), 3) AS peso
           ,ROUND(`incuarte`, 3) AS incuarte
           ,ROUND(`peso_dore`, 3) AS peso_dore
           ,ROUND(`peso_oro`, 3) AS peso_oro
           ,ROUND(((1000*`peso_oro`)/`peso`), 3) AS absorcion_au
           ,ROUND(((1000*(`peso_dore`-`incuarte`-`peso_oro`))/`peso`), 3) AS absorcion_ag
        FROM 
        	`arg_muestras_sobrelimites` sl
        	LEFT JOIN ordenes_transacciones AS ot
        		ON  sl.trn_id_muestra = ot.trn_id_rel
        WHERE
        	sl.trn_id_rel = ".$trn_id_abs."
        ORDER BY
        	ot.folio_interno") or die(mysqli_error());               
            //$orden_encabezado = $result->fetch_assoc(); 
      
        //mysqli_multi_query ($mysqli, "CALL arg_prc_revisionAbsorcion ($trn_id_abs,$metodo_id_abs)") OR DIE (mysqli_error($mysqli));
        
         ?>
             <br/> <br/>
             <div class="container">            
              <?                 
                 $hoy = date("Y-m-d H:i:s");                 
                 $html_en = "<table class='table table-striped' id='encabezado'>
                             <thead>
                                 <tr class='table-info'>                                  
                                      
                                     <th scope='col'>Batch: ".$orden_encabezado['folio_interno']."</th>
                                     <th scope='col'>Método: ".$metodo_nombre."</th>
                                      <th scope='col'>Fecha Orden: ".$orden_encabezado['fecha_inicio']."</th>
                                    <th scope='col'>Fecha Revisión: ".$hoy."</th>                                    
                                 </tr>
                                 <tr class='table-info'>            
                                    <th scope='col'>Usuario: ".$orden_encabezado['usuario']."</th>
                                    <th scope='col'>Departamento: Laboratorio</th>
                                    <th scope='col'>Revisión de Absorción Atómica</th>
                                     <th scope='col'>Unidad: ".$orden_encabezado['unidad']."</th>
                                  </tr>";
                  $html_en.="</thead></table>";
          
                  $html_det .= "<table class='table' id='detalle_abs'>
                                <thead>                   
                                    <tr class='table-secondary' justify-content: left;>
                                        <th scope='col1'>Folio Interno</th>
                                        <th scope='col1'>Folio Geología</th> 
                                        <th scope='col1'>Peso g</th>  
                                        <th scope='col1'>Incuarte mg</th> 
                                        <th scope='col1'>Peso Doré mg </th>
                                        <th scope='col1'>Peso Oro mg </th>
                                        <th scope='col1'>Au g/ton</th>
                                        <th scope='col1'>Ag g/ton</th>
                                        <th scope='col1'>Prom Au </th>
                                        <th scope='col1'>Prom Ag </th>";                                        
                                    $html_det.="</tr>
                               </thead>
                               <tbody>";
       $cont = 1; 
       $cadados = 2;
       $imp = 1;
       while ($row = $result->fetch_assoc()) {
           $prom_au = $prom_au+$row['absorcion_au'];
           $prom_ag = $prom_ag+$row['absorcion_ag'];
           
                                        $html_det.="<td align='left' >".$row['folio_interno']."</td>";
                                        $html_det.="<td align='left' >".$row['muestra_geologia']."</td>";
                                        $html_det.="<td align='left' >".$row['peso']."</td>";
                                        $html_det.="<td align='left' >".$row['incuarte']."</td>";
                                        $html_det.="<td align='left' >".$row['peso_dore']."</td>";                                         
                                        $html_det.="<td align='left' >".$row['peso_oro']."</td>"; 
                                        $html_det.="<td align='left' >".$row['absorcion_au']."</td>";                                       
                                        $html_det.="<td align='left' >".$row['absorcion_ag']."</td>";     
                                        if ($cont%$cadados == 0){
                                            $prom_au = ($prom_au/2);                                                                                          
                                            $prom_ag = ($prom_ag/2);
                                            if ($prom_au <= 0.020){$prom_au = 0.020;}
                                            if ($prom_ag <= 1){$prom_ag = 1.000;}
                                            $html_det.="<td align='left' >".$prom_au."</td>"; 
                                            $html_det.="<td align='left' >".$prom_ag."</td>";                                            
                                            if ($existe == 0){
                                                
                                                $query = "INSERT INTO temp_controles (trn_id_batch, metodo_id, posicion,  folio_interno, tipo_id, material_id, control, muestra_geologia, absorcion, absorcion_ag, inicio_proceso) ".
                                                                              "VALUES(".$trn_id_abs.",".$metodo_id.", ".$imp.", '".$row['folio_interno']."', 0, 0, '', '".$row['muestra_geologia']."', ".$prom_au.",".$prom_ag.", 0".")";       
                                                ///echo $query;
                                                
                                               $mysqli->query($query);  
                                            
                                            }                                            
                                            $prom_au = 0;
                                            $prom_ag = 0;      
                                            $imp++;                                     
                                       }
                                 $html_det.= "</tr>";
            $cont++;
                  
       }
    }   
    $html_det.="</tbody></table>";
    echo ("$html_en");
    echo ("$html_det");
    ?>
    </div>     
</div>
<?
?>                    
          

