<?
include "connections/config.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objWriter = new Spreadsheet();

$fecha = $_GET['fecha'];
$tipo = $_GET['tipo'];

//$objPHPExcel   = new PHPExcel;
$objWriter->getDefaultStyle()->getFont()->setName('Arial');
$objWriter->getDefaultStyle()->getFont()->setSize(10);
//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);

$mysqli->set_charset("utf8");
$datos_bancos_detalle = $mysqli->query("CALL arg_rpt_reportePlanta('$fecha', $tipo)") or die(mysqli_error($mysqli));
$objSheet = $objWriter->getActiveSheet();
if ($tipo == 1) {
    $objSheet->setTitle('Reporte Planta');
    $objSheet->mergeCells('B1:C1');
    $objSheet->mergeCells('D1:E1');
    $objSheet->mergeCells('F1:G1');
    $objSheet->mergeCells('H1:I1');
    $objSheet->mergeCells('J1:K1');
    $objSheet->mergeCells('L1:M1');
    $objSheet->setCellValue('A1', 'Descripción');
    $objSheet->setCellValue('B1', 'ORO');
    $objSheet->setCellValue('D1', 'PLATA');
    $objSheet->setCellValue('F1', 'COBRE');
    $objSheet->setCellValue('H1', 'NaCN');
    $objSheet->setCellValue('J1', 'pH');
    $objSheet->setCellValue('L1', 'CAO');
    $objSheet->setCellValue('A2', '');
    $objSheet->setCellValue('B2', '1er. Turno');
    $objSheet->setCellValue('C2', '2do. Turno');
    $objSheet->setCellValue('D2', '1er. Turno');
    $objSheet->setCellValue('E2', '2do. Turno');
    $objSheet->setCellValue('F2', '1er. Turno');
    $objSheet->setCellValue('G2', '2do. Turno');
    $objSheet->setCellValue('H2', '1er. Turno');
    $objSheet->setCellValue('I2', '2do. Turno');
    $objSheet->setCellValue('J2', '1er. Turno');
    $objSheet->setCellValue('K2', '2do. Turno');
    $objSheet->setCellValue('L2', '1er. Turno');
    $objSheet->setCellValue('M2', '2do. Turno');
    $objSheet->setCellValue('A3', '');
    $objSheet->setCellValue('B3', 'Au (ppm)');
    $objSheet->setCellValue('C3', 'Au (ppm)');
    $objSheet->setCellValue('D3', 'Ag (ppm)');
    $objSheet->setCellValue('E3', 'Ag (ppm)');
    $objSheet->setCellValue('F3', 'Cu (ppm)');
    $objSheet->setCellValue('G3', 'Cu (ppm)');
    $objSheet->setCellValue('H3', 'NaCN (ppm)');
    $objSheet->setCellValue('I3', 'NaCN (ppm)');
    $objSheet->setCellValue('J3', 'pH');
    $objSheet->setCellValue('K3', 'pH');
    $objSheet->setCellValue('L3', 'CaO (ppm)');
    $objSheet->setCellValue('M3', 'CaO (ppm)');
} else {
    $objSheet->setTitle('Reporte Planta');
    $objSheet->mergeCells('B1:C1');
    $objSheet->mergeCells('B2:C2');
    $objSheet->mergeCells('B3:C3');
    $objSheet->mergeCells('D1:E1');
    $objSheet->mergeCells('D2:E2');
    $objSheet->mergeCells('D3:E3');
    $objSheet->mergeCells('F1:G1');
    $objSheet->mergeCells('F2:G2');
    $objSheet->mergeCells('F3:G3');
    $objSheet->mergeCells('H1:I1');
    $objSheet->mergeCells('H2:I2');
    $objSheet->mergeCells('H3:I3');
    $objSheet->mergeCells('J1:K1');
    $objSheet->mergeCells('J2:K2');
    $objSheet->mergeCells('J3:K3');
    $objSheet->mergeCells('L1:M1');
    $objSheet->mergeCells('L2:M2');
    $objSheet->mergeCells('L3:M3');
    $objSheet->setCellValue('A1', 'Descripción');
    $objSheet->setCellValue('B1', 'ORO');
    $objSheet->setCellValue('D1', 'PLATA');
    $objSheet->setCellValue('F1', 'COBRE');
    $objSheet->setCellValue('H1', 'NaCN');
    $objSheet->setCellValue('J1', 'pH');
    $objSheet->setCellValue('L1', 'CAO');
    $objSheet->setCellValue('A2', '');
    $objSheet->setCellValue('B2', '1er. Turno');
    $objSheet->setCellValue('D2', '1er. Turno');
    $objSheet->setCellValue('F2', '1er. Turno');
    $objSheet->setCellValue('H2', '1er. Turno');
    $objSheet->setCellValue('J2', '1er. Turno');
    $objSheet->setCellValue('L2', '1er. Turno');
    $objSheet->setCellValue('A3', '');
    $objSheet->setCellValue('B3', 'Au (ppm)');
    $objSheet->setCellValue('D3', 'Ag (ppm)');
    $objSheet->setCellValue('F3', 'Cu (ppm)');
    $objSheet->setCellValue('H3', 'NaCN (ppm)');
    $objSheet->setCellValue('J3', 'pH');
    $objSheet->setCellValue('L3', 'CaO (ppm)');
}


