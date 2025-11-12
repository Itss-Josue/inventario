<?php
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-proyectoModel.php');
require_once('../model/admin-clienteModel.php');
$tipo = $_GET['tipo'];

//instanciar las clases
$objSesion = new SessionModel();
$objProyecto = new ProyectoModel();
$objCliente = new ClienteModel();

//variables de sesion
$id_sesion = $_REQUEST['sesion'];
$token = $_REQUEST['token'];

if ($tipo == "listar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $arr_Proyecto = $objProyecto->listarProyectos();
        $arr_contenido = [];
        if (!empty($arr_Proyecto)) {
            // recorremos el array para agregar las opciones de los proyectos
            for ($i = 0; $i < count($arr_Proyecto); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Proyecto[$i]->id;
                $arr_contenido[$i]->nombre = $arr_Proyecto[$i]->nombre;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "listar_proyectos_ordenados_tabla") {
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
        $busqueda_filtro = $objProyecto->buscarProyectosOrderByNombre_tabla_filtro($busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado);
        $arr_Proyecto = $objProyecto->buscarProyectosOrderByNombre_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_Proyecto)) {
            // recorremos el array para agregar las opciones de los proyectos
            for ($i = 0; $i < count($arr_Proyecto); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Proyecto[$i]->id;
                $arr_contenido[$i]->nombre = $arr_Proyecto[$i]->nombre;
                $arr_contenido[$i]->descripcion = $arr_Proyecto[$i]->descripcion;
                $arr_contenido[$i]->razon_social = $arr_Proyecto[$i]->razon_social;
                $arr_contenido[$i]->fecha_inicio = $arr_Proyecto[$i]->fecha_inicio;
                $arr_contenido[$i]->fecha_fin = $arr_Proyecto[$i]->fecha_fin;
                $arr_contenido[$i]->presupuesto = $arr_Proyecto[$i]->presupuesto;
                $arr_contenido[$i]->estado = $arr_Proyecto[$i]->estado;
                $arr_contenido[$i]->fecha_registro = $arr_Proyecto[$i]->fecha_registro;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar_proyecto' . $arr_Proyecto[$i]->id . '"><i class="fa fa-edit"></i></button>';
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
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            $presupuesto = $_POST['presupuesto'];
            $estado = $_POST['estado'];
            $usuario_registro = $_POST['usuario_registro'];

            if ($id_cliente == "" || $nombre == "" || $fecha_inicio == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $id_proyecto = $objProyecto->registrarProyecto($id_cliente, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $presupuesto, $estado, $usuario_registro);
                if ($id_proyecto > 0) {
                    $arr_Respuesta = array('status' => true, 'mensaje' => 'Proyecto registrado exitosamente');
                } else {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar proyecto');
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
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            $presupuesto = $_POST['presupuesto'];
            $estado = $_POST['estado'];

            if ($id == "" || $id_cliente == "" || $nombre == "" || $fecha_inicio == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos obligatorios vacíos');
            } else {
                $consulta = $objProyecto->actualizarProyecto($id, $id_cliente, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $presupuesto, $estado);
                if ($consulta) {
                    $arr_Respuesta = array('status' => true, 'mensaje' => 'Proyecto actualizado correctamente');
                } else {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar proyecto');
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if($tipo == "listarTodosProyectos"){
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {  
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $resProyecto = $objProyecto->listarProyectos();
        $arr_contenido = [];
        if (!empty($resProyecto)) {
            for ($i = 0; $i < count($resProyecto); $i++) {
                $arr_contenido[$i] = (object) [];
                $arr_contenido[$i]->id = $resProyecto[$i]->id;
                $arr_contenido[$i]->id_cliente = $resProyecto[$i]->id_cliente;
                $arr_contenido[$i]->nombre = $resProyecto[$i]->nombre;
                $arr_contenido[$i]->descripcion = $resProyecto[$i]->descripcion;
                $arr_contenido[$i]->razon_social = $resProyecto[$i]->razon_social;
                $arr_contenido[$i]->fecha_inicio = $resProyecto[$i]->fecha_inicio;
                $arr_contenido[$i]->fecha_fin = $resProyecto[$i]->fecha_fin;
                $arr_contenido[$i]->presupuesto = $resProyecto[$i]->presupuesto;
                $arr_contenido[$i]->estado = $resProyecto[$i]->estado;
                $arr_contenido[$i]->fecha_registro = $resProyecto[$i]->fecha_registro;
            }
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}