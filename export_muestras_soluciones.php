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

//$objWriter = new Spreadsheet();

    $spreadsheet = new Spreadsheet(); 
    $sheet = $spreadsheet->getActiveSheet();

$unidad_id = $_GET['unidad_id'];
$area_sel = $_GET['area'];

$area = [
    1 => "Planta",
    2 => "Metalurgia",
];
//$objPHPExcel   = new PHPExcel;
//$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
//$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

//http://192.168.20.3:81/minedata_labs/exportar_listado_ordenes.php?unidad_id=3&fecha_inicial=10-06-2022&fecha_final=12-06-2022
//$mysqli->set_charset("utf8");
$datos_bancos_detalle = $mysqli->query(
    "SELECT ba.*
              FROM `arg_ordenes_muestrasSoluciones` ba 
              LEFT JOIN arg_empr_unidades un 
              ON un.unidad_id = ba.unidad_id 
              WHERE ba.unidad_id = " . $unidad_id
) or die(mysqli_error($mysqli));

$datos_unidades = $mysqli->query(
    "SELECT nombre FROM arg_empr_unidades WHERE unidad_id = " .$unidad_id
) or die(mysqli_error($mysqli));

$nombre_uni = $datos_unidades->fetch_assoc();
$nombre = $nombre_uni['nombre'];

//$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Muestras Soluciones');
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A1', 'Muestras');
$sheet->setCellValue('A2', 'ID');
$sheet->setCellValue('B2', 'Folio');
$sheet->setCellValue('C2', 'Area');

//Se inserta el detalle de la consulta al informe
$num = 1;
$i = 3;

while ($row = $datos_bancos_detalle->fetch_assoc()) {
    if ($row['area_id'] == $area_sel || $area_sel == 0) {
        $sheet->setCellValue('A' . $i, $row['id']);
        $sheet->setCellValue('B' . $i, $row['folio']);
        $sheet->setCellValue('C' . $i,  $area[$row['area_id']]);
        $i = $i + 1;
        $num = $num + 1;
    }
}

//Ajuste de columnas a tama�o del texto contenido
for ($col = 'A'; $col != 'O'; $col++) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

//Ajuste de columnas a tama�o del texto contenido
for ($col = 'A'; $col != 'O'; $col++) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Muestras Soluciones"'.$nombre.'.xlsx"');

$writer->save('php://output');
