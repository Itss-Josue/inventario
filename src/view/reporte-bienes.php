<?php
require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start(); // Asegura que $_SESSION esté disponible

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER."src/control/Bien.php?tipo=ObtenerTodosBienes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
    exit;
}

$respuesta = json_decode($response);
if (!$respuesta || !$respuesta->status) {
    echo "Error al obtener los bienes.";
    exit;
}

$bienes = $respuesta->bienes;

// Crear Excel
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator("Sistema")
    ->setLastModifiedBy("Sistema")
    ->setTitle("Reporte de Bienes")
    ->setDescription("Listado de bienes registrados");

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Bienes");

// Estilos
$styleHeader = ['font' => ['bold' => true]];
$sheet->getStyle('A1:S1')->applyFromArray($styleHeader);

// Encabezados
$headers = [
    'ID', 'Ingreso', 'Ambiente', 'Código Patrimonial', 'Denominación', 'Marca', 'Modelo', 'Tipo', 'Color',
    'Serie', 'Dimensiones', 'Valor', 'Situación', 'Estado Conservación', 'Observaciones',
    'Fecha Registro', 'Usuario Registro', 'Estado'
];

foreach ($headers as $i => $header) {
    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
    $sheet->setCellValue($col . '1', $header);
}

// Datos
$row = 2;
foreach ($bienes as $bien) {
    $datos = [
        $bien->id ?? '',
        $bien->ingresonombre ?? '',
        $bien->ambiente ?? '',
        $bien->cod_patrimonial ?? '',
        $bien->denominacion ?? '',
        $bien->marca ?? '',
        $bien->modelo ?? '',
        $bien->tipo ?? '',
        $bien->color ?? '',
        $bien->serie ?? '',
        $bien->dimensiones ?? '',
        $bien->valor ?? '',
        $bien->situacion ?? '',
        $bien->estado_conservacion ?? '',
        $bien->observaciones ?? '',
        $bien->fecha_registro ?? '',
        $bien->usuarioregistro ?? '',
        $bien->estado ?? ''
    ];

    foreach ($datos as $i => $valor) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
        $sheet->setCellValue($col . $row, $valor);
    }

    $row++;
}

// Salida
ob_clean(); // Limpia buffer para evitar errores en descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="reporte_bienes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
