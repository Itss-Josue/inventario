<?php
session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-bienModel.php');
require_once('../model/admin-ingresoModel.php');
require_once('../model/admin-ambienteModel.php');
require_once('../model/adminModel.php');

$tipo = $_GET['tipo'] ?? '';

// Instanciar clases
$objSesion = new SessionModel();
$objBien = new BienModel();
$objIngreso = new IngresoModel();
$objAmbiente = new AmbienteModel();
$objAdmin = new AdminModel();

// Variables de sesión
$id_sesion = $_POST['sesion'] ?? $_GET['sesion'] ?? '';
$token = $_POST['token'] ?? $_GET['token'] ?? '';

// Función para validar sesión
function validarSesion($objSesion, $id_sesion, $token) {
    return $objSesion->verificar_sesion_si_activa($id_sesion, $token);
}

// Función para respuesta JSON estandarizada
function respuestaJson($status, $mensaje = '', $datos = []) {
    $respuesta = [
        'status' => $status,
        'mensaje' => $mensaje
    ];
    
    if (!empty($datos)) {
        $respuesta = array_merge($respuesta, $datos);
    }
    
    return json_encode($respuesta);
}

switch ($tipo) {
    case "buscar_bien_movimiento":
        $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
        
        if (validarSesion($objSesion, $id_sesion, $token)) {
            $ies = $_POST['ies'] ?? '';
            $dato_busqueda = $_POST['dato_busqueda'] ?? '';
            $ambiente = $_POST['ambiente'] ?? '';
            
            $arr_Bienes = $objBien->buscarBienes_filtro($dato_busqueda, $ambiente);
            $arr_contenido = [];
            $arr_Ambientes = $objAmbiente->buscarAmbienteByInstitucion($ies);
            
            $arr_Respuesta['ambientes'] = $arr_Ambientes;
            
            if (!empty($arr_Bienes)) {
                foreach ($arr_Bienes as $i => $bien) {
                    $arr_contenido[$i] = (object) [
                        'id' => $bien->id,
                        'id_ambiente' => $bien->id_ambiente,
                        'cod_patrimonial' => $bien->cod_patrimonial,
                        'denominacion' => $bien->denominacion,
                        'options' => '<button type="button" title="Agregar" class="btn btn-success waves-effect waves-light" onclick="agregar_bien_movimiento(' . $bien->id . ');"><i class="fa fa-plus"></i></button>'
                    ];
                }
                $arr_Respuesta['status'] = true;
                $arr_Respuesta['contenido'] = $arr_contenido;
            }
        }
        echo json_encode($arr_Respuesta);
        break;

    case "listar_bienes_ordenados_tabla":
        $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
        
        if (validarSesion($objSesion, $id_sesion, $token)) {
            $ies = $_POST['ies'] ?? '';
            $pagina = (int)($_POST['pagina'] ?? 1);
            $cantidad_mostrar = (int)($_POST['cantidad_mostrar'] ?? 10);
            $busqueda_tabla_codigo = $_POST['busqueda_tabla_codigo'] ?? '';
            $busqueda_tabla_ambiente = $_POST['busqueda_tabla_ambiente'] ?? '';
            $busqueda_tabla_denominacion = $_POST['busqueda_tabla_denominacion'] ?? '';
            
            $busqueda_filtro = $objBien->buscarBienesOrderByDenominacion_tabla_filtro(
                $busqueda_tabla_codigo, 
                $busqueda_tabla_ambiente, 
                $busqueda_tabla_denominacion, 
                $ies
            );
            
            $arr_Bienes = $objBien->buscarBienesOrderByDenominacion_tabla(
                $pagina, 
                $cantidad_mostrar, 
                $busqueda_tabla_codigo, 
                $busqueda_tabla_ambiente, 
                $busqueda_tabla_denominacion, 
                $ies
            );
            
            $arr_contenido = [];
            $arr_Ambientes = $objAmbiente->buscarAmbienteByInstitucion($ies);
            
            $arr_Respuesta['ambientes'] = $arr_Ambientes;
            
            if (!empty($arr_Bienes)) {
                foreach ($arr_Bienes as $i => $bien) {
                    $arr_contenido[$i] = (object) [
                        'id' => $bien->id,
                        'id_ambiente' => $bien->id_ambiente,
                        'cod_patrimonial' => $bien->cod_patrimonial,
                        'denominacion' => $bien->denominacion,
                        'marca' => $bien->marca,
                        'modelo' => $bien->modelo,
                        'tipo' => $bien->tipo,
                        'color' => $bien->color,
                        'serie' => $bien->serie,
                        'dimensiones' => $bien->dimensiones,
                        'valor' => $bien->valor,
                        'situacion' => $bien->situacion,
                        'estado_conservacion' => $bien->estado_conservacion,
                        'observaciones' => $bien->observaciones,
                        'options' => '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar' . $bien->id . '"><i class="fa fa-edit"></i></button>'
                    ];
                }
                $arr_Respuesta['total'] = count($busqueda_filtro);
                $arr_Respuesta['status'] = true;
                $arr_Respuesta['contenido'] = $arr_contenido;
            }
        }
        echo json_encode($arr_Respuesta);
        break;

    case "registrar":
        $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
        
        if (validarSesion($objSesion, $id_sesion, $token)) {
            $descripcion = trim($_POST['descripcion'] ?? '');
            $bienes = json_decode($_POST['bienes'] ?? '[]', true);

            if (empty($descripcion) || count($bienes) < 1) {
                echo respuestaJson(false, 'Error, campos vacíos y/o no existe bienes para registrar');
                break;
            }

            $arr_usuario = $objSesion->buscarSesionLoginById($id_sesion);
            $id_usuario = $arr_usuario->id_usuario;
            
            $id_ingreso = $objIngreso->registrarIngreso($descripcion, $id_usuario);
            
            if ($id_ingreso > 0) {
                $contar_errores = 0;
                $bienes_duplicados = [];
                
                foreach ($bienes as $bien) {
                    // Validar campos requeridos
                    $campos_requeridos = [
                        'ambiente', 'denominacion', 'marca', 'modelo', 'tipo', 
                        'color', 'serie', 'dimensiones', 'valor', 'situacion', 
                        'estado_conservacion', 'observaciones'
                    ];
                    
                    $campos_vacios = [];
                    foreach ($campos_requeridos as $campo) {
                        if (empty(trim($bien[$campo] ?? ''))) {
                            $campos_vacios[] = $campo;
                        }
                    }
                    
                    if (!empty($campos_vacios)) {
                        echo respuestaJson(false, 'Error, campos vacíos: ' . implode(', ', $campos_vacios));
                        exit;
                    }
                    
                    $cod_patrimonial = trim($bien['cod_patrimonial'] ?? '');
                    
                    // Verificar duplicados solo si hay código patrimonial
                    if (!empty($cod_patrimonial)) {
                        $arr_bien = $objBien->buscarBienByCodigoPatrimonial($cod_patrimonial);
                        if ($arr_bien) {
                            $bienes_duplicados[] = $cod_patrimonial;
                            continue;
                        }
                    }
                    
                    $id_bien = $objBien->registrarBien(
                        $bien['ambiente'], $cod_patrimonial, $bien['denominacion'],
                        $bien['marca'], $bien['modelo'], $bien['tipo'],
                        $bien['color'], $bien['serie'], $bien['dimensiones'],
                        $bien['valor'], $bien['situacion'], $bien['estado_conservacion'],
                        $bien['observaciones'], $id_usuario, $id_ingreso
                    );
                    
                    if ($id_bien == 0) {
                        $contar_errores++;
                    }
                }
                
                if (!empty($bienes_duplicados)) {
                    echo respuestaJson(false, 'Códigos patrimoniales duplicados: ' . implode(', ', $bienes_duplicados));
                } elseif ($contar_errores > 0) {
                    echo respuestaJson(false, 'Error al registrar ' . $contar_errores . ' bienes');
                } else {
                    echo respuestaJson(true, 'Registro exitoso');
                }
            } else {
                echo respuestaJson(false, 'Error al registrar ingreso');
            }
        } else {
            echo json_encode($arr_Respuesta);
        }
        break;

    case "actualizar":
        $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
        
        if (validarSesion($objSesion, $id_sesion, $token)) {
            if ($_POST) {
                $id = (int)($_POST['data'] ?? 0);
                $cod_patrimonial = trim($_POST['cod_patrimonial'] ?? '');
                $denominacion = trim($_POST['denominacion'] ?? '');
                $marca = trim($_POST['marca'] ?? '');
                $modelo = trim($_POST['modelo'] ?? '');
                $tipo = trim($_POST['tipo'] ?? '');
                $color = trim($_POST['color'] ?? '');
                $serie = trim($_POST['serie'] ?? '');
                $dimensiones = trim($_POST['dimensiones'] ?? '');
                $valor = trim($_POST['valor'] ?? '');
                $situacion = trim($_POST['situacion'] ?? '');
                $estado_conservacion = trim($_POST['estado_conservacion'] ?? '');
                $observaciones = trim($_POST['observaciones'] ?? '');
                
                // Validar campos requeridos
                if (empty($denominacion) || empty($marca) || empty($modelo) || 
                    empty($tipo) || empty($color) || empty($serie) || 
                    empty($dimensiones) || empty($valor) || empty($situacion) || 
                    empty($estado_conservacion) || empty($observaciones)) {
                    echo respuestaJson(false, 'Error, campos vacíos');
                    break;
                }
                
                // Verificar código patrimonial duplicado
                if (!empty($cod_patrimonial)) {
                    $arr_Bien = $objBien->buscarBienByCodigoPatrimonial($cod_patrimonial);
                    if ($arr_Bien && $arr_Bien->id != $id) {
                        echo respuestaJson(false, 'Código patrimonial ya está registrado');
                        break;
                    }
                }
                
                $consulta = $objBien->actualizarBien(
                    $id, $cod_patrimonial, $denominacion, $marca, $modelo, $tipo,
                    $color, $serie, $dimensiones, $valor, $situacion, 
                    $estado_conservacion, $observaciones
                );
                
                if ($consulta) {
                    echo respuestaJson(true, 'Actualizado correctamente');
                } else {
                    echo respuestaJson(false, 'Error al actualizar registro');
                }
            }
        } else {
            echo json_encode($arr_Respuesta);
        }
        break;

    case "listarBienes":
        $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
        
        if (validarSesion($objSesion, $id_sesion, $token)) {
            try {
                $arr_Bienes = $objBien->listarBienes();
                echo respuestaJson(true, 'Datos obtenidos correctamente', ['bienes' => $arr_Bienes]);
            } catch (Exception $e) {
                echo respuestaJson(false, 'Error al obtener los datos: ' . $e->getMessage());
            }
        } else {
            echo json_encode($arr_Respuesta);
        }
        break;

    case "listarBienesCompleto":
        $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
        
        if (validarSesion($objSesion, $id_sesion, $token)) {
            try {
                $filtros = [
                    'ies' => $_GET['ies'] ?? '',
                    'ambiente' => $_GET['ambiente'] ?? '',
                    'codigo' => $_GET['codigo'] ?? '',
                    'denominacion' => $_GET['denominacion'] ?? ''
                ];
                
                $arr_Bienes = $objBien->listarBienesCompleto($filtros);
                echo respuestaJson(true, 'Datos obtenidos correctamente', ['bienes' => $arr_Bienes]);
            } catch (Exception $e) {
                echo respuestaJson(false, 'Error al obtener los datos: ' . $e->getMessage());
            }
        } else {
            echo json_encode($arr_Respuesta);
        }
        break;

    default:
        echo respuestaJson(false, 'Tipo de operación no válido');
        break;
}
?>