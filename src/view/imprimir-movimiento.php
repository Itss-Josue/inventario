<?php 
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL . "movimientos");
}

// Obtener datos desde API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=" . $ruta[1],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
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

    require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

    class MYPDF extends TCPDF {
        public function Header() {
            $logoIzq = __DIR__ . '/../../img/LogoAri.png';
            $logoDer = __DIR__ . '/../../img/LogoAyacucho.png';

            if (file_exists($logoIzq)) {
                $this->Image($logoIzq, 15, 10, 25, '', 'PNG');
            }

            if (file_exists($logoDer)) {
                $this->Image($logoDer, 170, 10, 25, '', 'PNG');
            }

            $this->SetY(12);
            $this->SetFont('helvetica', 'B', 10);
            $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN AYACUCHO', 0, 1, 'C');
            $this->SetFont('helvetica', '', 10);
            $this->Cell(0, 5, 'OFICINA DE ADMINISTRACIÓN', 0, 1, 'C');
            $this->Cell(0, 5, 'UNIDAD DE RECURSOS HUMANOS', 0, 1, 'C');
        }

        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 10, 'Dirección Regional de Educación - Ayacucho | Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C');
        }
    }

    // Crear PDF
    $pdf = new MYPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('GOMEZ');
    $pdf->SetTitle('Papeleta de Rotación de Bienes');
    $pdf->SetMargins(15, 60, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    // Contenido HTML
    $html = '
<style>
    h1 {
        text-align: center;
        font-size: 16pt;
        margin-top: 10px;
        text-transform: uppercase;
    }
    .info { margin-top: 10px; line-height: 1.6; font-size: 11pt; }
    .info strong { display: inline-block; width: 110px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th {
        border: 1px solid #000;
        padding: 5px;
        font-size: 10pt;
        text-align: center;
        font-weight: bold;
        background-color: #f5f5f5;
    }
    td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 10pt;
        text-align: center;
    }
    .pie-fecha { text-align: right; margin-top: 30px; font-size: 10pt; }
.firma-unica {
    margin-top: 100px;
    text-align: center;
    font-size: 11pt;
    line-height: 1.3;
}
.firma-linea {
    border-top: 1px solid #000;
    width: 120px;
    margin: 0 auto 5px auto;
    height: 1px;
}

</style>

<h1>Papeleta de Rotación de Bienes</h1>

<div class="info">
  <div><strong>ENTIDAD:</strong> DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</div>
  <div><strong>ÁREA:</strong> OFICINA DE ADMINISTRACIÓN</div>
  <div><strong>ORIGEN:</strong> '.$respuesta->amb_origen->codigo.' - '.$respuesta->amb_origen->detalle.'</div>
  <div><strong>DESTINO:</strong> '.$respuesta->amb_destino->codigo.' - '.$respuesta->amb_destino->detalle.'</div>
  <div><strong>DESCRIPCIÓN:</strong> '.$respuesta->movimiento->descripcion.'</div>
</div>

<table>
    <thead>
        <tr>
            <th>ITEM</th>
            <th>CÓDIGO PATRIMONIAL</th>
            <th>NOMBRE DEL BIEN</th>
            <th>MARCA</th>
            <th>COLOR</th>
            <th>MODELO</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>';



    $contador = 1;
    foreach ($respuesta->detalle as $bien) {
        $html .= "<tr>
            <td>" . str_pad($contador, 2, "0", STR_PAD_LEFT) . "</td>
            <td>" . htmlspecialchars($bien->cod_patrimonial) . "</td>
            <td>" . htmlspecialchars($bien->denominacion) . "</td>
            <td>" . htmlspecialchars($bien->marca) . "</td>
            <td>" . htmlspecialchars($bien->color) . "</td>
            <td>" . htmlspecialchars($bien->modelo) . "</td>
            <td>" . htmlspecialchars($bien->estado_conservacion) . "</td>
        </tr>";
        $contador++;
    }

    $html .= '</tbody></table>';

    $html .= '
    <div class="pie-fecha">
        Ayacucho, ' . date("j") . ' de ' . ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","setiembre","octubre","noviembre","diciembre"][date("n")-1] . ' de ' . date("Y") . '
    </div>
      <div class="firma-unica">
      <div class="firma-linea"></div>
      <div>RESPONSABLE</div>
      </div>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('papeleta_movimiento.pdf', 'I');
}
?>