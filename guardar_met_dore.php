<?include "connections/config.php";?>
<?php
$html = '';
$trnid_m    = $_POST['trnid_orden'];
$trnidrel_m = $_POST['trnid_muestra'];
$metodo_sel = $_POST['metodo_id'];
$fase_sel   = $_POST['fase'];
$etapa_sel  = $_POST['etapa'];
$cantidad   = $_POST['cantidad_metodo'];
$final      = $_POST['fin_met'];
$u_id       = $_SESSION['u_id'];
/*$trnid_m    = 402;
$trnidrel_m = 10435;
$metodo_sel = 24;
$fase_sel   = 9;
$etapa_sel  = 6;
$cantidad   = 13;
$final      = 0;
$u_id       = 1;*/

/*echo $trnid_m;
echo $metodo_sel;
echo $trnidrel_m;*/

if (isset($trnid_m)){
   mysqli_multi_query ($mysqli, "CALL arg_prc_ordenMetodoDore(".$trnid_m.", ".$trnidrel_m.", ".$metodo_sel.", ".$fase_sel.", ".$etapa_sel.", ".$cantidad.", ".$u_id.",".$final.")") OR DIE (mysqli_error($mysqli));    
         
      $tipo_orden = $mysqli->query("SELECT 
                                         (CASE WHEN ord.trn_id_rel = 0 THEN 0 ELSE 1 END) AS reensaye 
                                        ,odet.folio_interno AS folio_interno
                                        
                                      FROM arg_ordenes ord
                                      LEFT JOIN arg_ordenes_detalle odet
                                            ON ord.trn_id =  odet.trn_id_rel
                                      WHERE 
                                           odet.trn_id = ".$trnid_m
                                    ) or die(mysqli_error());             
       $tipo_ord = $tipo_orden->fetch_assoc();
       $reensaye = $tipo_ord['reensaye'];
       $orden_trabajo = $tipo_ord['folio_interno'];
       
      if ($reensaye == 0){
            $resultado = $mysqli->query("SELECT
                                         se.trn_id as trnid_batch_met
                                        ,se.trn_id_rel as trnid_rel_met
                                        ,met.nombre as metodo_nombre
                                        ,ROUND(se.peso_dore, 2) AS peso_dore
                                        ,om.folio_interno as muestra_met                                      
                                        ,om.muestra_geologia as muestra_interna_met
                                     FROM 
                                        arg_muestras_resultados se
                                        LEFT JOIN ordenes_transacciones om
                                            ON se.trn_id = om.trn_id_batch
                                            AND se.trn_id_rel = om.trn_id_rel
                                        LEFT JOIN arg_metodos met
                                            ON met.metodo_id = se.metodo_id
                                     WHERE 
                                        se.trn_id = ".$trnid_m."
                                        AND se.metodo_id = ".$metodo_sel."
                                        AND se.peso_dore = 0
                                     ORDER BY 
                                        om.folio_interno"
                                    ) or die(mysqli_error());
                                    /* */
      }
       else{
            $origen_reen = $mysqli->query("SELECT 
                                                                        COUNT(*) AS existe_rech
                                                                   FROM 
                                                                       arg_muestras_reensaye mre
                                                                   WHERE mre.trn_id_rel = " . $trnid_m . " 
                                                                   AND mre.metodo_id = (CASE WHEN " . $metodo_sel . " = 0 
                                                                                        THEN mre.metodo_id ELSE " . $metodo_sel . " 
                                                                                        END) 
                                                                   AND mre.trn_id_muestra IN (SELECT trn_id_rel 
                                                                                          FROM muestras_recheck)"
                                    ) or die(mysqli_error($mysqli));
                                    $existe_reche = $origen_reen->fetch_assoc();
                                    $existe_rec = $existe_reche['existe_rech'];
                                    
                                    if ($existe_rec == 0){  
                                        $resultado = $mysqli->query("SELECT
                                             se.trn_id as trnid_batch_met
                                            ,se.trn_id_rel as trnid_rel_met
                                            ,met.nombre as metodo_nombre            
                                            ,ROUND(se.peso_dore, 2) AS peso_dore
                                            ,om.folio_interno as muestra_met                                      
                                            ,om.muestra_geologia as muestra_interna_met
                                         FROM 
                                            arg_muestras_resultados se
                                            LEFT JOIN ordenes_reensayes om
                                                ON se.trn_id = om.trn_id_rel
                                                AND se.trn_id_rel = om.trn_id_muestra
                                            LEFT JOIN arg_metodos met
                                                ON met.metodo_id = se.metodo_id
                                         WHERE 
                                            se.trn_id = ".$trnid_m."
                                            AND se.metodo_id = ".$metodo_sel."
                                            AND se.peso_dore = 0
                                         ORDER BY 
                                            om.folio_interno"
                                        ) or die(mysqli_error());
                                    }
                                    else{
                                        $resultado = $mysqli->query("SELECT
                                             se.trn_id as trnid_batch_met
                                            ,se.trn_id_rel as trnid_rel_met
                                            ,met.nombre as metodo_nombre            
                                            ,ROUND(se.peso_dore, 2) AS peso_dore
                                            ,om.folio_interno as muestra_met                                      
                                            ,om.muestra_geologia as muestra_interna_met
                                         FROM 
                                            arg_muestras_resultados se
                                            LEFT JOIN ordenes_reensayes_recheck om
                                                ON se.trn_id = om.trn_id_rel
                                                AND se.trn_id_rel = om.trn_id_muestra
                                            LEFT JOIN arg_metodos met
                                                ON met.metodo_id = se.metodo_id
                                         WHERE 
                                            se.trn_id = ".$trnid_m."
                                            AND se.metodo_id = ".$metodo_sel."
                                            AND se.peso_dore = 0
                                         ORDER BY 
                                            om.folio_interno"
                                        ) or die(mysqli_error());
                                    }
       }
       //echo $mysqli;
        
        if ($resultado->num_rows > 0) {
            $resultado_efaa = $mysqli->query("SELECT metodo, fase, etapa
                                           FROM ordenes_fases_etapas
                                           WHERE trn_id_rel = ".$trnid_m."
                                           AND fase_id = ".$fase_sel."
                                           AND etapa_id = ".$etapa_sel
                                         ) or die(mysqli_error());
       
            $datos_gen = $resultado_efaa ->fetch_array(MYSQLI_ASSOC);
            $metodo_codigo = $datos_gen['metodo'];
            $metodo_fase = $datos_gen['fase'];
            $metodo_etapa = $datos_gen['etapa'];
                
            if ($etapa_sel == 20){
                 $html.=  "<table class='table text-black' id='tabla_datos_dore'>
                                <thead class='thead-info' align='center'>
                                   <tr class='table-info'>
                                        <th colspan='4'>".$metodo_codigo." Fase: ".$metodo_fase." Etapa: ".$metodo_etapa."</th>
                                   </tr>
                                   <tr class='table-warning' align='center'>
                                        <th colspan='11'>ORDEN DE TRABAJO: ".$orden_trabajo."</th>
                                    </tr>"; 
                 $html.="<tr class='table-info' align='left'>
                                        <th>No.</th>
                                        <th>Muestra</th>
                                        <th>Peso Dor&eacute mg</th>
                                        <th></th>                       
                                </thead>
                                <tbody>";
                 $cont = 0;
                 while ($res_muestras = $resultado->fetch_assoc()) {
                        $cont = $cont+1;
                        $trnid_batch_met = $res_muestras['trnid_batch_met'];
                        $trnid_rel_met   = $res_muestras['trnid_rel_met'];
                        $muestra_met     = $res_muestras['muestra_met']; 
                                                      
                        $html.="<tr>                    
                                   <td>".$cont."</td> 
                                   <td style='display:none;'> <input type='input' id='trnid_batch_dore".$cont."' value='".$trnid_batch_met."'/></td>  
                                   <td style='display:none;'> <input type='input' id='trnid_rel_dore".$cont."' value='".$trnid_rel_met."'/>".$muestra_met."</td>                             
                                   <td>".$muestra_met."</td>
                                   <td> <input type='number' id='peso_dore".$cont."' class='form-control' /> </td>
                                   <td> <button type='button'class='btn btn-primary' id='boton_save_dore' onclick='met_dore_guardar(".$trnid_batch_met.",".$trnid_rel_met.",".$metodo_sel.",".$fase_sel.",".$etapa_sel.",".$cont.")' >
                                            <span class='fa fa-cloud fa-1x'></span>
                                        </button>
                                   </td>                                   
                                </tr>"; 
                    }
             $html .= "</tbody></table></div>";
            }//Fin de etapa 20 pesaje dore
        }
       else{
           $html = "La etapa ha finalizado."; 
        }     
         $mysqli -> set_charset("utf8");
         echo ($html);
         //echo utf8_encode($html);
}