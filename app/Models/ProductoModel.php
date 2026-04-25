<?php
// app/Models/ProductoModel.php

require_once __DIR__ . '/Database.php';

class ProductoModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los productos
     */
    public function getAll($tipo = null, $status = null, $limit = 100, $offset = 0) {
        $sql = "SELECT * FROM productos WHERE 1=1";
        $params = [];
        
        if ($tipo) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        if ($status !== null) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY nombre ASC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
            if ($key == ':limit' || $key == ':offset') {
                $stmt->bindValue($key, $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $val);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener producto por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar por código
     */
    public function getByCodigo($codigo) {
        $sql = "SELECT * FROM productos WHERE codigo = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar productos
     */
    public function search($termino) {
        $sql = "SELECT * FROM productos WHERE (nombre LIKE :termino OR codigo LIKE :termino) AND status = 1 ORDER BY nombre ASC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':termino' => "%$termino%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear producto
     */
    public function create($data) {
        $sql = "INSERT INTO productos (codigo, nombre, tipo, categoria, descripcion, 
                precio_unitario, costo_unitario, iva, stock_minimo, stock_actual, 
                unidad_medida, requiere_autorizacion, created_by, status) 
                VALUES (:codigo, :nombre, :tipo, :categoria, :descripcion, 
                :precio_unitario, :costo_unitario, :iva, :stock_minimo, :stock_actual, 
                :unidad_medida, :requiere_autorizacion, :created_by, :status)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigo' => $data['codigo'],
            ':nombre' => $data['nombre'],
            ':tipo' => $data['tipo'],
            ':categoria' => $data['categoria'] ?? null,
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio_unitario' => $data['precio_unitario'] ?? 0,
            ':costo_unitario' => $data['costo_unitario'] ?? 0,
            ':iva' => $data['iva'] ?? 19,
            ':stock_minimo' => $data['stock_minimo'] ?? 0,
            ':stock_actual' => $data['stock_actual'] ?? 0,
            ':unidad_medida' => $data['unidad_medida'] ?? null,
            ':requiere_autorizacion' => $data['requiere_autorizacion'] ?? 0,
            ':created_by' => $_SESSION['user_id'],
            ':status' => $data['status'] ?? 1
        ]);
    }
    
    /**
     * Actualizar producto
     */
    public function update($id, $data) {
        $sql = "UPDATE productos SET 
                codigo = :codigo,
                nombre = :nombre,
                tipo = :tipo,
                categoria = :categoria,
                descripcion = :descripcion,
                precio_unitario = :precio_unitario,
                costo_unitario = :costo_unitario,
                iva = :iva,
                stock_minimo = :stock_minimo,
                stock_actual = :stock_actual,
                unidad_medida = :unidad_medida,
                requiere_autorizacion = :requiere_autorizacion,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':codigo' => $data['codigo'],
            ':nombre' => $data['nombre'],
            ':tipo' => $data['tipo'],
            ':categoria' => $data['categoria'] ?? null,
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio_unitario' => $data['precio_unitario'] ?? 0,
            ':costo_unitario' => $data['costo_unitario'] ?? 0,
            ':iva' => $data['iva'] ?? 19,
            ':stock_minimo' => $data['stock_minimo'] ?? 0,
            ':stock_actual' => $data['stock_actual'] ?? 0,
            ':unidad_medida' => $data['unidad_medida'] ?? null,
            ':requiere_autorizacion' => $data['requiere_autorizacion'] ?? 0,
            ':status' => $data['status'] ?? 1
        ]);
    }
    
    /**
     * Actualizar stock
     */
    public function updateStock($id, $cantidad) {
        $sql = "UPDATE productos SET stock_actual = stock_actual - :cantidad WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':cantidad' => $cantidad]);
    }
    
    /**
     * Eliminar producto
     */
    public function delete($id) {
        $sql = "UPDATE productos SET status = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Obtener estadísticas
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN tipo = 'medicamento' THEN 1 ELSE 0 END) as total_medicamentos,
                    SUM(CASE WHEN tipo = 'procedimiento' THEN 1 ELSE 0 END) as total_procedimientos,
                    SUM(CASE WHEN tipo = 'examen' THEN 1 ELSE 0 END) as total_examenes,
                    SUM(CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END) as productos_bajo_stock
                FROM productos WHERE status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}