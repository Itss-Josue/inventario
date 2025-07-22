<?php
require_once "../library/conexion.php";

class BienModel
{
    private $conexion;
    
    function __construct()
    {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->connect();
    }

    public function registrarBien($ambiente, $cod_patrimonial, $denominacion, $marca, $modelo, $tipo, $color, $serie, $dimensiones, $valor, $situacion, $estado_conservacion, $observaciones, $id_usuario, $id_ingreso)
    {
        // Usar prepared statements para prevenir inyección SQL
        $stmt = $this->conexion->prepare("INSERT INTO bienes (id_ingreso_bienes, id_ambiente, cod_patrimonial, denominacion, marca, modelo, tipo, color, serie, dimensiones, valor, situacion, estado_conservacion, observaciones, usuario_registro, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $stmt->bind_param("iissssssssssssi", $id_ingreso, $ambiente, $cod_patrimonial, $denominacion, $marca, $modelo, $tipo, $color, $serie, $dimensiones, $valor, $situacion, $estado_conservacion, $observaciones, $id_usuario);
        
        if ($stmt->execute()) {
            return $this->conexion->insert_id;
        } else {
            return 0;
        }
    }

    public function actualizarBien($id, $cod_patrimonial, $denominacion, $marca, $modelo, $tipo, $color, $serie, $dimensiones, $valor, $situacion, $estado_conservacion, $observaciones)
    {
        $stmt = $this->conexion->prepare("UPDATE bienes SET cod_patrimonial=?, denominacion=?, marca=?, modelo=?, tipo=?, color=?, serie=?, dimensiones=?, valor=?, situacion=?, estado_conservacion=?, observaciones=? WHERE id=?");
        
        $stmt->bind_param("ssssssssssssi", $cod_patrimonial, $denominacion, $marca, $modelo, $tipo, $color, $serie, $dimensiones, $valor, $situacion, $estado_conservacion, $observaciones, $id);
        
        return $stmt->execute();
    }

    public function actualizarBien_Ambiente($id, $nuevo_ambiente)
    {
        $stmt = $this->conexion->prepare("UPDATE bienes SET id_ambiente=? WHERE id=?");
        $stmt->bind_param("ii", $nuevo_ambiente, $id);
        return $stmt->execute();
    }

    public function buscarBienById($id)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM bienes WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }

