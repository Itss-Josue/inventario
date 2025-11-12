<?php
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-clienteModel.php');
require_once('../model/adminModel.php');
$tipo = $_GET['tipo'];

//instanciar la clase categoria model
$objSesion = new SessionModel();
$objCliente = new ClienteModel();
$objAdmin = new AdminModel();

//variables de sesion
$id_sesion = $_REQUEST['sesion'];
$token = $_REQUEST['token'];

if ($tipo == "listar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $arr_Cliente = $objCliente->listarClientes();
        $arr_contenido = [];
        if (!empty($arr_Cliente)) {
            // recorremos el array para agregar las opciones de las categorias
            for ($i = 0; $i < count($arr_Cliente); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Cliente[$i]->id;
                $arr_contenido[$i]->razon_social = $arr_Cliente[$i]->razon_social;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "listar_clientes_ordenados_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_ruc = $_POST['busqueda_tabla_ruc'];
        $busqueda_tabla_razon_social = $_POST['busqueda_tabla_razon_social'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objCliente->buscarClientesOrderByRazonSocial_tabla_filtro($busqueda_tabla_ruc, $busqueda_tabla_razon_social, $busqueda_tabla_estado);
        $arr_Cliente = $objCliente->buscarClientesOrderByRazonSocial_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_ruc, $busqueda_tabla_razon_social, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_Cliente)) {
            // recorremos el array para agregar las opciones de las categorias
            for ($i = 0; $i < count($arr_Cliente); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Cliente[$i]->id;
                $arr_contenido[$i]->ruc = $arr_Cliente[$i]->ruc;
                $arr_contenido[$i]->razon_social = $arr_Cliente[$i]->razon_social;
                $arr_contenido[$i]->direccion = $arr_Cliente[$i]->direccion;
                $arr_contenido[$i]->telefono = $arr_Cliente[$i]->telefono;
                $arr_contenido[$i]->correo = $arr_Cliente[$i]->correo;
                $arr_contenido[$i]->contacto = $arr_Cliente[$i]->contacto;
                $arr_contenido[$i]->estado = $arr_Cliente[$i]->estado;
                $arr_contenido[$i]->fecha_registro = $arr_Cliente[$i]->fecha_registro;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar' . $arr_Cliente[$i]->id . '"><i class="fa fa-edit"></i></button>';
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
            $ruc = $_POST['ruc'];
            $razon_social = $_POST['razon_social'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $contacto = $_POST['contacto'];

            if ($ruc == "" || $razon_social == "" || $telefono == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $arr_Cliente = $objCliente->buscarClienteByRuc($ruc);
                if ($arr_Cliente) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Registro Fallido, Cliente con este RUC ya se encuentra registrado');
                } else {
                    $id_cliente = $objCliente->registrarCliente($ruc, $razon_social, $direccion, $telefono, $correo, $contacto);
                    if ($id_cliente > 0) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente registrado exitosamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar cliente');
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
            $id = $_POST['id'];
            $ruc = $_POST['ruc'];
            $razon_social = $_POST['razon_social'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $contacto = $_POST['contacto'];
            $estado = $_POST['estado'];

            if ($id == "" || $ruc == "" || $razon_social == "" || $telefono == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $arr_Cliente = $objCliente->buscarClienteByRuc($ruc);
                if ($arr_Cliente) {
                    if ($arr_Cliente->id == $id) {
                        $consulta = $objCliente->actualizarCliente($id, $ruc, $razon_social, $direccion, $telefono, $correo, $contacto, $estado);
                        if ($consulta) {
                            $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente actualizado correctamente');
                        } else {
                            $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar cliente');
                        }
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'RUC ya está registrado en otro cliente');
                    }
                } else {
                    $consulta = $objCliente->actualizarCliente($id, $ruc, $razon_social, $direccion, $telefono, $correo, $contacto, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Cliente actualizado correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar cliente');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if($tipo == "listarTodosClientes"){
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {  
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $resCliente = $objCliente->listarClientes();
        $arr_contenido = [];
        if (!empty($resCliente)) {
            for ($i = 0; $i < count($resCliente); $i++) {
                $arr_contenido[$i] = (object) [];
                $arr_contenido[$i]->id = $resCliente[$i]->id;
                $arr_contenido[$i]->ruc = $resCliente[$i]->ruc;
                $arr_contenido[$i]->razon_social = $resCliente[$i]->razon_social;
                $arr_contenido[$i]->direccion = $resCliente[$i]->direccion;
                $arr_contenido[$i]->telefono = $resCliente[$i]->telefono;
                $arr_contenido[$i]->correo = $resCliente[$i]->correo;
                $arr_contenido[$i]->contacto = $resCliente[$i]->contacto;
                $arr_contenido[$i]->estado = $resCliente[$i]->estado;
                $arr_contenido[$i]->fecha_registro = $resCliente[$i]->fecha_registro;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}