<?php
require_once('../model/admin-bienModel.php'); // Asegúrate que el path sea correcto
require_once('../tcpdf/tcpdf.php'); // Ruta a la librería TCPDF

// Crear instancia del modelo
$bienModel = new BienModel();
$lista_bienes = $bienModel->listarBienes(); // Asegúrate que esta función retorne todos los bienes

// Crear nueva instancia de TCPDF
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Inventario');
$pdf->SetTitle('Reporte de Bienes');
$pdf->SetHeaderData('', 0, 'Municipalidad Distrital de Luricocha', 'Reporte General de Bienes', [0,64,255], [0,64,128]);
$pdf->setHeaderFont(Array('helvetica', '', 12));
$pdf->setFooterFont(Array('helvetica', '', 10));
$pdf->SetMargins(15, 27, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('helvetica', '', 10);

// Añadir página
$pdf->AddPage();

// Contenido del reporte
$html = '<h2 align="center">Reporte de Bienes Registrados</h2>';
$html .= '<table border="1" cellpadding="5">
<thead>
<tr style="background-color:#d1d1d1;">
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Marca</th>
    <th>Modelo</th>
    <th>Color</th>
    <th>Serie</th>
    <th>Estado</th>
    <th>Ambiente</th>
    <th>Fecha Registro</th>
</tr>
</thead>
<tbody>';

// Agregar los bienes
foreach ($lista_bienes as $bien) {
    $html .= '<tr>
        <td>' . $bien['id'] . '</td>
        <td>' . htmlspecialchars($bien['nombre']) . '</td>
        <td>' . htmlspecialchars($bien['descripcion']) . '</td>
        <td>' . htmlspecialchars($bien['marca']) . '</td>
        <td>' . htmlspecialchars($bien['modelo']) . '</td>
        <td>' . htmlspecialchars($bien['color']) . '</td>
        <td>' . htmlspecialchars($bien['serie']) . '</td>
        <td>' . htmlspecialchars($bien['estado']) . '</td>
        <td>' . htmlspecialchars($bien['ambiente']) . '</td>
        <td>' . $bien['fecha_registro'] . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Escribir el contenido HTML al PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('reporte_bienes.pdf', 'I');
?>