    public function buscarBienes_filtro($filtro, $ambiente)
    {
        $arrRespuesta = array();
        $stmt = $this->conexion->prepare("SELECT * FROM bienes WHERE (cod_patrimonial LIKE ? OR denominacion LIKE ?) AND id_ambiente=? AND estado = 1");
        $filtro_like = $filtro . '%';
        $filtro_like_denominacion = '%' . $filtro . '%';
        $stmt->bind_param("ssi", $filtro_like, $filtro_like_denominacion, $ambiente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($objeto = $result->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }

    public function buscarBienByCodigoPatrimonial($codigo)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM bienes WHERE cod_patrimonial = ? AND estado = 1");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }

    public function buscarBienByCpdigoInstitucion($codigo, $institucion)
    {
        $stmt = $this->conexion->prepare("SELECT * FROM bienes WHERE codigo=? AND id_ies=? AND estado = 1");
        $stmt->bind_param("si", $codigo, $institucion);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }

    public function buscarBienesOrderByDenominacion_tabla_filtro($busqueda_tabla_codigo, $busqueda_tabla_ambiente, $busqueda_tabla_denominacion, $ies)
    {
        $condicion = " b.cod_patrimonial LIKE ? AND b.denominacion LIKE ? AND b.estado = 1";
        $params = [$busqueda_tabla_codigo . '%', '%' . $busqueda_tabla_denominacion . '%'];
        $types = "ss";
        
        if ($busqueda_tabla_ambiente > 0) {
            $condicion .= " AND b.id_ambiente = ?";
            $params[] = $busqueda_tabla_ambiente;
            $types .= "i";
        }
        
        if ($ies > 0) {
            $condicion .= " AND ai.id_ies = ?";
            $params[] = $ies;
            $types .= "i";
        }
        
        $arrRespuesta = array();
        $stmt = $this->conexion->prepare("SELECT b.id FROM bienes b INNER JOIN ambientes_institucion ai ON b.id_ambiente = ai.id WHERE $condicion ORDER BY b.denominacion");
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($objeto = $result->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }

    public function buscarBienesOrderByDenominacion_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_codigo, $busqueda_tabla_ambiente, $busqueda_tabla_denominacion, $ies)
    {
        $condicion = " b.cod_patrimonial LIKE ? AND b.denominacion LIKE ? AND b.estado = 1";
        $params = [$busqueda_tabla_codigo . '%', '%' . $busqueda_tabla_denominacion . '%'];
        $types = "ss";
        
        if ($busqueda_tabla_ambiente > 0) {
            $condicion .= " AND b.id_ambiente = ?";
            $params[] = $busqueda_tabla_ambiente;
            $types .= "i";
        }
        
        if ($ies > 0) {
            $condicion .= " AND ai.id_ies = ?";
            $params[] = $ies;
            $types .= "i";
        }
        
        $iniciar = ($pagina - 1) * $cantidad_mostrar;
        $params[] = $iniciar;
        $params[] = $cantidad_mostrar;
        $types .= "ii";
        
        $arrRespuesta = array();
        $stmt = $this->conexion->prepare("SELECT b.id, b.id_ambiente, b.cod_patrimonial, b.denominacion, b.marca, b.modelo, b.tipo, b.color, b.serie, b.dimensiones, b.valor, b.situacion, b.estado_conservacion, b.observaciones FROM bienes b INNER JOIN ambientes_institucion ai ON b.id_ambiente = ai.id WHERE $condicion ORDER BY b.denominacion LIMIT ?, ?");
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($objeto = $result->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }

    public function listarBienes()
    {
        $arrRespuesta = array();
        $sql = $this->conexion->query("SELECT * FROM bienes WHERE estado = 1 ORDER BY denominacion ASC");

        while ($objeto = $sql->fetch_object()) {
            array_push($arrRespuesta, $objeto);
        }
        return $arrRespuesta;
    }

    public function listarBienesCompleto($filtros = [])
    {
        $condiciones = ["b.estado = 1"];
        $params = [];
        $types = "";
        
        // Aplicar filtros dinámicamente
        if (!empty($filtros['codigo'])) {
            $condiciones[] = "b.cod_patrimonial LIKE ?";
            $params[] = $filtros['codigo'] . '%';
            $types .= "s";
        }
        
        if (!empty($filtros['denominacion'])) {
            $condiciones[] = "b.denominacion LIKE ?";
            $params[] = '%' . $filtros['denominacion'] . '%';
            $types .= "s";
        }
        
        if (!empty($filtros['ambiente']) && $filtros['ambiente'] > 0) {
            $condiciones[] = "b.id_ambiente = ?";
            $params[] = $filtros['ambiente'];
            $types .= "i";
        }
        
        if (!empty($filtros['ies']) && $filtros['ies'] > 0) {
            $condiciones[] = "ai.id_ies = ?";
            $params[] = $filtros['ies'];
            $types .= "i";
        }
        
        $where_clause = implode(" AND ", $condiciones);
        
        $sql = "SELECT 
                    b.id, 
                    b.id_ingreso_bienes,
                    b.id_ambiente,
                    ai.detalle as ambiente_nombre,
                    b.cod_patrimonial,
                    b.denominacion,
                    b.marca,
                    b.modelo,
                    b.tipo,
                    b.color,
                    b.serie,
                    b.dimensiones,
                    b.valor,
                    b.situacion,
                    b.estado_conservacion,
                    b.observaciones,
                    b.fecha_registro,
                    b.usuario_registro,
                    b.estado,
                    u.nombres as usuario_nombre
                FROM bienes b 
                LEFT JOIN ambientes_institucion ai ON b.id_ambiente = ai.id
                LEFT JOIN usuarios u ON b.usuario_registro = u.id
                WHERE $where_clause 
                ORDER BY b.denominacion ASC";
        
        $arrRespuesta = array();
        
        if (!empty($params)) {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($objeto = $result->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        } else {
            $result = $this->conexion->query($sql);
            while ($objeto = $result->fetch_object()) {
                array_push($arrRespuesta, $objeto);
            }
        }
        
        return $arrRespuesta;
    }

    public function obtenerEstadisticasBienes()
    {
        $sql = "SELECT 
                    COUNT(*) as total_bienes,
                    COUNT(CASE WHEN situacion = 'Bueno' THEN 1 END) as bienes_buenos,
                    COUNT(CASE WHEN situacion = 'Regular' THEN 1 END) as bienes_regulares,
                    COUNT(CASE WHEN situacion = 'Malo' THEN 1 END) as bienes_malos,
                    SUM(CAST(valor AS DECIMAL(10,2))) as valor_total
                FROM bienes 
                WHERE estado = 1";
        
        $result = $this->conexion->query($sql);
        return $result->fetch_object();
    }
}
?>