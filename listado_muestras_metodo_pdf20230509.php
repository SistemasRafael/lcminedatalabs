<? include "connections/config.php";
require_once 'vendors/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$trn_id    = $_GET['trn_id'];
$metodo_id = $_GET['metodo_id'];
$preparacion = $_GET['prep'];

$pdf_style = '';

$pdf_style .= '@page { margin-top: 20px; margin-bottom: 80px; margin-left: 0px; margin-right: 0px; }';
$pdf_style .= 'html, body, table { font-family: helvetica; font-size: 100%; }';
$pdf_style .= '#header { position: fixed; left: 15px; top: -25px; right: 0px; height: 150px; text-align: center;font-size: 80% }';
$pdf_style .= '#body { position: absolute; height: 150px; text-align: center; font-size: 80% }';
$pdf_style .= '#footer { position: fixed; left: 15px; bottom: -20px; right: 0px; height: 150px; }';
$pdf_style .= '#footer.page:after { content: counter(page, upper-roman); }';
$pdf_style .= '.column { float: left; width: 33.33%; top: 33.33%;}';
$pdf_style .= '.row:after {content: ""; display: flex; clear: both; }';

$datos_orden = $mysqli->query("SELECT
                                un.nombre AS unidad, od.folio_interno AS folio
                                ,DATE_FORMAT(ord.fecha,'%d/%m/%Y') AS fecha
                                ,(CASE WHEN ord.trn_id_rel <> 0 THEN 1 ELSE 0 END) AS reensaye
                                ,(CASE WHEN ord.tipo = 5 THEN 1 ELSE 0 END) AS sobrelimite
                             FROM `arg_ordenes` ord
                                LEFT JOIN arg_ordenes_detalle od
                                    ON ord.trn_id = od.trn_id_rel
                                LEFT JOIN arg_empr_unidades AS un
                                    ON un.unidad_id = ord.unidad_id                                          
                                WHERE od.trn_id = " . $trn_id
) or die(mysqli_error($mysqli));



$orden_encabezado = $datos_orden->fetch_assoc();
$mina  = $orden_encabezado['unidad'];
$folio = $orden_encabezado['folio'];
$fecha = $orden_encabezado['fecha'];
$reensaye = $orden_encabezado['reensaye'];
$sobrelimite = $orden_encabezado['sobrelimite'];

$datos_met = $mysqli->query("SELECT nombre, nombre_largo FROM arg_metodos WHERE metodo_id =" . $metodo_id) or die(mysqli_error($mysqli));
$datos_meto = $datos_met->fetch_assoc();
$metodo  = $datos_meto['nombre'];
$metodo_desc = $datos_meto['nombre_largo'];

/*
$detalle_etiquetas = $mysqli->query(
    "SELECT
                                                        od.trn_id_batch,
                                                        od.posicion,
                                                        (CASE WHEN od.tipo_id = 0 THEN od.muestra_geologia WHEN od.tipo_id = 3 THEN control END) AS muestra_geologia
                                                 FROM
                                                        ordenes_transacciones od
                                                 WHERE
                                                        od.tipo_id IN(0, 3)
                                                        AND od.trn_id_batch = " . $trn_id . "
                                                        AND od.metodo_id = " . $metodo_id . "
                                                  ORDER BY od.posicion "
) or die(mysqli_error($mysqli));*/

