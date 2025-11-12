<?php
require_once "../library/conexion.php";

class ClienteModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }
    
    public function listarClientes(){
        $respuest = array();
        $sql = $this->conexion->query("SELECT * FROM clientes");
        while ($objeto = $sql->fetch_object()) {
            array_push($respuest, $objeto);
        }
        return $respuest;
    }
    
    public function registrarCliente($ruc, $razon_social, $direccion, $telefono, $correo, $contacto)
    {
        $sql = $this->conexion->query("INSERT INTO clientes (ruc, razon_social, direccion, telefono, correo, contacto) 
                                     VALUES ('$ruc','$razon_social','$direccion','$telefono','$correo','$contacto')");
        if ($sql) {
            $sql = $this->conexion->insert_id;
        } else {
            $sql = 0;
        }
        return $sql;
    }
    
    public function actualizarCliente($id, $ruc, $razon_social, $direccion, $telefono, $correo, $contacto, $estado)
    {
        $sql = $this->conexion->query("UPDATE clientes SET 
                                    ruc='$ruc',
                                    razon_social='$razon_social',
                                    direccion='$direccion',
                                    telefono='$telefono',
                                    correo='$correo',
                                    contacto='$contacto',
                                    estado='$estado' 
                                    WHERE id='$id'");
        return $sql;
    }
    
    public function buscarClienteById($id)
    {
        $sql = $this->conexion->query("SELECT * FROM clientes WHERE id='$id'");
        $sql = $sql->fetch_object();
        return $sql;
    }
    
    public function buscarClienteByRuc($ruc)
    {
        $sql = $this->conexion->query("SELECT * FROM clientes WHERE ruc='$ruc'");
        $sql = $sql->fetch_object();
        return $sql;
    }
    
    public function buscarClientesOrderByRazonSocial_tabla_filtro($busqueda_tabla_ruc, $busqueda_tabla_razon_social, $busqueda_tabla_estado)
    {
        $condicion = " ruc LIKE '$busqueda_tabla_ruc%' AND razon_social LIKE '$busqueda_tabla_razon_social%'";
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND estado = '$busqueda_tabla_estado'";
        }
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT * FROM clientes WHERE $condicion ORDER BY razon_social");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }
    
    public function buscarClientesOrderByRazonSocial_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_ruc, $busqueda_tabla_razon_social, $busqueda_tabla_estado)
    {
        $condicion = " ruc LIKE '$busqueda_tabla_ruc%' AND razon_social LIKE '$busqueda_tabla_razon_social%'";
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND estado = '$busqueda_tabla_estado'";
        }
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT * FROM clientes WHERE $condicion ORDER BY razon_social LIMIT $iniciar, $cantidad_mostrar");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }
}