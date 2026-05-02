<?php
// app/Models/OrdenMedicaModel.php

require_once __DIR__ . '/Database.php';

class OrdenMedicaModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Generar número de orden único
     */
    private function generarNumeroOrden() {
        $prefijo = 'ORD-' . date('Ymd');
        $sql = "SELECT COUNT(*) as total FROM ordenes_medicas WHERE numero_orden LIKE :prefijo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prefijo' => $prefijo . '%']);
        $result = $stmt->fetch();
        $numero = ($result['total'] ?? 0) + 1;
        return $prefijo . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Obtener todas las órdenes
     */
    public function getAll($estado = null, $limit = 100, $offset = 0) {
    $sql = "SELECT o.*,
                p.nombre as paciente_nombre, p.identificacion as paciente_identificacion,
                m.nombre as medico_nombre,
                u.name as creado_por_nombre,
                (SELECT COUNT(*) FROM ordenes_productos WHERE orden_id = o.id) as total_items,
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM autorizaciones a
                        INNER JOIN ordenes_productos op ON a.orden_producto_id = op.id
                        WHERE op.orden_id = o.id AND a.estado = 'rechazada'
                    ) THEN 'rechazada'
                    WHEN EXISTS (
                        SELECT 1 FROM autorizaciones a
                        INNER JOIN ordenes_productos op ON a.orden_producto_id = op.id
                        WHERE op.orden_id = o.id AND a.estado = 'pendiente'
                    ) THEN 'pendiente'
                    WHEN EXISTS (
                        SELECT 1 FROM autorizaciones a
                        INNER JOIN ordenes_productos op ON a.orden_producto_id = op.id
                        WHERE op.orden_id = o.id AND a.estado = 'aprobada'
                    ) THEN 'aprobada'
                    ELSE 'sin_autorizacion'
                END as estado_autorizacion_real
            FROM ordenes_medicas o
            LEFT JOIN clientes p ON o.paciente_id = p.id
            LEFT JOIN clientes m ON o.medico_id = m.id
            LEFT JOIN users u ON o.created_by = u.id
            WHERE 1=1";
        $params = [];
        
        if ($estado) {
            $sql .= " AND o.estado = :estado";
            $params[':estado'] = $estado;
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
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
     * Obtener orden por ID con detalles
     */
    public function getById($id) {
        $sql = "SELECT o.*, 
                p.nombre as paciente_nombre, p.identificacion as paciente_identificacion, p.telefono as paciente_telefono, p.email as paciente_email,
                m.nombre as medico_nombre, m.telefono as medico_telefono,
                u.name as creado_por_nombre
                FROM ordenes_medicas o
                LEFT JOIN clientes p ON o.paciente_id = p.id
                LEFT JOIN clientes m ON o.medico_id = m.id
                LEFT JOIN users u ON o.created_by = u.id
                WHERE o.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener productos de una orden
     */
    public function getProductosByOrden($ordenId) {
        $sql = "SELECT op.*, pr.nombre as producto_nombre, pr.codigo as producto_codigo,
                pr.unidad_medida, pr.requiere_autorizacion
                FROM ordenes_productos op
                INNER JOIN productos pr ON op.producto_id = pr.id
                WHERE op.orden_id = :orden_id
                ORDER BY op.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':orden_id' => $ordenId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Crear orden médica (versión simple sin autorizaciones automáticas)
     */
    public function create($data, $productos) {
        try {
            $this->db->beginTransaction();
            
            $numeroOrden = $this->generarNumeroOrden();
            $totalProductos = count($productos);
            $totalValor = 0;
            
            foreach ($productos as $prod) {
                $totalValor += ($prod['cantidad'] ?? 1) * ($prod['precio_unitario'] ?? 0);
            }
            
            $sql = "INSERT INTO ordenes_medicas (numero_orden, paciente_id, medico_id, fecha_orden, 
                    fecha_expiracion, diagnostico, prioridad, observaciones, total_productos, 
                    total_valor, created_by, estado) 
                    VALUES (:numero_orden, :paciente_id, :medico_id, :fecha_orden, 
                    :fecha_expiracion, :diagnostico, :prioridad, :observaciones, :total_productos, 
                    :total_valor, :created_by, 'pendiente')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':numero_orden' => $numeroOrden,
                ':paciente_id' => $data['paciente_id'] ?? null,
                ':medico_id' => $data['medico_id'] ?? null,
                ':fecha_orden' => $data['fecha_orden'] ?? date('Y-m-d'),
                ':fecha_expiracion' => $data['fecha_expiracion'] ?? null,
                ':diagnostico' => $data['diagnostico'] ?? null,
                ':prioridad' => $data['prioridad'] ?? 'media',
                ':observaciones' => $data['observaciones'] ?? null,
                ':total_productos' => $totalProductos,
                ':total_valor' => $totalValor,
                ':created_by' => $_SESSION['user_id']
            ]);
            
            $ordenId = $this->db->lastInsertId();
            
            // Insertar productos
            foreach ($productos as $prod) {
                $subtotal = ($prod['cantidad'] ?? 1) * ($prod['precio_unitario'] ?? 0);
                
                $sqlProd = "INSERT INTO ordenes_productos (orden_id, producto_id, cantidad, 
                            precio_unitario, subtotal, estado_autorizacion) 
                            VALUES (:orden_id, :producto_id, :cantidad, 
                            :precio_unitario, :subtotal, 'pendiente')";
                
                $stmtProd = $this->db->prepare($sqlProd);
                $stmtProd->execute([
                    ':orden_id' => $ordenId,
                    ':producto_id' => $prod['producto_id'],
                    ':cantidad' => $prod['cantidad'] ?? 1,
                    ':precio_unitario' => $prod['precio_unitario'] ?? 0,
                    ':subtotal' => $subtotal
                ]);
            }
            
            $this->db->commit();
            return $ordenId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al crear orden: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear orden médica con autorizaciones automáticas
     */
    public function createWithAutorizaciones($data, $productos) {
        try {
            $this->db->beginTransaction();
            
            $numeroOrden = $this->generarNumeroOrden();
            $totalProductos = count($productos);
            $totalValor = 0;
            
            foreach ($productos as $prod) {
                $totalValor += ($prod['cantidad'] ?? 0) * ($prod['precio_unitario'] ?? 0);
            }
            
            $sql = "INSERT INTO ordenes_medicas (numero_orden, paciente_id, medico_id, fecha_orden, 
                    diagnostico, prioridad, observaciones, total_productos, total_valor, created_by, estado) 
                    VALUES (:numero_orden, :paciente_id, :medico_id, :fecha_orden, 
                    :diagnostico, :prioridad, :observaciones, :total_productos, 
                    :total_valor, :created_by, 'pendiente')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':numero_orden' => $numeroOrden,
                ':paciente_id' => $data['paciente_id'] ?? null,
                ':medico_id' => $data['medico_id'] ?? null,
                ':fecha_orden' => $data['fecha_orden'] ?? date('Y-m-d'),
                ':diagnostico' => $data['diagnostico'] ?? null,
                ':prioridad' => $data['prioridad'] ?? 'media',
                ':observaciones' => $data['observaciones'] ?? null,
                ':total_productos' => $totalProductos,
                ':total_valor' => $totalValor,
                ':created_by' => $_SESSION['user_id']
            ]);
            
            $ordenId = $this->db->lastInsertId();
            
            // Insertar productos y crear autorizaciones si es necesario
            foreach ($productos as $prod) {
                $subtotal = ($prod['cantidad'] ?? 1) * ($prod['precio_unitario'] ?? 0);
                
                // Verificar si el producto requiere autorización
                $requiereAutorizacion = $this->productoRequiereAutorizacion($prod['producto_id']);
                $estadoAutorizacion = $requiereAutorizacion ? 'pendiente' : 'aprobada';
                
                $sqlProd = "INSERT INTO ordenes_productos (orden_id, producto_id, cantidad, 
                            precio_unitario, subtotal, estado_autorizacion) 
                            VALUES (:orden_id, :producto_id, :cantidad, 
                            :precio_unitario, :subtotal, :estado_autorizacion)";
                
                $stmtProd = $this->db->prepare($sqlProd);
                $stmtProd->execute([
                    ':orden_id' => $ordenId,
                    ':producto_id' => $prod['producto_id'],
                    ':cantidad' => $prod['cantidad'] ?? 1,
                    ':precio_unitario' => $prod['precio_unitario'] ?? 0,
                    ':subtotal' => $subtotal,
                    ':estado_autorizacion' => $estadoAutorizacion
                ]);
                
                $ordenProductoId = $this->db->lastInsertId();
                
                // Si requiere autorización, crear registro en autorizaciones
                if ($requiereAutorizacion) {
                    $this->crearAutorizacion($ordenProductoId, $data['paciente_id'], $prod['producto_id'], $prod['cantidad'] ?? 1);
                }
            }
            
            $this->db->commit();
            return $ordenId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al crear orden con autorizaciones: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un producto requiere autorización
     */
    private function productoRequiereAutorizacion($productoId) {
        $sql = "SELECT requiere_autorizacion FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $productoId]);
        $result = $stmt->fetch();
        return $result && $result['requiere_autorizacion'] == 1;
    }
    
    /**
     * Crear autorización para un producto
     */
    private function crearAutorizacion($ordenProductoId, $pacienteId, $productoId, $cantidad) {
        require_once __DIR__ . '/AutorizacionModel.php';
        $autorizacionModel = new AutorizacionModel();
        
        $data = [
            'orden_producto_id' => $ordenProductoId,
            'paciente_id' => $pacienteId,
            'cantidad_aprobada' => $cantidad
        ];
        
        return $autorizacionModel->create($data);
    }
    
    /**
     * Actualizar estado de orden
     */
    public function updateEstado($id, $estado) {
        $sql = "UPDATE ordenes_medicas SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':estado' => $estado]);
    }
    
    /**
     * Anular orden
     */
    public function anular($id, $motivo) {
        $sql = "UPDATE ordenes_medicas SET estado = 'anulada', observaciones = CONCAT(IFNULL(observaciones, ''), ' Motivo anulación: ', :motivo) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':motivo' => $motivo]);
    }
    
    /**
     * Obtener estadísticas
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'anulada' THEN 1 ELSE 0 END) as anuladas,
                    SUM(CASE WHEN estado = 'parcial' THEN 1 ELSE 0 END) as parcial,
                    SUM(total_valor) as valor_total,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as ordenes_hoy
                FROM ordenes_medicas";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Obtener órdenes por paciente
     */
    public function getByPaciente($pacienteId, $limit = 50) {
        $sql = "SELECT o.*, 
                m.nombre as medico_nombre,
                (SELECT COUNT(*) FROM ordenes_productos WHERE orden_id = o.id) as total_items
                FROM ordenes_medicas o
                LEFT JOIN clientes m ON o.medico_id = m.id
                WHERE o.paciente_id = :paciente_id
                ORDER BY o.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':paciente_id', $pacienteId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar órdenes por número
     */
    public function searchByNumero($termino, $limit = 50) {
        $sql = "SELECT o.*, 
                p.nombre as paciente_nombre
                FROM ordenes_medicas o
                LEFT JOIN clientes p ON o.paciente_id = p.id
                WHERE o.numero_orden LIKE :termino
                ORDER BY o.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':termino', "%$termino%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /**
 * Aprobar orden: cambia estado y crea autorizaciones para todos sus productos
 */
public function aprobarOrden($ordenId)
{
    try {
        $this->db->beginTransaction();

        // Cambiar estado de la orden
        $sql = "UPDATE ordenes_medicas SET estado = 'en_proceso' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $ordenId]);

        // Obtener todos los productos de la orden
        $productos = $this->getProductosByOrden($ordenId);

        // Obtener paciente_id de la orden
        $orden = $this->getById($ordenId);
        $pacienteId = $orden['paciente_id'];

        require_once __DIR__ . '/AutorizacionModel.php';
        $autorizacionModel = new AutorizacionModel();

        foreach ($productos as $prod) {
            $requiereAutorizacion = $prod['requiere_autorizacion'] ?? 0;
            $estadoAut = $requiereAutorizacion ? 'pendiente' : 'aprobada';

            // Actualizar estado_autorizacion en ordenes_productos
            $sqlOp = "UPDATE ordenes_productos 
                      SET estado_autorizacion = :estado 
                      WHERE id = :id";
            $stmtOp = $this->db->prepare($sqlOp);
            $stmtOp->execute([
                ':estado' => $estadoAut,
                ':id'     => $prod['id']
            ]);

            // Crear registro en autorizaciones solo si no existe ya
            $yaExiste = $autorizacionModel->existeParaOrdenProducto($prod['id']);
            if (!$yaExiste) {
                $autorizacionModel->create([
                    'orden_producto_id'    => $prod['id'],
                    'paciente_id'          => $pacienteId,
                    'cantidad_aprobada'    => $prod['cantidad'],
                    'medico_autorizador_id'=> null,
                    'observaciones'        => $requiereAutorizacion
                                                ? null
                                                : 'Producto libre - aprobado automáticamente',
                    'estado_inicial'       => $estadoAut   // se usará en create()
                ]);
            }
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollback();
        error_log("Error al aprobar orden: " . $e->getMessage());
        return false;
    }
}

/**
 * Rechazar orden: solo cambia estado, no crea autorizaciones
 */
public function rechazarOrden($ordenId, $motivo = null)
{
    $sql = "UPDATE ordenes_medicas 
            SET estado = 'anulada',
                observaciones = CONCAT(IFNULL(observaciones, ''), ' | Rechazada: ', :motivo)
            WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':id'     => $ordenId,
        ':motivo' => $motivo ?? 'Sin motivo especificado'
    ]);
}
}
?>