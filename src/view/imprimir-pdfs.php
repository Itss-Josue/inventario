<?php 
$ruta = explode("/", $_GET['views']);

if (!isset($ruta[1]) || $ruta[1] == "") {
    header("Location: " . BASE_URL . "404");
    exit();
}

require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    // Encabezado personalizado
    public function Header() {
        // Corrección: rutas reales a las imágenes JPG
        $image_path_izq = realpath(__DIR__ . '/../../img/LogoHuanta.jpg');
        $image_path_der = realpath(__DIR__ . '/../../img/LogoAya.jpg');

        // LOGO IZQUIERDO
        if ($image_path_izq && file_exists($image_path_izq)) {
            $this->Image($image_path_izq, 15, 8, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        // TEXTOS
        $this->SetFont('helvetica', 'B', 10);
        $this->SetY(10);
        $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');


        // LOGO DERECHO
        if ($image_path_der && file_exists($image_path_der)) {
            $this->Image($image_path_der, 170, 8, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
    }

    // Pie de página
    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 8);
        $footer_html = '
            <table border="0" width="100%">
                <tr>
                    <td align="center">DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO</td>
                </tr>
            </table>
        ';
        $this->writeHTML($footer_html, true, false, true, false, '');
    }
}

// imprimir instituciones
if ($ruta[1] == "imprInstituciones") {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Institucion.php?tipo=listar&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $respuesta = json_decode($response);
        $instituciones = $respuesta->contenido;

        $new_Date = new DateTime();
        $dia = $new_Date->format('d');
        $año = $new_Date->format('Y');
        $mesNumero = (int)$new_Date->format('n');
        $meses = [1 => 'Enero',2 => 'Febrero',3 => 'Marzo',4 => 'Abril',5 => 'Mayo',6 => 'Junio',7 => 'Julio',8 => 'Agosto',9 => 'Septiembre',10 => 'Octubre',11 => 'Noviembre',12 => 'Diciembre'];

        $contenido_pdf = '
        <style>
            h2 {
                text-align: center;
                text-transform: uppercase;
                font-size: 16px;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
                margin-top: 10px;
            }
            th {
                background-color: #f0f0f0;
                font-weight: bold;
            }
            th, td {
                border: 1px solid #333;
                padding: 5px;
                text-align: center;
            }
            .fecha {
                text-align: right;
                font-size: 11px;
                margin-top: 30px;
            }
            .firmas {
                margin-top: 50px;
                width: 100%;
            }
            .firmas td {
                width: 50%;
                text-align: center;
                border: none;
                padding-top: 40px;
            }
        </style>

        <h2>REPORTE DE INSTITUCIONES</h2>

        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>BENEFICIARIO</th>
                    <th>COD. MODULAR</th>
                    <th>RUC</th>
                    <th>NOMBRE</th>
                </tr>
            </thead>
            <tbody>';
            
        $contador = 1;
        foreach ($instituciones as $institucion) {
            $contenido_pdf .= '
                <tr>
                    <td>'.$contador.'</td>
                    <td>'.$institucion->beneficiario.'</td>
                    <td>'.$institucion->cod_modular.'</td>
                    <td>'.$institucion->ruc.'</td>
                    <td>'.$institucion->nombre.'</td>
                </tr>';
            $contador++;
        }

        $contenido_pdf .= '
            </tbody>
        </table>

        <div class="fecha">
            Ayacucho, '.$dia.' de '.$meses[$mesNumero].' del '.$año.'
        </div>

        <table class="firmas">
            <tr>
                <td>
                    ____________________________<br>
                    ENTREGUÉ CONFORME
                </td>
                <td>
                    ____________________________<br>
                    RECIBÍ CONFORME
                </td>
            </tr>
        </table>';

        // Generar PDF
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Gestión');
        $pdf->SetTitle('REPORTE DE INSTITUCIONES');
        $pdf->SetMargins(PDF_MARGIN_LEFT, 48, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->writeHTML($contenido_pdf, true, false, true, false, '');
        $pdf->Output('REPORTE_INSTITUCIONES.pdf', 'I');
        exit;
    }
}


// Imprimir reporte de ambientes
if ($ruta[1] == "imprAmbientes") {
    
    // Obtener datos de ambientes mediante API
    $ambientes = obtenerAmbientes();
    
    if ($ambientes === false) {
        echo "Error al obtener los datos de ambientes";
        exit;
    }
    
    // Generar PDF
    generarPDFAmbientes($ambientes);
    exit;
}

/**
 * Obtiene la lista de ambientes desde la API
 * @return array|false Lista de ambientes o false en caso de error
 */
function obtenerAmbientes() {
    $url = BASE_URL_SERVER . "src/control/Ambiente.php?tipo=listarTodosAmbientes&sesion=" . 
           $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'];
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: " . BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        error_log("cURL Error: " . $err);
        return false;
    }
    
    $respuesta = json_decode($response);
    return $respuesta->contenido ?? false;
}

