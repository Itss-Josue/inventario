<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-usuarioModel.php');
require_once('../model/adminModel.php');

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

$tipo = $_GET['tipo'];


//instanciar la clase categoria model
$objSesion = new SessionModel();
$objUsuario = new UsuarioModel();
$objAdmin = new AdminModel();

//variables de sesion
$id_sesion = $_POST['sesion'];
$token = $_POST['token'];

if ($tipo=="validar_datos_reset_password") {

  $id_email = $_POST ['id'];
  $token_email = $_POST ['token'];

  $arr_Respuesta = array('status' => false, 'msg' => 'link caducado');
  $datos_usuario = $objUsuario->buscarUsuarioById($id_email);

  if ($datos_usuario->token_password==1 && password_verify($datos_usuario->token_password,$token_email)) {
  $arr_Respuesta = array('status' => true, 'msg' => 'Ok');

    echo json_encode($arr_Respuesta);
  

  }
  
}



if ($tipo == "listar_usuarios_ordenados_tabla") {

    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_dni = $_POST['busqueda_tabla_dni'];
        $busqueda_tabla_nomap = $_POST['busqueda_tabla_nomap'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla_filtro($busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_Usuario = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_Usuario)) {
            // recorremos el array para agregar las opciones de las categorias
            for ($i = 0; $i < count($arr_Usuario); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Usuario[$i]->id;
                $arr_contenido[$i]->dni = $arr_Usuario[$i]->dni;
                $arr_contenido[$i]->nombres_apellidos = $arr_Usuario[$i]->nombres_apellidos;
                $arr_contenido[$i]->correo = $arr_Usuario[$i]->correo;
                $arr_contenido[$i]->telefono = $arr_Usuario[$i]->telefono;
                $arr_contenido[$i]->estado = $arr_Usuario[$i]->estado;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar' . $arr_Usuario[$i]->id . '"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-info" title="Resetear Contraseña" onclick="reset_password(' . $arr_Usuario[$i]->id . ')"><i class="fa fa-key"></i></button>';
                $arr_contenido[$i]->options = $opciones;
            }
            $arr_Respuesta['total'] = count($busqueda_filtro);
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}



 if ($tipo == "registrar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        //repuesta
        if ($_POST) {
            $dni = $_POST['dni'];
            $apellidos_nombres = $_POST['apellidos_nombres'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $password = $_POST['password'];

            if ($dni == "" || $apellidos_nombres == "" || $correo == "" || $telefono == "" || $password == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Registro Fallido, Usuario ya se encuentra registrado');
                } else {
                    $id_usuario = $objUsuario->registrarUsuario($dni, $apellidos_nombres, $correo, $telefono, $password);
                    if ($id_usuario > 0) {
                        // array con los id de los sistemas al que tendra el acceso con su rol registrado
                        // caso de administrador y director
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Registro Exitoso');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar producto');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "actualizar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        //repuesta
        if ($_POST) {
            $id = $_POST['data'];
            $dni = $_POST['dni'];
            $nombres_apellidos = $_POST['nombres_apellidos'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $estado = $_POST['estado'];

            if ($id == "" || $dni == "" || $nombres_apellidos == "" || $correo == "" || $telefono == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    if ($arr_Usuario->id == $id) {
                        $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                        if ($consulta) {
                            $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                        } else {
                            $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                        }
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'dni ya esta registrado');
                    }
                } else {
                    $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "reiniciar_password") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $id_usuario = $_POST['id'];
        $password = $objAdmin->generar_llave(10);
        $pass_secure = password_hash($password, PASSWORD_DEFAULT);
        $actualizar = $objUsuario->actualizarPassword($id_usuario, $pass_secure);
        if ($actualizar) {
            $arr_Respuesta = array('status' => true, 'mensaje' => 'Contraseña actualizado correctamente a: ' . $password);
        } else {
            $arr_Respuesta = array('status' => false, 'mensaje' => 'Hubo un problema al actualizar la contraseña, intente nuevamente');
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "sent_email_password") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {   
        $datos_sesion = $objSesion->buscarSesionLoginById($id_sesion);
        $datos_usuario = $objUsuario->buscarUsuarioById($datos_sesion->id_usuario);
        $nombreusuario = $datos_usuario->nombres_apellidos;
        $llave = $objAdmin->generar_llave(30);
        $token = password_hash($llave, PASSWORD_DEFAULT);
        $update = $objUsuario->updateResetPassword($datos_sesion->id_usuario, $llave , 1);
        if ($update) {
            

        //Import PHPMailer classes into the global namespace
        //These must be at the top of your script, not inside a function

        //Load Composer's autoloader (created by composer, not included with PHPMailer)
        require '../../vendor/autoload.php';

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'mail.limon-cito.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'josuegomezc21@limon-cito.com';                     //SMTP username
            $mail->Password   = '1?rnmfHpzA&%';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('josuegomezc21@limon-cito.com', 'Cambio de Contraseña:');
            $mail->addAddress($datos_usuario->correo, $datos_usuario->nombres_apellidos);     //Add a recipient
           /* $mail->addAddress('ellen@example.com');               //Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');

            //Attachments
            $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            */
            //Content
            $mail->isHTML(true);    
            $mail->CharSet = 'UTF-8';                              //Set email format to HTML
            $mail->Subject = 'Cambio de contraseña - Sistema de Inventario';
            $mail->Body    = '<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cambio de Contraseña - AriModas</title>
  <style>
    body, p, div, h1, h2, h3, a {
      margin: 0;
      padding: 0;
      font-family: Tahoma, Geneva, Verdana, sans-serif;
      color: #444444;
      line-height: 1.6;
    }
    body {
      background-color: #f4f6f9;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }
    a {
      color: #2563eb;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.07);
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }
    .header {
      background: linear-gradient(90deg,rgb(189, 208, 238) 0%, #2563eb 100%);
      padding: 30px;
      text-align: center;
      color: white;
    }
    .header img {
      max-width: 100px;
      margin-bottom: 10px;
     
    }
    .header h1 {
      font-weight: 700;
      font-size: 24px;
      margin-top: 10px;
    }
    .body {
      padding: 35px 30px 40px;
      font-size: 16px;
      color: #555555;
    }
    .body h2 {
      font-weight: 600;
      font-size: 22px;
      margin-bottom: 18px;
      color: #222222;
    }
    .body p {
      margin-bottom: 18px;
    }
    .btn-primary {
      display: inline-block;
      background-color:rgb(152, 189, 250);
      color: #fff !important;
      padding: 14px 28px;
      font-weight: 600;
      border-radius: 8px;
      font-size: 16px;
      margin: 25px 0;
      text-align: center;
      box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #1d4ed8;
    }
    .footer {
      background-color: #f1f5f9;
      padding: 20px 30px;
      font-size: 13px;
      text-align: center;
      color: #888888;
      border-top: 1px solid #e2e8f0;
    }
    .footer a {
      color: #3b82f6;
    }
    @media screen and (max-width: 620px) {
      .container {
        width: 95% !important;
        margin: 15px auto;
        border-radius: 8px;
      }
      .body {
        padding: 25px 20px 30px;
        font-size: 15px;
      }
      .btn-primary {
        width: 100%;
        box-sizing: border-box;
      }
    }
  </style>
</head>
<body>
  <div class="container" style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
    <!-- Header -->
    <div class="header" style="text-align: center; padding: 20px 0;">
   <img src="https://drive.google.com/uc?export=dowload&id=1zMqvObW5v16DEPqAdJO-M6QeHa9Oh3Wk" alt="Logo AriModas" style="width: 100px;">
      <h1 style="margin: 0; color: #333;">AriModas</h1>
    </div>
    <!-- Body -->
    <div class="body" style="padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
      <h2 >Hola, '.$nombreusuario.' 👋</h2>
      <p>Hemos recibido una solicitud para cambiar tu contraseña. Si fuiste tú quien hizo esta solicitud, puedes continuar el proceso haciendo clic en el botón a continuación.</p>
      <p>Por seguridad, este enlace solo estará disponible durante las próximas 24 horas. Si no realizaste esta solicitud, puedes ignorar este mensaje sin problemas.</p>

      <div style="text-align: center; margin: 30px 0;">
        <a href="'.BASE_URL.'reset-password?data='.$datos_usuario->id.'&data2='.$token.'"class="button">Cambiar contraseña</a>
      </div>  

      <p>Gracias por confiar en nosotros.<br>— El equipo de <strong>AriModas</strong></p>
    </div>

    <!-- Footer -->
    <div class="footer" style="text-align: center; font-size: 13px; color: #888; margin-top: 20px;">
      <p>© 2025 AriModas. Todos los derechos reservados.</p>
    </div>
  </div>
</body>

</html>
';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
            
        }else {
            echo "fallo al actualizar";
        }
        //print_r($token);
    }
}