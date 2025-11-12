<?php
require_once "../library/conexion.php";

class ClienteApiModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function listarClientesApi(){
        $respuest = array();
        $sql = $this->conexion->query("SELECT ca.*, c.razon_social 
                                     FROM cliente_api ca 
                                     LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                     ORDER BY ca.fecha_registro DESC");
        while ($objeto = $sql->fetch_object()) {
            array_push($respuest, $objeto);
        }
        return $respuest;
    }
    
    public function registrarClienteApi($id_cliente, $nombre, $descripcion, $ip_permisos)
    {
        $sql = $this->conexion->query("INSERT INTO cliente_api (id_cliente, nombre, descripcion, ip_permisos) 
                                     VALUES ('$id_cliente','$nombre','$descripcion','$ip_permisos')");
        if ($sql) {
            $sql = $this->conexion->insert_id;
        } else {
            $sql = 0;
        }
        return $sql;
    }
    
    public function actualizarClienteApi($id, $id_cliente, $nombre, $descripcion, $ip_permisos, $estado)
    {
        $sql = $this->conexion->query("UPDATE cliente_api SET 
                                    id_cliente='$id_cliente',
                                    nombre='$nombre',
                                    descripcion='$descripcion',
                                    ip_permisos='$ip_permisos',
                                    estado='$estado'
                                    WHERE id='$id'");
        return $sql;
    }
    
    public function buscarClienteApiById($id)
    {
        $sql = $this->conexion->query("SELECT ca.*, c.razon_social 
                                     FROM cliente_api ca 
                                     LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                     WHERE ca.id='$id'");
        $sql = $sql->fetch_object();
        return $sql;
    }
    
    public function buscarClienteApiByNombre($nombre)
    {
        $sql = $this->conexion->query("SELECT * FROM cliente_api WHERE nombre='$nombre'");
        $sql = $sql->fetch_object();
        return $sql;
    }
    
    public function buscarClientesApiOrderByNombre_tabla_filtro($busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado)
    {
        $condicion = " ca.nombre LIKE '%$busqueda_tabla_nombre%'";
        if ($busqueda_tabla_cliente != '0') {
            $condicion .= " AND ca.id_cliente = '$busqueda_tabla_cliente'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND ca.estado = '$busqueda_tabla_estado'";
        }
        
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT ca.*, c.razon_social 
                                           FROM cliente_api ca 
                                           LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                           WHERE $condicion 
                                           ORDER BY ca.nombre");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }
    
    public function buscarClientesApiOrderByNombre_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_nombre, $busqueda_tabla_cliente, $busqueda_tabla_estado)
    {
        $condicion = " ca.nombre LIKE '%$busqueda_tabla_nombre%'";
        if ($busqueda_tabla_cliente != '0') {
            $condicion .= " AND ca.id_cliente = '$busqueda_tabla_cliente'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND ca.estado = '$busqueda_tabla_estado'";
        }
        
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT ca.*, c.razon_social 
                                           FROM cliente_api ca 
                                           LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                           WHERE $condicion 
                                           ORDER BY ca.nombre 
                                           LIMIT $iniciar, $cantidad_mostrar");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }
}