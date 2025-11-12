<?php
// AGREGAR ESTAS 3 LÍNEAS AL INICIO
header('Content-Type: application/json');
ob_start();
error_reporting(0);

session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-clienteApiModel.php');
require_once('../model/admin-clienteModel.php');
$tipo = $_GET['tipo'];

//instanciar las clases
$objSesion = new SessionModel();
$objClienteApi = new ClienteApiModel();
$objCliente = new ClienteModel();

//variables de sesion
$id_sesion = $_REQUEST['sesion'];
$token = $_REQUEST['token'];

if ($tipo == "listar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $arr_ClienteApi = $objClienteApi->listarClientesApi();
        $arr_contenido = [];
        if (!empty($arr_ClienteApi)) {
            // recorremos el array para agregar las opciones de los clientes API
            for ($i = 0; $i < count($arr_ClienteApi); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_ClienteApi[$i]->id;
                $arr_contenido[$i]->nombre = $arr_ClienteApi[$i]->nombre;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "listar_clientes_api_ordenados_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_nombre = $_POST['busqueda_tabla_nombre'];
        $busqueda_tabla_cliente = $_POST['busqueda_tabla_cliente'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objClienteApi->buscarClientesApiOrderByNombre_tabla_filtro($busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado);
        $arr_ClienteApi = $objClienteApi->buscarClientesApiOrderByNombre_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_ClienteApi)) {
            // recorremos el array para agregar las opciones de los clientes API
            for ($i = 0; $i < count($arr_ClienteApi); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_ClienteApi[$i]->id;
                $arr_contenido[$i]->nombre = $arr_ClienteApi[$i]->nombre;
                $arr_contenido[$i]->descripcion = $arr_ClienteApi[$i]->descripcion;
                $arr_contenido[$i]->razon_social = $arr_ClienteApi[$i]->razon_social;
                $arr_contenido[$i]->ip_permisos = $arr_ClienteApi[$i]->ip_permisos;
                $arr_contenido[$i]->estado = $arr_ClienteApi[$i]->estado;
                $arr_contenido[$i]->fecha_registro = $arr_ClienteApi[$i]->fecha_registro;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar_cliente_api' . $arr_ClienteApi[$i]->id . '"><i class="fa fa-edit"></i></button>';
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
            $id_cliente = $_POST['id_cliente'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $ip_permisos = $_POST['ip_permisos'];

            if ($id_cliente == "" || $nombre == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $arr_ClienteApi = $objClienteApi->buscarClienteApiByNombre($nombre);
                if ($arr_ClienteApi) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Registro Fallido, Cliente API con este nombre ya se encuentra registrado');
                } else {
                    $id_cliente_api = $objClienteApi->registrarClienteApi($id_cliente, $nombre, $descripcion, $ip_permisos);
                    if ($id_cliente_api > 0) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente API registrado exitosamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar cliente API');
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
            $id_cliente = $_POST['id_cliente'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $ip_permisos = $_POST['ip_permisos'];
            $estado = $_POST['estado'];

            if ($id == "" || $id_cliente == "" || $nombre == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $arr_ClienteApi = $objClienteApi->buscarClienteApiByNombre($nombre);
                if ($arr_ClienteApi) {
                    if ($arr_ClienteApi->id == $id) {
                        $consulta = $objClienteApi->actualizarClienteApi($id, $id_cliente, $nombre, $descripcion, $ip_permisos, $estado);
                        if ($consulta) {
                            $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente API actualizado correctamente');
                        } else {
                            $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar cliente API');
                        }
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Nombre ya está registrado en otro cliente API');
                    }
                } else {
                    $consulta = $objClienteApi->actualizarClienteApi($id, $id_cliente, $nombre, $descripcion, $ip_permisos, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente API actualizado correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar cliente API');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if($tipo == "listarTodosClientesApi"){
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {  
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $resClienteApi = $objClienteApi->listarClientesApi();
        $arr_contenido = [];
        if (!empty($resClienteApi)) {
            for ($i = 0; $i < count($resClienteApi); $i++) {
                $arr_contenido[$i] = (object) [];
                $arr_contenido[$i]->id = $resClienteApi[$i]->id;
                $arr_contenido[$i]->id_cliente = $resClienteApi[$i]->id_cliente;
                $arr_contenido[$i]->nombre = $resClienteApi[$i]->nombre;
                $arr_contenido[$i]->descripcion = $resClienteApi[$i]->descripcion;
                $arr_contenido[$i]->razon_social = $resClienteApi[$i]->razon_social;
                $arr_contenido[$i]->ip_permisos = $resClienteApi[$i]->ip_permisos;
                $arr_contenido[$i]->estado = $resClienteApi[$i]->estado;
                $arr_contenido[$i]->fecha_registro = $resClienteApi[$i]->fecha_registro;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}