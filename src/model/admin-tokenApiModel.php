<?php
require_once "../library/conexion.php";

class TokenApiModel
{
    private $conexion;

    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    public function listarTokensApi()
    {
        $respuest = array();
        $sql = $this->conexion->query("SELECT ta.*, ca.nombre as cliente_api_nombre, c.razon_social 
                                     FROM token_api ta 
                                     LEFT JOIN cliente_api ca ON ta.id_cliente_api = ca.id 
                                     LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                     ORDER BY ta.fecha_creacion DESC");
        while ($objeto = $sql->fetch_object()) {
            array_push($respuest, $objeto);
        }
        return $respuest;
    }

    public function registrarTokenApi($id_cliente_api, $token, $fecha_expiracion, $permisos)
    {
        $sql = $this->conexion->query("INSERT INTO token_api (id_cliente_api, token, fecha_expiracion, permisos) 
                                     VALUES ('$id_cliente_api','$token','$fecha_expiracion','$permisos')");
        if ($sql) {
            $sql = $this->conexion->insert_id;
        } else {
            $sql = 0;
        }
        return $sql;
    }

    public function actualizarTokenApi($id, $id_cliente_api, $token, $fecha_expiracion, $permisos, $estado)
    {
        $sql = $this->conexion->query("UPDATE token_api SET 
                                    id_cliente_api='$id_cliente_api',
                                    token='$token',
                                    fecha_expiracion='$fecha_expiracion',
                                    permisos='$permisos',
                                    estado='$estado'
                                    WHERE id='$id'");
        return $sql;
    }

    public function buscarTokenApiById($id)
    {
        $sql = $this->conexion->query("SELECT ta.*, ca.nombre as cliente_api_nombre, c.razon_social 
                                     FROM token_api ta 
                                     LEFT JOIN cliente_api ca ON ta.id_cliente_api = ca.id 
                                     LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                     WHERE ta.id='$id'");
        $sql = $sql->fetch_object();
        return $sql;
    }

    public function buscarTokenApiByToken($token)
    {
        $sql = $this->conexion->query("SELECT * FROM token_api WHERE token='$token'");
        $sql = $sql->fetch_object();
        return $sql;
    }

    public function buscarTokensApiOrderByFecha_tabla_filtro($busqueda_tabla_cliente_api, $busqueda_tabla_estado)
    {
        $condicion = " 1=1";
        if ($busqueda_tabla_cliente_api != '0') {
            $condicion .= " AND ta.id_cliente_api = '$busqueda_tabla_cliente_api'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND ta.estado = '$busqueda_tabla_estado'";
        }

        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT ta.*, ca.nombre as cliente_api_nombre, c.razon_social 
                                           FROM token_api ta 
                                           LEFT JOIN cliente_api ca ON ta.id_cliente_api = ca.id 
                                           LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                           WHERE $condicion 
                                           ORDER BY ta.fecha_creacion DESC");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }

    public function buscarTokensApiOrderByFecha_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_cliente_api, $busqueda_tabla_estado)
    {
        $condicion = " 1=1";
        if ($busqueda_tabla_cliente_api != '0') {
            $condicion .= " AND ta.id_cliente_api = '$busqueda_tabla_cliente_api'";
        }
        if ($busqueda_tabla_estado != '') {
            $condicion .= " AND ta.estado = '$busqueda_tabla_estado'";
        }

        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $arrRespuesta = array();
        $respuesta = $this->conexion->query("SELECT ta.*, ca.nombre as cliente_api_nombre, c.razon_social 
                                           FROM token_api ta 
                                           LEFT JOIN cliente_api ca ON ta.id_cliente_api = ca.id 
                                           LEFT JOIN clientes c ON ca.id_cliente = c.id 
                                           WHERE $condicion 
                                           ORDER BY ta.fecha_creacion DESC 
                                           LIMIT $iniciar, $cantidad_mostrar");
        while ($objeto = $respuesta->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }

    public function generarTokenUnico()
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-+=[]{}|;:,.<>?';
        $longitud = 32;
        $token = '';

        for ($i = 0; $i < $longitud; $i++) {
            $token .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        // Verificar que el token sea único
        $token_existente = $this->buscarTokenApiByToken($token);
        if ($token_existente) {
            return $this->generarTokenUnico(); // Recursivamente generar otro token
        }

        return $token;
    }
}
