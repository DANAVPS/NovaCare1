<?php
// app/Models/ClienteModel.php

require_once __DIR__ . '/Database.php';

class ClienteModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los clientes
     */
    public function getAll($tipo = null, $status = null, $limit = 100, $offset = 0) {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM ordenes_medicas WHERE paciente_id = c.id) as total_ordenes
                FROM clientes c WHERE 1=1";
        $params = [];
        
        if ($tipo) {
            $sql .= " AND c.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        if ($status !== null) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
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
     * Obtener cliente por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar cliente por identificación
     */
    public function getByIdentificacion($identificacion) {
        $sql = "SELECT * FROM clientes WHERE identificacion = :identificacion";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identificacion' => $identificacion]);
        return $stmt->fetch();
    }
    
    /**
     * Buscar clientes por nombre
     */
    public function search($termino, $tipo = null) {
        $sql = "SELECT * FROM clientes WHERE (nombre LIKE :termino OR apellido LIKE :termino OR identificacion LIKE :termino) AND status = 1";
        $params = [':termino' => "%$termino%"];
        
        if ($tipo) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        $sql .= " ORDER BY nombre ASC LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear cliente
     */
    public function create($data) {
        $sql = "INSERT INTO clientes (tipo, identificacion, nombre, apellido, email, telefono, 
                direccion, ciudad, departamento, contacto_nombre, contacto_telefono, observaciones, 
                created_by, status) 
                VALUES (:tipo, :identificacion, :nombre, :apellido, :email, :telefono, 
                :direccion, :ciudad, :departamento, :contacto_nombre, :contacto_telefono, 
                :observaciones, :created_by, :status)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tipo' => $data['tipo'],
            ':identificacion' => $data['identificacion'],
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':ciudad' => $data['ciudad'] ?? null,
            ':departamento' => $data['departamento'] ?? null,
            ':contacto_nombre' => $data['contacto_nombre'] ?? null,
            ':contacto_telefono' => $data['contacto_telefono'] ?? null,
            ':observaciones' => $data['observaciones'] ?? null,
            ':created_by' => $_SESSION['user_id'],
            ':status' => $data['status'] ?? 1
        ]);
    }
    
    /**
     * Actualizar cliente
     */
    public function update($id, $data) {
        $sql = "UPDATE clientes SET 
                tipo = :tipo,
                identificacion = :identificacion,
                nombre = :nombre,
                apellido = :apellido,
                email = :email,
                telefono = :telefono,
                direccion = :direccion,
                ciudad = :ciudad,
                departamento = :departamento,
                contacto_nombre = :contacto_nombre,
                contacto_telefono = :contacto_telefono,
                observaciones = :observaciones,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':tipo' => $data['tipo'],
            ':identificacion' => $data['identificacion'],
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':ciudad' => $data['ciudad'] ?? null,
            ':departamento' => $data['departamento'] ?? null,
            ':contacto_nombre' => $data['contacto_nombre'] ?? null,
            ':contacto_telefono' => $data['contacto_telefono'] ?? null,
            ':observaciones' => $data['observaciones'] ?? null,
            ':status' => $data['status'] ?? 1
        ]);
    }
    
    /**
     * Eliminar cliente (soft delete)
     */
    public function delete($id) {
        $sql = "UPDATE clientes SET status = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Obtener estadísticas de clientes
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN tipo = 'EPS' THEN 1 ELSE 0 END) as total_eps,
                    SUM(CASE WHEN tipo = 'IPS' THEN 1 ELSE 0 END) as total_ips,
                    SUM(CASE WHEN tipo = 'medico' THEN 1 ELSE 0 END) as total_medicos,
                    SUM(CASE WHEN tipo = 'paciente' THEN 1 ELSE 0 END) as total_pacientes
                FROM clientes WHERE status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}