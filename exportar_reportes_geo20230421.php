<?

/**
 * Informe para exportar desde Bd Minedata-labs
 * Danira Romero Maldonado * 
 * ----------------------------------------
 * Listado de �rdenes
 **/

include "connections/config.php";
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$fecha_inicial = $_GET['fecha_inicial'];
$fecha_final = $_GET['fecha_final'];
$fecha_inicial = date("d-m-Y", strtotime($fecha_inicial));
$fecha_final = date("d-m-Y", strtotime($fecha_final));
$tipo_id = $_GET['tipo_id'];
$spreadsheet  = new Spreadsheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

$mysqli->set_charset("utf8");

$datos_geo = $mysqli->query("CALL arg_rpt_reporteGeosql('$fecha_inicial','$fecha_final', $tipo_id)") or die(mysqli_error($mysqli));
$objSheet = $spreadsheet->getActiveSheet();

$objSheet->setCellValue('A2', 'Num');
$objSheet->setCellValue('B2', 'Muestra');
$objSheet->setCellValue('C2', 'Au_PPM');
$objSheet->setCellValue('D2', 'Ag_PPM');
$nueva_orden = "";
//Se inserta el detalle de la consulta al informe
$num = 1;
$i = 3;
while ($row = $datos_geo->fetch_assoc()) {
    $orden = $row['orden_trabajo'];
    if ($num != 1) {
        if ($orden != $nueva_orden) {
            $i = 3;
            $objSheet = $spreadsheet->createSheet();
            $objSheet->setTitle('Reporte Geología para Orden '.$orden);
            $objSheet->setCellValue('A1', 'Orden de Trabajo '.$orden);
            $objSheet->mergeCells('A1:D1');
            $objSheet->setCellValue('A2', 'Num');
            $objSheet->setCellValue('B2', 'Muestra');
            $objSheet->setCellValue('C2', 'Au_PPM');
            $objSheet->setCellValue('D2', 'Ag_PPM');
        }
    }else{
        $objSheet->setTitle('Reporte Geología para Orden '.$orden);
        $objSheet->setCellValue('A1', 'Orden de Trabajo '.$orden);
    }

    $objSheet->setCellValue('A' . $i, $num);
    $objSheet->setCellValue('B' . $i, $row['muestra_geologia']);
    $objSheet->setCellValue('C' . $i, $row['au_ppm']);
    $objSheet->setCellValue('D' . $i, $row['ag_ppm']);
    $num = $num + 1;
    $i = $i + 1;
    $nueva_orden = $orden;
}
//Ajuste de columnas a tama�o del texto contenido
for ($col = 'A'; $col != 'N'; $col++) {
    $objSheet->getColumnDimension($col)->setAutoSize(true);
}

//Ajuste de columnas a tama�o del texto contenido
for ($col = 'A'; $col != 'N'; $col++) {
    $objSheet->getColumnDimension($col)->setAutoSize(true);
}


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reportes Geología.xlsx"');
$writer->save('php://output');
