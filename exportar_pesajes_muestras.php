<?php

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

$metodo_sel = $_GET['metodo_sel'];
$tipo_mues = $_GET['tipo_mues'];
$orden = $_GET['orden'];

$fecha_i = $_GET['fecha_i'];
$fecha_f = $_GET['fecha_f'];

if($orden <> ''){    
   $trn_orden = $mysqli->query("SELECT 
                                    odet.trn_id
                                FROM 
                                    arg_ordenes_detalle odet
                                WHERE odet.folio_interno = '".$orden."'") or die(mysqli_error());             
   $trn_orden_se = $trn_orden->fetch_assoc();
   $trn_orden_sel = $trn_orden_se['trn_id'];
}
else{
    $trn_orden_sel = 0;
}

$spreadsheet  = new Spreadsheet();
$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

$mysqli->set_charset("utf8");
mysqli_multi_query($mysqli, "CALL arg_rpt_pesajesMuestras(" . $trn_orden_sel. ", " . $tipo_mues  . ", " . $metodo_sel .", '".$fecha_i."', '".$fecha_f."')") or die(mysqli_error($mysqli));
$result = $mysqli->store_result();

$objSheet = $spreadsheet->getActiveSheet();
$objSheet->setTitle('Pesajes Muestra');

$objSheet->mergeCells('A1:I1');
$objSheet->setCellValue('A1', 'Pesajes Muestra');
$objSheet->setCellValue('A2', 'No.');
$objSheet->setCellValue('B2', 'Orden');
$objSheet->setCellValue('C2', 'Folio Interno');
$objSheet->setCellValue('D2', 'Muestra');
$objSheet->setCellValue('E2', 'Metodo');
$objSheet->setCellValue('F2', 'Secado');
$objSheet->setCellValue('G2', 'Peso Que');
$objSheet->setCellValue('H2', 'Peso Malla Que');
$objSheet->setCellValue('I2', 'Porc Que');
$objSheet->setCellValue('J2', 'Peso Pul');
$objSheet->setCellValue('K2', 'Peso Malla Pul');
$objSheet->setCellValue('L2', 'Porc Pul');
$objSheet->setCellValue('M2', 'Peso Metodo');
$objSheet->setCellValue('N2', 'Incuarte');
$objSheet->setCellValue('O2', 'Peso Payon');
$objSheet->setCellValue('P2', 'Peso Dore');
$objSheet->setCellValue('Q2', 'Au_PPM');
$objSheet->setCellValue('R2', 'Ag_PPM');

$num = 1;
$i = 3;
while ($row = $result->fetch_row()) {
    $objSheet->setCellValue('A' . $i, $num);
    $objSheet->setCellValue('B' . $i, $row[0]);
    $objSheet->setCellValue('C' . $i, $row[1]);
    $objSheet->setCellValue('D' . $i, $row[2]);
    $objSheet->setCellValue('E' . $i, $row[4]);
    $objSheet->setCellValue('F' . $i, $row[5]);
    $objSheet->setCellValue('G' . $i, $row[6]);
    $objSheet->setCellValue('H' . $i, $row[7]);
    $objSheet->setCellValue('I' . $i, $row[8]);
    $objSheet->setCellValue('J' . $i, $row[9]);
    $objSheet->setCellValue('K' . $i, $row[10]);
    $objSheet->setCellValue('L' . $i, $row[11]);
    $objSheet->setCellValue('M' . $i, $row[12]);
    $objSheet->setCellValue('N' . $i, $row[13]);
    $objSheet->setCellValue('O' . $i, $row[14]);
    $objSheet->setCellValue('P' . $i, $row[15]);
    $objSheet->setCellValue('Q' . $i, $row[16]);    
    $objSheet->setCellValue('R' . $i, $row[17]);
    $num = $num + 1;
    $i = $i + 1;
}

for ($col = 'A'; $col != 'N'; $col++) {
  $objSheet->getColumnDimension($col)->setAutoSize(true);
}

for ($col = 'A'; $col != 'N'; $col++) {
  $objSheet->getColumnDimension($col)->setAutoSize(true);
}


$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Pesaje Muestra.xlsx"');
$writer->save('php://output');
