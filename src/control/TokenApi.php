<?php
// AGREGAR ESTAS 3 LÍNEAS AL INICIO
header('Content-Type: application/json');
ob_start();
error_reporting(0);

session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-tokenApiModel.php');
require_once('../model/admin-clienteApiModel.php');
$tipo = $_GET['tipo'];

//instanciar las clases
$objSesion = new SessionModel();
$objTokenApi = new TokenApiModel();
$objClienteApi = new ClienteApiModel();

//variables de sesion
$id_sesion = $_REQUEST['sesion'];
$token = $_REQUEST['token'];

if ($tipo == "listar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $arr_TokenApi = $objTokenApi->listarTokensApi();
        $arr_contenido = [];
        if (!empty($arr_TokenApi)) {
            // recorremos el array para agregar las opciones de los tokens
            for ($i = 0; $i < count($arr_TokenApi); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_TokenApi[$i]->id;
                $arr_contenido[$i]->token = $arr_TokenApi[$i]->token;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "listar_tokens_api_ordenados_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_cliente_api = $_POST['busqueda_tabla_cliente_api'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objTokenApi->buscarTokensApiOrderByFecha_tabla_filtro($busqueda_tabla_cliente_api, $busqueda_tabla_estado);
        $arr_TokenApi = $objTokenApi->buscarTokensApiOrderByFecha_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_cliente_api, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_TokenApi)) {
            // recorremos el array para agregar las opciones de los tokens
            for ($i = 0; $i < count($arr_TokenApi); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_TokenApi[$i]->id;
                $arr_contenido[$i]->token = $arr_TokenApi[$i]->token;
                $arr_contenido[$i]->cliente_api_nombre = $arr_TokenApi[$i]->cliente_api_nombre;
                $arr_contenido[$i]->razon_social = $arr_TokenApi[$i]->razon_social;
                $arr_contenido[$i]->fecha_creacion = $arr_TokenApi[$i]->fecha_creacion;
                $arr_contenido[$i]->fecha_expiracion = $arr_TokenApi[$i]->fecha_expiracion;
                $arr_contenido[$i]->estado = $arr_TokenApi[$i]->estado;
                $arr_contenido[$i]->permisos = $arr_TokenApi[$i]->permisos;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar_token_api' . $arr_TokenApi[$i]->id . '"><i class="fa fa-edit"></i></button>';
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
            $id_cliente_api = $_POST['id_cliente_api'];
            $fecha_expiracion = $_POST['fecha_expiracion'];
            $permisos = $_POST['permisos'];

            if ($id_cliente_api == "" || $fecha_expiracion == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                // Generar token único
                $token = $objTokenApi->generarTokenUnico();
                
                $id_token_api = $objTokenApi->registrarTokenApi($id_cliente_api, $token, $fecha_expiracion, $permisos);
                if ($id_token_api > 0) {
                    $arr_Respuesta = array('status' => true, 'mensaje' => 'Token API registrado exitosamente', 'token_generado' => $token);
                } else {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar token API');
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
            $id_cliente_api = $_POST['id_cliente_api'];
            $token = $_POST['token'];
            $fecha_expiracion = $_POST['fecha_expiracion'];
            $permisos = $_POST['permisos'];
            $estado = $_POST['estado'];

            if ($id == "" || $id_cliente_api == "" || $token == "" || $fecha_expiracion == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $consulta = $objTokenApi->actualizarTokenApi($id, $id_cliente_api, $token, $fecha_expiracion, $permisos, $estado);
                if ($consulta) {
                    $arr_Respuesta = array('status' => true, 'mensaje' => 'Token API actualizado correctamente');
                } else {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar token API');
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if($tipo == "listarTodosTokensApi"){
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {  
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $resTokenApi = $objTokenApi->listarTokensApi();
        $arr_contenido = [];
        if (!empty($resTokenApi)) {
            for ($i = 0; $i < count($resTokenApi); $i++) {
                $arr_contenido[$i] = (object) [];
                $arr_contenido[$i]->id = $resTokenApi[$i]->id;
                $arr_contenido[$i]->id_cliente_api = $resTokenApi[$i]->id_cliente_api;
                $arr_contenido[$i]->token = $resTokenApi[$i]->token;
                $arr_contenido[$i]->cliente_api_nombre = $resTokenApi[$i]->cliente_api_nombre;
                $arr_contenido[$i]->razon_social = $resTokenApi[$i]->razon_social;
                $arr_contenido[$i]->fecha_creacion = $resTokenApi[$i]->fecha_creacion;
                $arr_contenido[$i]->fecha_expiracion = $resTokenApi[$i]->fecha_expiracion;
                $arr_contenido[$i]->estado = $resTokenApi[$i]->estado;
                $arr_contenido[$i]->permisos = $resTokenApi[$i]->permisos;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "generar_token") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        $nuevo_token = $objTokenApi->generarTokenUnico();
        $arr_Respuesta = array('status' => true, 'token' => $nuevo_token);
    }
    echo json_encode($arr_Respuesta);
}