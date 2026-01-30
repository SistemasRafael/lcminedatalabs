<?

/**
 * Informe para exportar desde Bd Minedata-labs
 * Danira Romero Maldonado * 
 * ----------------------------------------
 * Listado de �rdenes
 **/

include "connections/config.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objWriter = new Spreadsheet();



$trn_id = $_GET['trn_id'];
$metodo_id = $_GET['metodo_id'];

//$objPHPExcel   = new PHPExcel;
$objWriter->getDefaultStyle()->getFont()->setName('Arial');
$objWriter->getDefaultStyle()->getFont()->setSize(10);
//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
$datos_orden = $mysqli->query(
    "SELECT
                                 un.nombre AS unidad, ord.folio, ord.fecha_inicio, ord.hora, us.nombre AS usuario
                                ,(CASE WHEN ord.trn_id_rel <> 0 THEN 1 ELSE 0 END) AS reensaye
                           FROM 
                                arg_ordenes_detalle AS det
                                LEFT JOIN `arg_ordenes` ord
                                    ON det.trn_id_rel  = ord.trn_id                                    
                                LEFT JOIN arg_empr_unidades AS un
                                    ON un.unidad_id = ord.unidad_id
                                LEFT JOIN arg_usuarios us
                                    ON us.u_id = ord.usuario_id
                           WHERE det.trn_id = " . $trn_id
) or die(mysqli_error($mysqli));
$orden_encabezado = $datos_orden->fetch_assoc();
//http://192.168.20.3:81/minedata_labs/exportar_listado_ordenes.php?unidad_id=3&fecha_inicial=10-06-2022&fecha_final=12-06-2022
$mysqli->set_charset("utf8");
mysqli_multi_query($mysqli, "SELECT posicion, folio_interno, mr.folio as nombre, ot.metodo_id FROM `arg_ordenes_soluciones` ot LEFT JOIN arg_ordenes_muestrasSoluciones mr ON mr.trn_id = ot.trn_id_rel WHERE ot.trn_id_batch = $trn_id AND ot.metodo_id = $metodo_id") or die(mysqli_error($mysqli));
$result = $mysqli->store_result();
$objSheet = $objWriter->getActiveSheet();
$objSheet->setTitle('Muestras de Control para Orden de Trabajo '.$orden_encabezado['folio']);
$objSheet->setCellValue('B1', 'Posicion');
$objSheet->setCellValue('C1', 'Folio Interno');
$objSheet->setCellValue('D1', 'Nombre');
$objSheet->setCellValue('E1', 'Metodo ID');

//Se inserta el detalle de la consulta al informe
$num = 1;
$i = 3;

while ($row = $result->fetch_row()) {
    //$row = eliminar_acentos($row);
    $objSheet->setCellValue('A' . $i, $num);
    $objSheet->setCellValue('B' . $i, $row['posicion']);
    $objSheet->setCellValue('C' . $i, $row['folio_interno']);
    $objSheet->setCellValue('D' . $i, $row['nombre']);
    $objSheet->setCellValue('E' . $i, $row['metodo_id']);
    $i = $i + 1;
    $num = $num + 1;
}

//Ajuste de columnas a tama�o del texto contenido
for ($col = 'A'; $col != 'O'; $col++) {
    $objSheet->getColumnDimension($col)->setAutoSize(true);
}

//Ajuste de columnas a tama�o del texto contenido
for ($col = 'A'; $col != 'O'; $col++) {
    $objSheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($objWriter);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listados.xlsx"');

$writer->save('php://output');