$i = 4;

while ($row = $datos_bancos_detalle->fetch_assoc()) {
    //$row = eliminar_acentos($row);
    if ($tipo == 1) {
        $au_ppm_t1 = $row['Au_ppm_t1'] ? $row['Au_ppm_t1'] : '0';
        $au_ppm_t2 = $row['Au_ppm_t2'] ? $row['Au_ppm_t2'] : '0';
        $ag_ppm_t1 = $row['Ag_ppm_t1'] ? $row['Ag_ppm_t1'] : '0';
        $ag_ppm_t2 = $row['Ag_ppm_t2'] ? $row['Ag_ppm_t2'] : '0';
        $cu_ppm_t1 = $row['cu_ppm_t1'] ? $row['cu_ppm_t1'] : '0';
        $cu_ppm_t2 = $row['cu_ppm_t2'] ? $row['cu_ppm_t2'] : '0';
        $phh_t1 = $row['phh_t1'] ? $row['phh_t1'] : '0';
        $phh_t2 = $row['phh_t2'] ? $row['phh_t2'] : '0';
        $cnl_t1 = $row['cnl_t1'] ? $row['cnl_t1'] : '0';
        $cnl_t2 = $row['cnl_t2'] ? $row['cnl_t2'] : '0';
        $cao_t1 = $row['cao_t1'] ? $row['cao_t1'] : '0';
        $cao_t2 = $row['cao_t2'] ? $row['cao_t2'] : '0';
        $objSheet->setCellValue('A' . $i, $row['folio']);
        $objSheet->setCellValue('B' . $i, $au_ppm_t1);
        $objSheet->setCellValue('C' . $i, $au_ppm_t2);
        $objSheet->setCellValue('D' . $i, $ag_ppm_t1);
        $objSheet->setCellValue('E' . $i, $ag_ppm_t2);
        $objSheet->setCellValue('F' . $i, $cu_ppm_t1);
        $objSheet->setCellValue('G' . $i, $cu_ppm_t2);
        $objSheet->setCellValue('H' . $i, $cnl_t1);
        $objSheet->setCellValue('I' . $i, $cnl_t2);
        $objSheet->setCellValue('J' . $i, $phh_t1);
        $objSheet->setCellValue('K' . $i, $phh_t2);
        $objSheet->setCellValue('L' . $i, $cao_t1);
        $objSheet->setCellValue('M' . $i, $cao_t2);
    } else {
        $au_ppm_t1 = $row['Au_ppm_t1'] ? $row['Au_ppm_t1'] : '0';
        $ag_ppm_t1 = $row['Ag_ppm_t1'] ? $row['Ag_ppm_t1'] : '0';
        $cu_ppm_t1 = $row['cu_ppm_t1'] ? $row['cu_ppm_t1'] : '0';
        $phh_t1 = $row['phh_t1'] ? $row['phh_t1'] : '0';
        $cnl_t1 = $row['cnl_t1'] ? $row['cnl_t1'] : '0';
        $cao_t1 = $row['cao_t1'] ? $row['cao_t1'] : '0';
        $objSheet->setCellValue('A' . $i, $row['folio']);
        $objSheet->setCellValue('B' . $i, $au_ppm_t1);
        $objSheet->setCellValue('D' . $i, $ag_ppm_t1);
        $objSheet->setCellValue('F' . $i, $cu_ppm_t1);
        $objSheet->setCellValue('H' . $i, $cnl_t1);
        $objSheet->setCellValue('J' . $i, $phh_t1);
        $objSheet->setCellValue('L' . $i, $cao_t1);
    }

    $i = $i + 1;
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
header('Content-Disposition: attachment;filename="Reporte Planta.xlsx"');

$writer->save('php://output');
