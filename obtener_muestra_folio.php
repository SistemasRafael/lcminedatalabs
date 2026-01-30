<?php
//include '\xampp\htdocs\minedata_labs\connections\config.php';
include "connections/config.php";
$html   = '';
$trn_id_sel  = $_POST['muestra_sele'];
      
if (isset($trn_id_sel)){
         $resultado = $mysqli->query("SELECT folio
                                      FROM 
                                        arg_ordenes_muestrasSoluciones mu
                                      WHERE 
                                     	  mu.trn_id = ".$trn_id_sel
                                    ) or die(mysqli_error());
  }    
if ($resultado->num_rows > 0) {
 
   while ($row = $resultado->fetch_assoc()) {  
        $folio_act   = $row['folio']; 
        $html = $folio_act;
        //("<label for=\"$trn_muestra\"><input id=\"$folio_act\" type=\"checkbox\" value=\"$folio_act\" />$folio_act</label>");
        // ("<option value=$trn_muestra>$folio_act</option>"); 
    }
    //$html .= "</div>";
}

echo $html;
?>