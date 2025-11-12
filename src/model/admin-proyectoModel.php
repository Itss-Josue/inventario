<?php
require_once "../library/conexion.php";

class ProyectoModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function listarProyectos(){
        $respuest = array();
        $sql = $this->conexion->query("SELECT p.*, c.razon_social 
                                     FROM proyectos p 
                                     LEFT JOIN clientes c ON p.id_cliente = c.id 
                                     ORDER BY p.fecha_registro DESC");
        while ($objeto = $sql->fetch_object()) {
            array_push($respuest, $objeto);
        }
        return $respuest;
    }
    
    public function registrarProyecto($id_cliente, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $presupuesto, $estado, $usuario_registro)
    {
        $sql = $this->conexion->query("INSERT INTO proyectos (id_cliente, nombre, descripcion, fecha_inicio, fecha_fin, presupuesto, estado, usuario_registro) 
                                     VALUES ('$id_cliente','$nombre','$descripcion','$fecha_inicio','$fecha_fin','$presupuesto','$estado','$usuario_registro')");
        if ($sql) {
            $sql = $this->conexion->insert_id;
        } else {
            $sql = 0;
        }
        return $sql;
    }
    
    public function actualizarProyecto($id, $id_cliente, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $presupuesto, $estado)
    {
        $sql = $this->conexion->query("UPDATE proyectos SET 
                                    id_cliente='$id_cliente',
                                    nombre='$nombre',
                                    descripcion='$descripcion',
                                    fecha_inicio='$fecha_inicio',
                                    fecha_fin='$fecha_fin',
                                    presupuesto='$presupuesto',
                                    estado='$estado'
                                    WHERE id='$id'");
        return $sql;
    }
    
    public function buscarProyectoById($id)
    {
        $sql = $this->conexion->query("SELECT p.*, c.razon_social 
                                     FROM proyectos p 
                                     LEFT JOIN clientes c ON p.id_cliente = c.id 
                                     WHERE p.id='$id'");
        $sql = $sql->fetch_object();
        return $sql;
    }
    
    public function buscarProyectosOrderByNombre_tabla_filtro($busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado)
    {
        $condicion = " p.nombre LIKE '%$busqueda_tabla_nombre%'";
        if ($busqueda_tabla_cliente != '0') {
            $condicion .= " AND p.id_cliente = '$busqueda_tabla_cliente'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND p.estado = '$busqueda_tabla_estado'";
        }
        
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT p.*, c.razon_social 
                                           FROM proyectos p 
                                           LEFT JOIN clientes c ON p.id_cliente = c.id 
                                           WHERE $condicion 
                                           ORDER BY p.nombre");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }
    
    public function buscarProyectosOrderByNombre_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado)
    {
        $condicion = " p.nombre LIKE '%$busqueda_tabla_nombre%'";
        if ($busqueda_tabla_cliente != '0') {
            $condicion .= " AND p.id_cliente = '$busqueda_tabla_cliente'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND p.estado = '$busqueda_tabla_estado'";
        }
        
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT p.*, c.razon_social 
                                           FROM proyectos p 
                                           LEFT JOIN clientes c ON p.id_cliente = c.id 
                                           WHERE $condicion 
                                           ORDER BY p.nombre 
                                           LIMIT $iniciar, $cantidad_mostrar");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }
}