/**
 * Genera el HTML para el reporte de ambientes
 * @param array $ambientes Lista de ambientes
 * @return string HTML del reporte
 */
function generarHTMLReporte($ambientes) {
    // Obtener fecha actual
    $fecha = obtenerFechaFormateada();
    
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Ambientes</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 40px;
                color: #333;
            }
            
            h2 {
                text-align: center;
                text-transform: uppercase;
                color: #2c3e50;
                margin-bottom: 30px;
                font-size: 18px;
            }
            
            .header-info {
                margin-bottom: 25px;
                line-height: 1.8;
                font-size: 12px;
            }
            
            .main-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 9px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .main-table th {
                background-color: #34495e;
                color: white;
                font-weight: bold;
                padding: 10px 6px;
                text-align: center;
                border: 1px solid #2c3e50;
            }
            
            .main-table td {
                border: 1px solid #bdc3c7;
                text-align: center;
                padding: 8px 6px;
                vertical-align: middle;
            }
            
            .main-table tbody tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            .main-table tbody tr:hover {
                background-color: #e8f4f8;
            }
            
            .fecha-container {
                margin-top: 40px;
                text-align: right;
                font-size: 12px;
                color: #555;
            }
            
            .firma-section {
                margin-top: 50px;
                width: 100%;
                border-collapse: collapse;
            }
            
            .firma-section td {
                border: none;
                text-align: center;
                padding: 20px;
                font-size: 11px;
                width: 50%;
            }
            
            .firma-line {
                border-bottom: 2px solid #333;
                width: 200px;
                margin: 0 auto 10px auto;
                height: 1px;
            }
            
            .firma-text {
                font-weight: bold;
                color: #555;
            }
        </style>
    </head>
    <body>
        <h2>Reporte de Ambientes</h2>
        
        <table class="main-table">
            <thead>
                <tr>
                    <th>ÍTEM</th>
                    <th>INSTITUCIÓN</th>
                    <th>ENCARGADO</th>
                    <th>CÓDIGO</th>
                    <th>DETALLE</th>
                    <th>OTROS DETALLES</th>
                </tr>
            </thead>
            <tbody>';
    
    // Generar filas de datos
    $contador = 1;
    foreach ($ambientes as $ambiente) {
        $html .= '<tr>';
        $html .= '<td>' . $contador . '</td>';
        $html .= '<td>' . htmlspecialchars($ambiente->institucion ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($ambiente->encargado ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($ambiente->codigo ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($ambiente->detalle ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($ambiente->otros_detalle ?? '') . '</td>';
        $html .= '</tr>';
        $contador++;
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="fecha-container">
            Ayacucho, ' . $fecha . '
        </div>
        
        <table class="firma-section">
            <tr>
                <td>
                    <div class="firma-line"></div>
                    <div class="firma-text">ENTREGUÉ CONFORME</div>
                </td>
                <td>
                    <div class="firma-line"></div>
                    <div class="firma-text">RECIBÍ CONFORME</div>
                </td>
            </tr>
        </table>
    </body>
    </html>';
    
    return $html;
}

/**
 * Obtiene la fecha actual formateada en español
 * @return string Fecha formateada
 */
function obtenerFechaFormateada() {
    $fecha = new DateTime();
    $dia = $fecha->format('d');
    $año = $fecha->format('Y');
    $mesNumero = (int)$fecha->format('n');
    
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    return $dia . " de " . $meses[$mesNumero] . " del " . $año;
}

/**
 * Configura y genera el PDF
 * @param array $ambientes Lista de ambientes
 */
function generarPDFAmbientes($ambientes) {
    $contenido_html = generarHTMLReporte($ambientes);
    
    // Configurar PDF
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Metadatos del PDF
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema de Gestión');
    $pdf->SetTitle('Reporte de Ambientes');
    $pdf->SetSubject('Reporte de Ambientes Institucionales');
    $pdf->SetKeywords('TCPDF, PDF, Ambientes, Reporte');
    
    // Configurar márgenes
    $pdf->SetMargins(PDF_MARGIN_LEFT, 48, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // Agregar página y contenido
    $pdf->AddPage();
    $pdf->writeHTML($contenido_html, true, false, true, false, '');
    
    // Generar salida
    $pdf->Output('REPORTE_AMBIENTES.pdf', 'I');
}

//imprimir bienes
if ($ruta[1] == "imprBienes") {
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
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $respuest = json_decode($response);
        $bienes = $respuest->bienes;

        $new_Date = new DateTime();
        $dia = $new_Date->format('d');
        $año = $new_Date->format('Y');
        $mesNumero = (int)$new_Date->format('n');
        $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

        $contenido_pdf = '';

        $contenido_pdf .= '<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Bienes</title>
  <style>
    body {
      font-family: Inter, Arial, sans-serif;
      margin: 40px;
      color: #333;
    }
    h2 {
      text-align: center;
      text-transform: uppercase;
      color: #2c3e50;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9px;
      margin-top: 15px;
    }
    thead {
      background-color: #2c3e50;
      color: #ecf0f1;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px;
      text-align: center;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    .fecha {
      margin-top: 30px;
      text-align: right;
      font-size: 10px;
      color: #555;
    }
    .firma-section {
      margin-top: 50px;
      width: 100%;
    }
    .firma-section td {
      border: none;
      text-align: center;
      font-size: 10px;
      padding-top: 30px;
    }
  </style>
</head>
<body>

  <h2>REPORTE DE BIENES</h2>

  <table>
    <thead>
      <tr>
        <th>ITEM</th>
        <th>INGRESO</th>
        <th>AMBIENTE</th>
        <th>COD. PATRI</th>
        <th>DENOMINACIÓN</th>
        <th>MARCA</th>
        <th>VALOR</th>
        <th>ESTADO</th>
        <th>REGISTRO</th>
        <th>USUARIO</th>
      </tr>
    </thead>
    <tbody>';

        $contador = 1;
        foreach ($bienes as $bien) {
            $contenido_pdf .= '<tr>';
            $contenido_pdf .= "<td>" . $contador . "</td>";
            $contenido_pdf .= "<td>" . $bien->ingresonombre . "</td>";
            $contenido_pdf .= "<td>" . $bien->ambiente . "</td>";
            $contenido_pdf .= "<td>" . $bien->cod_patrimonial . "</td>";
            $contenido_pdf .= "<td>" . $bien->denominacion . "</td>";
            $contenido_pdf .= "<td>" . $bien->marca . "</td>";
            $contenido_pdf .= "<td>" . $bien->valor . "</td>";
            $contenido_pdf .= "<td>" . $bien->estado_conservacion . "</td>";
            $contenido_pdf .= "<td>" . $bien->fecha_registro . "</td>";
            $contenido_pdf .= "<td>" . $bien->usuarioregistro . "</td>";
            $contenido_pdf .= '</tr>';
            $contador++;
        }

        $contenido_pdf .= '</tbody>
  </table>

  <div class="fecha">
    Ayacucho, ' . $dia . ' de ' . $meses[$mesNumero] . ' del ' . $año . '
  </div>

  <table class="firma-section">
    <tr>
      <td>------------------------------<br>ENTREGUÉ CONFORME</td>
      <td>------------------------------<br>RECIBÍ CONFORME</td>
    </tr>
  </table>

</body>
</html>';

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Gestión');
        $pdf->SetTitle('REPORTE DE BIENES');
        $pdf->SetSubject('Reporte');
        $pdf->SetKeywords('TCPDF, PDF, bienes, reporte');
        $pdf->SetMargins(PDF_MARGIN_LEFT, 48, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->writeHTML($contenido_pdf, true, false, true, false, '');
        $pdf->Output('REPORTE_BIENES.pdf', 'I');
        exit;
    }
}



// Imprimir reporte de movimientos
if ($ruta[1] == "imprMovimientos") {
    
    // Obtener datos de movimientos mediante API
    $movimientos = obtenerMovimientos();
    
    if ($movimientos === false) {
        echo "Error al obtener los datos de movimientos";
        exit;
    }
    
    // Generar PDF
    generarPDFMovimientos($movimientos);
    exit;
}

/**
 * Obtiene la lista de movimientos desde la API
 * @return array|false Lista de movimientos o false en caso de error
 */
function obtenerMovimientos() {
    $url = BASE_URL_SERVER . "src/control/Movimiento.php?tipo=ListarMovimientos&sesion=" . 
           $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'];
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: " . BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        error_log("cURL Error: " . $err);
        return false;
    }
    
    $respuesta = json_decode($response);
    return $respuesta->movimientos ?? false;
}

/**
 * Genera el HTML para el reporte de movimientos
 * @param array $movimientos Lista de movimientos
 * @return string HTML del reporte
 */
function generarHTMLReporteMovimientos($movimientos) {
    // Reutilizar la función existente de fechas
    $fecha = obtenerFechaFormateada();
    $fechaHora = date('d/m/Y H:i');
    $totalMovimientos = count($movimientos);
    
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Movimientos</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "Arial", sans-serif;
                font-size: 10px;
                color: #1f2937;
                padding: 15px;
                background-color: #ffffff;
                line-height: 1.4;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
                padding: 15px 0;
                border-bottom: 3px solid #059669;
                background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
                border-radius: 8px 8px 0 0;
            }

            .header h1 {
                font-size: 18px;
                font-weight: 700;
                color: #065f46;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 5px;
            }

            .subtitle {
                font-size: 10px;
                color: #6b7280;
                font-weight: 500;
            }

            .report-info {
                background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
                padding: 12px 15px;
                border-radius: 8px;
                border-left: 4px solid #10b981;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .report-meta {
                font-size: 10px;
                color: #374151;
                font-weight: 500;
            }

            .summary-stats {
                font-size: 11px;
                font-weight: 700;
                color: #065f46;
                background: white;
                padding: 4px 8px;
                border-radius: 4px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }

            .table-container {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                border: 1px solid #d1d5db;
                margin-bottom: 20px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            }

            .main-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 8px;
            }

            .main-table thead th {
                background: linear-gradient(135deg, #059669 0%, #047857 100%);
                color: white;
                font-weight: 600;
                padding: 10px 6px;
                text-align: center;
                font-size: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                line-height: 1.2;
            }

            .main-table tbody tr {
                transition: background-color 0.2s ease;
            }

            .main-table tbody tr:nth-child(even) {
                background-color: #f9fafb;
            }

            .main-table tbody tr:nth-child(odd) {
                background-color: #ffffff;
            }

            .main-table tbody td {
                padding: 8px 6px;
                text-align: center;
                border-bottom: 1px solid #e5e7eb;
                vertical-align: middle;
                font-size: 7px;
                line-height: 1.3;
            }

            .main-table tbody tr:last-child td {
                border-bottom: none;
            }

            .item-number {
                background-color: #065f46;
                color: white;
                padding: 2px 6px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 7px;
            }

            .ambiente-cell {
                font-weight: 500;
                color: #1e293b;
                text-align: left;
                padding-left: 8px;
                background-color: #f0f9ff;
            }

            .destino-cell {
                font-weight: 500;
                color: #7c2d12;
                text-align: left;
                padding-left: 8px;
                background-color: #fef7ed;
            }

            .usuario-cell {
                font-weight: 500;
                color: #4338ca;
                background-color: #f0f0ff;
            }

            .descripcion-cell {
                text-align: left;
                padding-left: 8px;
                font-size: 7px;
                color: #374151;
                background-color: #f8fafc;
            }

            .fecha-cell {
                font-family: "Courier New", monospace;
                font-size: 7px;
                color: #6b7280;
                font-weight: 600;
                background-color: #fffbeb;
            }

            .institucion-cell {
                font-weight: 500;
                color: #be185d;
                font-size: 6px;
                background-color: #fdf2f8;
            }

            .fecha-generacion {
                text-align: right;
                font-size: 10px;
                color: #6b7280;
                font-style: italic;
                margin-top: 25px;
                font-weight: 500;
            }

            .firmas-section {
                margin-top: 40px;
                width: 100%;
            }

            .firma-container {
                width: 100%;
                border-collapse: collapse;
            }

            .firma-box {
                text-align: center;
                padding: 20px 10px;
                width: 50%;
                font-size: 10px;
                border: none;
            }

            .firma-line {
                border-bottom: 2px solid #374151;
                width: 180px;
                margin: 0 auto 12px auto;
                height: 1px;
            }

            .firma-text {
                font-weight: 600;
                color: #4b5563;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

        </style>
    </head>
    <body>

        <div class="header">
            <h1>Reporte de Movimientos</h1>
            <div class="subtitle">Control de Traslados de Ambientes</div>
        </div>

        <div class="report-info">
            <div class="report-meta">
                <span>Generado: ' . $fechaHora . '</span>
            </div>
            <div class="summary-stats">
                <span>Total: <strong>' . $totalMovimientos . ' movimientos</strong></span>
            </div>
        </div>

        <div class="table-container">
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="col-numero">N°</th>
                        <th class="col-origen">Ambiente Origen</th>
                        <th class="col-destino">Destino</th>
                        <th class="col-usuario">Usuario</th>
                        <th class="col-fecha">Fecha</th>
                        <th class="col-descripcion">Descripción</th>
                        <th class="col-institucion">Institución</th>
                    </tr>
                </thead>
                <tbody>';

    // Generar filas de datos
    $contador = 1;
    foreach ($movimientos as $movimiento) {
        $fechaFormateada = isset($movimiento->fecha) ? 
                          date('d/m/y', strtotime($movimiento->fecha)) : 'N/A';
        
        $html .= '<tr>';
        $html .= '<td><span class="item-number">' . $contador . '</span></td>';
        $html .= '<td class="ambiente-cell">' . 
                 htmlspecialchars(substr($movimiento->origenname ?? '', 0, 25)) . '</td>';
        $html .= '<td class="destino-cell">' . 
                 htmlspecialchars(substr($movimiento->destinoname ?? '', 0, 20)) . '</td>';
        $html .= '<td class="usuario-cell">' . 
                 htmlspecialchars(substr($movimiento->usuarioname ?? '', 0, 15)) . '</td>';
        $html .= '<td class="fecha-cell">' . $fechaFormateada . '</td>';
        $html .= '<td class="descripcion-cell">' . 
                 htmlspecialchars(substr($movimiento->descripcion ?? '', 0, 40)) . '</td>';
        $html .= '<td class="institucion-cell">' . 
                 htmlspecialchars(substr($movimiento->institucionname ?? '', 0, 10)) . '</td>';
        $html .= '</tr>';
        $contador++;
    }

    $html .= '
                </tbody>
            </table>
        </div>

        <div class="fecha-generacion">
            📍 Ayacucho, ' . $fecha . '
        </div>

        <div class="firmas-section">
            <table class="firma-container">
                <tr>
                    <td class="firma-box">
                        <div class="firma-line"></div>
                        <div class="firma-text">Entregué Conforme</div>
                    </td>
                    <td class="firma-box">
                        <div class="firma-line"></div>
                        <div class="firma-text">Recibí Conforme</div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
    </html>';

    return $html;
}

/**
 * Configura y genera el PDF de movimientos
 * @param array $movimientos Lista de movimientos
 */
function generarPDFMovimientos($movimientos) {
    $contenido_html = generarHTMLReporteMovimientos($movimientos);
    
    // Configurar PDF
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Metadatos del PDF
    $pdf->SetCreator('Sistema de Gestión');
    $pdf->SetAuthor('Sistema Administrativo');
    $pdf->SetTitle('Reporte de Movimientos - ' . date('d/m/Y'));
    $pdf->SetSubject('Reporte detallado de movimientos de ambientes');
    $pdf->SetKeywords('Movimientos, Reporte, Ambientes, PDF, Traslados');
    
    // Configurar márgenes optimizados
    $pdf->SetMargins(10, 15, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // Agregar página y contenido
    $pdf->AddPage();
    $pdf->writeHTML($contenido_html, true, false, true, false, '');
    
    // Generar nombre de archivo único
    $nombreArchivo = 'Reporte_Movimientos_' . date('Y-m-d_H-i-s') . '.pdf';
    $pdf->Output($nombreArchivo, 'I');
}

/**
 * Obtiene estadísticas de movimientos por período
 * @param array $movimientos Lista de movimientos
 * @return array Estadísticas calculadas
 */
function obtenerEstadisticasMovimientos($movimientos) {
    $total = count($movimientos);
    $movimientosHoy = 0;
    $movimientosSemana = 0;
    $fechaHoy = date('Y-m-d');
    $fechaSemana = date('Y-m-d', strtotime('-7 days'));
    
    foreach ($movimientos as $movimiento) {
        $fechaMovimiento = date('Y-m-d', strtotime($movimiento->fecha));
        
        if ($fechaMovimiento === $fechaHoy) {
            $movimientosHoy++;
        }
        
        if ($fechaMovimiento >= $fechaSemana) {
            $movimientosSemana++;
        }
    }
    
    return [
        'total' => $total,
        'hoy' => $movimientosHoy,
        'semana' => $movimientosSemana,
        'promedio_diario' => $total > 0 ? round($movimientosSemana / 7, 1) : 0
    ];
}

/**
 * Valida y sanitiza los datos de movimientos
 * @param array $movimientos Lista de movimientos
 * @return array Movimientos validados
 */
function validarDatosMovimientos($movimientos) {
    $movimientosValidados = [];
    
    foreach ($movimientos as $movimiento) {
        // Asegurar que existan todas las propiedades necesarias
        $movimientoValidado = (object) [
            'origenname' => $movimiento->origenname ?? 'No especificado',
            'destinoname' => $movimiento->destinoname ?? 'No especificado',
            'usuarioname' => $movimiento->usuarioname ?? 'Usuario desconocido',
            'fecha' => $movimiento->fecha ?? date('Y-m-d'),
            'descripcion' => $movimiento->descripcion ?? 'Sin descripción',
            'institucionname' => $movimiento->institucionname ?? 'N/A'
        ];
        
        $movimientosValidados[] = $movimientoValidado;
    }
    
    return $movimientosValidados;
}



// Imprimir reporte de usuarios
if ($ruta[1] == "imprUsuarios") {
    
    // Obtener datos de usuarios mediante API
    $usuarios = obtenerUsuarios();
    
    if ($usuarios === false) {
        echo "Error al obtener los datos de usuarios";
        exit;
    }
    
    // Generar PDF
    generarPDFUsuarios($usuarios);
    exit;
}

/**
 * Obtiene la lista de usuarios desde la API
 * @return array|false Lista de usuarios o false en caso de error
 */
function obtenerUsuarios() {
    $url = BASE_URL_SERVER . "src/control/Usuario.php?tipo=listarUsuarios&sesion=" . 
           $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'];
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: " . BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        error_log("cURL Error: " . $err);
        return false;
    }
    
    $respuesta = json_decode($response);
    return $respuesta->usuarios ?? false;
}

/**
 * Genera el HTML para el reporte de usuarios
 * @param array $usuarios Lista de usuarios
 * @return string HTML del reporte
 */
function generarHTMLReporteUsuarios($usuarios) {
    // Reutilizar la función existente de fechas
    $fecha = obtenerFechaFormateada(); // Usar la función ya existente
    $fechaHora = date('d/m/Y H:i');
    $totalUsuarios = count($usuarios);
    
    $html = '
<style>
    body {
        font-family: "Arial", sans-serif;
        font-size: 10px;
        color: #1f2937;
        line-height: 1.4;
    }

    .espaciado-superior {
        margin-top: 250px; /* Ajustado */
        text-align: center;
    }

    .titulo-reporte {
        font-size: 18px;
        font-weight: bold;
        color: #1e3a8a;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .report-info {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 12px 15px;
        border-radius: 8px;
        border-left: 4px solid #0ea5e9;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 10px;
    }

    .summary-stats {
        font-size: 11px;
        font-weight: 700;
        color: #0369a1;
        background: white;
        padding: 4px 8px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .main-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
    }

    .main-table thead th {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        font-weight: 600;
        padding: 8px 5px;
        text-align: center;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .main-table tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }

    .main-table tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .main-table tbody td {
        padding: 7px 5px;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: 8px;
    }

    .status-active {
        background-color: #dcfce7;
        color: #166534;
        padding: 2px 6px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 7px;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 2px 6px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 7px;
    }

    .fecha-generacion {
        text-align: right;
        font-size: 10px;
        color: #64748b;
        font-style: italic;
        margin-top: 25px;
        font-weight: 500;
    }

    .firmas-section {
        margin-top: 40px;
        width: 100%;
    }

    .firma-container {
        width: 100%;
        border-collapse: collapse;
    }

    .firma-box {
        text-align: center;
        padding: 20px 10px;
        width: 50%;
        font-size: 10px;
        border: none;
    }

    .firma-line {
        border-bottom: 2px solid #374151;
        width: 180px;
        margin: 0 auto 12px auto;
        height: 1px;
    }

    .firma-text {
        font-weight: 600;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .col-numero { width: 5%; }
    .col-dni { width: 12%; }
    .col-nombres { width: 25%; }
    .col-correo { width: 25%; }
    .col-telefono { width: 12%; }
    .col-estado { width: 10%; }
    .col-registro { width: 11%; }
</style>

<div class="espaciado-superior">
    <div class="titulo-reporte"></div>
    <div class="titulo-reporte"></div>
    <div class="titulo-reporte"></div>

    <div class="titulo-reporte"> Reporte de Usuarios</div>



    <div class="report-info">
        <div>Generado: ' . $fechaHora . '</div>
        <div class="summary-stats">
            Total: <strong>' . $totalUsuarios . ' usuarios</strong>
        </div>
    </div>

    <div class="table-container">
        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-numero">N°</th>
                    <th class="col-dni">DNI</th>
                    <th class="col-nombres">Nombres y Apellidos</th>
                    <th class="col-correo">Correo Electrónico</th>
                    <th class="col-telefono">Teléfono</th>
                    <th class="col-estado">Estado</th>
                    <th class="col-registro">Fecha Registro</th>
                </tr>
            </thead>
            <tbody>';



    // Generar filas de datos
    $contador = 1;
    foreach ($usuarios as $usuario) {
        $estadoClass = ($usuario->estado == 1) ? 'status-active' : 'status-inactive';
        $estadoTexto = ($usuario->estado == 1) ? '✅ Activo' : '❌ Inactivo';
        $fechaRegistro = isset($usuario->fecha_registro) ? 
                        date('d/m/Y', strtotime($usuario->fecha_registro)) : 'N/A';
        
        $html .= '<tr>';
        $html .= '<td><strong>' . $contador . '</strong></td>';
        $html .= '<td>' . htmlspecialchars($usuario->dni ?? '') . '</td>';
        $html .= '<td style="text-align:left; padding-left:8px;">' . 
                 htmlspecialchars($usuario->nombres_apellidos ?? '') . '</td>';
        $html .= '<td style="text-align:left; padding-left:8px;">' . 
                 htmlspecialchars($usuario->correo ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($usuario->telefono ?? '') . '</td>';
        $html .= '<td><span class="' . $estadoClass . '">' . $estadoTexto . '</span></td>';
        $html .= '<td>' . $fechaRegistro . '</td>';
        $html .= '</tr>';
        $contador++;
    }

    $html .= '
                </tbody>
            </table>
        </div>

        <div class="fecha-generacion">
            📍 Ayacucho, ' . $fecha . '
        </div>

        <div class="firmas-section">
            <table class="firma-container">
                <tr>
                    <td class="firma-box">
                        <div class="firma-line"></div>
                        <div class="firma-text">Entregué Conforme</div>
                    </td>
                    <td class="firma-box">
                        <div class="firma-line"></div>
                        <div class="firma-text">Recibí Conforme</div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
    </html>';

    return $html;
}

/**
 * Obtiene la fecha actual formateada en español para usuarios
 * @return string Fecha formateada
 */
function obtenerFechaFormateadaUsuarios() {
    $fecha = new DateTime();
    $dia = $fecha->format('d');
    $año = $fecha->format('Y');
    $mesNumero = (int)$fecha->format('n');
    
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    return $dia . " de " . $meses[$mesNumero] . " del " . $año;
}

/**
 * Configura y genera el PDF de usuarios
 * @param array $usuarios Lista de usuarios
 */
function generarPDFUsuarios($usuarios) {
    $contenido_html = generarHTMLReporteUsuarios($usuarios);
    
    // Configurar PDF
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Metadatos del PDF
    $pdf->SetCreator('Sistema de Gestión');
    $pdf->SetAuthor('Sistema Administrativo');
    $pdf->SetTitle('Reporte de Usuarios - ' . date('d/m/Y'));
    $pdf->SetSubject('Reporte detallado de usuarios del sistema');
    $pdf->SetKeywords('Usuarios, Reporte, Sistema, PDF, Gestión');
    
    // Configurar márgenes optimizados
    $pdf->SetMargins(10, 15, 10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // Agregar página y contenido
    $pdf->AddPage();
    $pdf->writeHTML($contenido_html, true, false, true, false, '');
    
    // Generar nombre de archivo único
    $nombreArchivo = 'Reporte_Usuarios_' . date('Y-m-d_H-i-s') . '.pdf';
    $pdf->Output($nombreArchivo, 'I');
}

/**
 * Obtiene estadísticas básicas de usuarios
 * @param array $usuarios Lista de usuarios
 * @return array Estadísticas calculadas
 */
function obtenerEstadisticasUsuarios($usuarios) {
    $total = count($usuarios);
    $activos = 0;
    $inactivos = 0;
    
    foreach ($usuarios as $usuario) {
        if ($usuario->estado == 1) {
            $activos++;
        } else {
            $inactivos++;
        }
    }
    
    return [
        'total' => $total,
        'activos' => $activos,
        'inactivos' => $inactivos,
        'porcentaje_activos' => $total > 0 ? round(($activos / $total) * 100, 1) : 0
    ];
}

?>