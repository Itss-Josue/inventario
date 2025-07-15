<?php
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Crear el objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator("yo")
    ->setLastModifiedBy("yo")
    ->setTitle("Enumeración horizontal")
    ->setDescription("Numeración del 1 al 30 en fila 1");

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Hoja 1');

// Llenar la fila 1 con números del 1 al 30 en columnas A-AD
for ($i = 1; $i <= 30; $i++) {
    $col = Coordinate::stringFromColumnIndex($i); // Convierte índice a letra
    $sheet->setCellValue($col . '1', $i);   
}

// Encabezados HTTP para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="enumeracion_1_al_30.xlsx"');
header('Cache-Control: max-age=0');

// Guardar y enviar al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