if ($reensaye == 0) {
    $detalle_etiquetas = $mysqli->query("SELECT
                                                     om.trn_id_batch                                                  
                                                    ,muestra_geologia
                                                    ,om.folio_interno as folio_interno
                                                    ,om.posicion                                                    
                                                    FROM
                                                        ordenes_transacciones om
                                                    WHERE 
                                                    	om.trn_id_batch = ".$trn_id."
                                                        AND tipo_id = 0
                                                    UNION ALL
                                                    SELECT
                                                    	 cn.trn_id_batch
                                                   		,cn.control  AS muestra_geologia
                                                    	,cn.folio_interno as folio_interno
                                                        ,cn.posicion
                                                    FROM
                                                        ordenes_transacciones cn
                                                    WHERE 
                                                    	cn.trn_id_batch = ".$trn_id."
                                                        AND cn.trn_id_dup = 0
                                                        AND tipo_id <> 0
                                                        AND cn.metodo_id = ".$metodo_id."
                                                    UNION ALL
                                                    SELECT
                                                    	 ot.trn_id_batch
                                                        ,CONCAT(dup.muestra_geologia, ' - D') AS muestra_geologia
                                                    	,ot.folio_interno as folio_interno
                                                        ,ot.posicion
                                                    FROM
                                                        ordenes_transacciones ot
                                                        LEFT JOIN (SELECT muestra_geologia, trn_id_batch, dup.metodo_id, trn_id_rel
                                                                    FROM
                                                                        ordenes_transacciones dup
                                                                   ) AS dup
                                                            ON  ot.trn_id_batch = dup.trn_id_batch
                                                            AND ot.trn_id_dup = dup.trn_id_rel
                                                            AND ot.metodo_id = dup.metodo_id
                                                    WHERE 
                                                    	ot.trn_id_batch = ".$trn_id."
                                                        AND ot.trn_id_dup <> 0
                                                        AND ot.metodo_id = ".$metodo_id." ORDER BY posicion") or die(mysqli_error($mysqli));
} else if ($sobrelimite == 1){
        $detalle_etiquetas = $mysqli->query("SELECT                                                
                                                 muestra_geologia
                                                ,od.folio_interno
                                            FROM
                                                ordenes_sobrelimites od
                                            WHERE
                                                od.trn_id_rel = " . $trn_id . "
                                                AND od.metodo_id = " . $metodo_id . " 
                                            ORDER BY od.folio_interno") or die(mysqli_error($mysqli));
    }
    else{
        $detalle_etiquetas = $mysqli->query("SELECT                                                
                                                     muestra_geologia
                                                    ,od.folio_interno
                                                FROM
                                                        ordenes_reensayes od
                                                WHERE
                                                    od.trn_id_rel = " . $trn_id . "
                                                    AND od.metodo_id = " . $metodo_id . " 
                                                ORDER BY od.folio_interno") or die(mysqli_error($mysqli));
    }

$total_muestras = (mysqli_num_rows($detalle_etiquetas));

$html_en = "";

$html_en .= "<h1>".$folio."</h1>.</br></br></br></br></br></br>
            <table border='0' width='100%' CELLPADDING=3 CELLSPACING=0>
                             <thead>
                                
                                 <tr>
                                    <th width='10%' align='left'>METODO: " . $metodo . " </th>
                                 </tr>
                                 <tr>
                                    <th width='80%' align='left'>ELABORADA: " . $orden_encabezado['fecha'] . " " . $orden_encabezado['hora'] . "</th> 
                                 </tr>
                                 <tr>
                                    <th width='80%' align='left'>CLIENTE: " . $orden_encabezado['unidad'] . " </th>
                                 </tr>
                                 <tr>
                                    <th width='80%' align='left'>DESCRIPCION: " . $metodo_desc . " </th>
                                 </tr>";
$html_en .= "</thead></table><br/>";
$html_en_met = "";

if ($metodo_id == 1) {
    $html_en_met .= "<table border = '1' width='98%' CELLPADDING=5 CELLSPACING=0>
                            <thead>
                                <tr>
                                    <th width='80% align='center'> PESADO:__________</th>
                                    <th width='80% align='center'> FUNDIDO:__________</th>
                                    <th width='80% align='center'> COPELADO:__________</th>                                    
                                </tr>
                                <tr>
                                    <th width='80% align='center'> DORE:__________</th>
                                    <th width='80% align='center'> ATAQUE QUIMICO:__________</th>
                                    <th width='98% align='center'> PESO ORO:______________</th>                                  
                            </thead>";
    $html_en_met .= "</thead></table><br/>";
}//colspan=2

if ($metodo_id == 3) {
    $html_en_met .= "<table border = '1' width='98%' CELLPADDING=5 CELLSPACING=0>
                            <thead>
                                <tr>
                                    <th width='80% align='center'> FUNDIDO:__________</th>
                                    <th width='80% align='center'> COPELADO:__________</th>
                                    <th width='80% align='center'> DIGESTION:__________</th>
                                    <th width='80% align='center'> AA:__________</th>
                                </tr>
                                <tr>
                                    <th width='98% align='center' colspan=4> ARCHIVO:____________________</th>                                  
                            </thead>";
    $html_en_met .= "</thead></table><br/>";
}
if ($metodo_id == 6 || $metodo_id == 7) {
    $html_en_met .= "<table border = '1' width='98%' CELLPADDING=5 CELLSPACING=0>
                            <thead>
                                <tr>
                                    <th width='80% align='center'> PESADO:__________</th>
                                    <th width='80% align='center'> DIGESTION:__________</th>
                                    <th width='80% align='center'> AFORO:__________</th>
                                    <th width='80% align='center'> AA:__________</th>
                                </tr>
                                <tr>
                                    <th width='98% align='center' colspan=4> ARCHIVO:____________________</th>                                  
                            </thead>";
    $html_en_met .= "</thead></table><br/>";
}
if ($metodo_id == 11 || $metodo_id == 12 || $metodo_id == 13) {
    $html_en_met .= "<table border = '1' width='98%' CELLPADDING=5 CELLSPACING=0>
                            <thead>
                                <tr>
                                    <th width='80% align='center'> PESADO:__________</th>
                                    <th width='80% align='center'> CIANURACION:__________</th>
                                    <th width='80% align='center'> CENTIFRUGADO:__________</th>
                                    <th width='80% align='center'> AA:__________</th>
                                </tr>
                                <tr>
                                    <th width='98% align='center' colspan=4> ARCHIVO:____________________</th>                                  
                            </thead>";
    $html_en_met .= "</thead></table><br/>";
}

if ($metodo_id == 24) {
    $html_en_met .= "<table border = '1' width='98%' CELLPADDING=5 CELLSPACING=0>
                            <thead>
                                <tr>
                                    <th width='80% align='center'> PESADO:__________</th>
                                    <th width='80% align='center'> FUNDIDO:__________</th>
                                    <th width='80% align='center'> COPELADO:__________</th>                                    
                                </tr>
                                <tr>
                                    <th width='80% align='center'> DORE:__________</th>
                                    <th width='80% align='center'> ATAQUE QUIMICO:__________</th>
                                    <th width='98% align='center'> AA:____________________</th>                                  
                            </thead>";
    $html_en_met .= "</thead></table><br/>";
}//colspan=2

$f = 1;
while ($fila_det = $detalle_etiquetas->fetch_assoc()) {
    $etiqueta_muestra[$f]  = $fila_det['folio_interno'];
    $etiqueta_posicion[$f] = $fila_det['posicion'];
    $f++;
}
$i = 1;
$page = 1;
$total_paginas = ceil($total_muestras / 78);
$total_filas   = ceil($total_muestras / 78);
$total = $total_muestras;
while ($page <= $total_paginas) {
    $html_det = "<div class='row' style='margin-top: 45%; margin-left: 10%;>'";
    $columna = 1;
    while ($columna < 4 && $i <= $total_muestras) {
        $f = 0;
        $html_det .= "<div class= 'column' style='right:100%;'>
                        <table border='1' cellspacing='0'>
                                            <thead>                                
                                                <tr>      
                                                    <th align='center' colspan='1'>No.</th>
                                                    <th align='center' colspan='1'>ID MUESTRA</th>  
                                                </tr>
                                            </thead>
                                            <tbody> ";
        while ($total > 0) {
            if ($f == 26) {
                break;
            }
            $html_det .= "<tr>
                                        <th colspan='1'>" . $i . "</th> 
                                        <th colspan='1'>" . $etiqueta_muestra[$i] . "</th> 
                                </tr>";
            $i = $i + 1;
            $f = $f + 1;
            $total = $total - 1;
        }
        $html_det .= "</tbody></table></div>";
        $columna = $columna + 1;
        $pdf_html .= $html_det;
        $top  = '';
        $left = '';
        $html_det = '';
    }
    if ($i != $total_muestras) {
        $pdf_html .= '</div><div style="page-break-inside:always;"></div>';
        $page = $page + 1;
    }
}

$options = array();
$options["isRemoteEnabled"] = true;

/*
$pdf = new Dompdf($options);
$customPaper = array(0, 0, 180, 320);
$pdf->setPaper($customPaper, 'A4');*/

$pdf = new Dompdf($options);
$pdf->setPaper('letter', 'mm', 'A4');
$pdf_content = '';

$pdf_header = '';
$img_factor = 0.30; //0.95;

$par_x = 0;
$par_y = 0;

$ifactor = 220 / 1050;
$iwidth = 50;
//$pdf_header .= "<br/><br/><br/><br/><CXY X='5' Y='185'></CXY>";

$pdf_header .= '<cfs FONTSIZE="20"></cfs>';
$pdf_header .= '<h3>"LABORATORIO QUIMICO: La Colorada"</h3>' ;
//$pdf_header .= '<cfs FONTSIZE="5"></cfs>';
//$pdf_header .= '<CXY X="' . ($par_x+5) . '" Y="' . ($par_y + 5) . '"></CXY>';
$pdf_header .= '<img width="120" height="120" align="left" src="http://192.168.20.58/lcminedatalabs/images/encabezado_viaje.jpg">';


$pdf_header .= $html_en;

$pdf_header .= $html_en_met;

$pdf_content .= '<html>';
$pdf_content .= '<head>';
$pdf_content .= '<style>';
$pdf_content .= $pdf_style;
$pdf_content .= '</style>';
$pdf_content .= '<div id="header">';
$pdf_content .= $pdf_header;
$pdf_content .= '</div>';
$pdf_content .= '</head>';
$pdf_content .= '<body>';
$pdf_content .= '<div id="content">';
$pdf_content .= $pdf_html;
$pdf_content .= '</div>';
$pdf_content .= '</body>';
$pdf_content .= '</html>';

$pdf->loadHtml($pdf_content);

$output_options = array();

$output_options["Accept-Ranges"] = 1;
$output_options["Attachment"] = 0;

$pdf->render();

$pdf->stream($file_name . ".pdf", $output_options);

file_put_contents($file_path . $file_name . '.pdf', $pdf->output());
