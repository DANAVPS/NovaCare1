<?php
// app/Models/AutorizacionModel.php

require_once __DIR__ . '/Database.php';

class AutorizacionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Generar número de autorización único
     */
    private function generarNumeroAutorizacion()
    {
        $prefijo = 'AUT-' . date('Ymd');
        $sql = "SELECT COUNT(*) as total FROM autorizaciones WHERE numero_autorizacion LIKE :prefijo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':prefijo' => $prefijo . '%']);
        $result = $stmt->fetch();
        $numero = $result['total'] + 1;
        return $prefijo . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener todas las autorizaciones
     */
    public function getAll($estado = null, $limit = 100, $offset = 0)
    {
        $sql = "SELECT a.*, 
                p.nombre as paciente_nombre, p.identificacion as paciente_identificacion,
                m.nombre as medico_nombre,
                pr.nombre as producto_nombre
                FROM autorizaciones a
                LEFT JOIN clientes p ON a.paciente_id = p.id
                LEFT JOIN clientes m ON a.medico_autorizador_id = m.id
                LEFT JOIN ordenes_productos op ON a.orden_producto_id = op.id
                LEFT JOIN productos pr ON op.producto_id = pr.id
                WHERE 1=1";
        $params = [];

        if ($estado) {
            $sql .= " AND a.estado = :estado";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
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
     * Obtener autorización por ID
     */
    public function getById($id)
    {
        $sql = "SELECT a.*, 
                p.nombre as paciente_nombre, p.identificacion as paciente_identificacion,
                m.nombre as medico_nombre,
                pr.nombre as producto_nombre, pr.codigo as producto_codigo,
                op.cantidad, op.cantidad_autorizada
                FROM autorizaciones a
                LEFT JOIN clientes p ON a.paciente_id = p.id
                LEFT JOIN clientes m ON a.medico_autorizador_id = m.id
                LEFT JOIN ordenes_productos op ON a.orden_producto_id = op.id
                LEFT JOIN productos pr ON op.producto_id = pr.id
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crear autorización
     */
    public function create($data)
{
    $numeroAutorizacion = $this->generarNumeroAutorizacion();
    // Si viene estado_inicial (producto libre aprobado auto), usarlo; si no, 'pendiente'
    $estado = $data['estado_inicial'] ?? 'pendiente';

    $sql = "INSERT INTO autorizaciones (numero_autorizacion, orden_producto_id, paciente_id, 
            medico_autorizador_id, cantidad_aprobada, observaciones, estado,
            fecha_autorizacion, autorizado_por) 
            VALUES (:numero_autorizacion, :orden_producto_id, :paciente_id, 
            :medico_autorizador_id, :cantidad_aprobada, :observaciones, :estado,
            :fecha_autorizacion, :autorizado_por)";

    $fechaAut  = ($estado === 'aprobada') ? date('Y-m-d H:i:s') : null;
    $autorizadoPor = ($estado === 'aprobada') ? ($_SESSION['user_id'] ?? null) : null;

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':numero_autorizacion'  => $numeroAutorizacion,
        ':orden_producto_id'    => $data['orden_producto_id'],
        ':paciente_id'          => $data['paciente_id'],
        ':medico_autorizador_id'=> $data['medico_autorizador_id'] ?? null,
        ':cantidad_aprobada'    => $data['cantidad_aprobada'] ?? 1,
        ':observaciones'        => $data['observaciones'] ?? null,
        ':estado'               => $estado,
        ':fecha_autorizacion'   => $fechaAut,
        ':autorizado_por'       => $autorizadoPor
    ]);
}

/**
 * Verificar si ya existe una autorización para un orden_producto
 */
public function existeParaOrdenProducto($ordenProductoId): bool
{
    $sql = "SELECT COUNT(*) FROM autorizaciones WHERE orden_producto_id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $ordenProductoId]);
    return (int) $stmt->fetchColumn() > 0;
}

    /**
     * Aprobar/Rechazar autorización
     */
    public function aprobar($id, $estado, $motivo = null)
    {
        $sql = "UPDATE autorizaciones SET 
                estado = :estado, 
                fecha_autorizacion = NOW(),
                motivo_rechazo = :motivo,
                autorizado_por = :autorizado_por
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':estado' => $estado,
            ':motivo' => $motivo,
            ':autorizado_por' => $_SESSION['user_id']
        ]);
    }

    /**
     * Obtener estadísticas
     */
    public function getStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas
                FROM autorizaciones";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Aprobar autorización y enviar correo
     */
    public function aprobarConNotificacion($id, $cantidadAprobada)
    {
        try {
            $this->db->beginTransaction();

            // Obtener la autorización con todos los datos
            $autorizacion = $this->getById($id);
            if (!$autorizacion) {
                return false;
            }

            // Obtener datos del paciente
            $paciente = $this->getPacienteById($autorizacion['paciente_id']);

            // Obtener producto
            $producto = $this->getProductoByOrdenProductoId($autorizacion['orden_producto_id']);

            // Obtener orden
            $orden = $this->getOrdenByOrdenProductoId($autorizacion['orden_producto_id']);

            // Actualizar autorización
            $sql = "UPDATE autorizaciones SET 
                estado = 'aprobada', 
                fecha_autorizacion = NOW(),
                cantidad_aprobada = :cantidad_aprobada,
                autorizado_por = :autorizado_por
                WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':cantidad_aprobada' => $cantidadAprobada,
                ':autorizado_por' => $_SESSION['user_id']
            ]);

            // Actualizar orden_producto
            $sqlOp = "UPDATE ordenes_productos SET 
                  estado_autorizacion = 'aprobada',
                  cantidad_autorizada = :cantidad_aprobada
                  WHERE id = :orden_producto_id";

            $stmtOp = $this->db->prepare($sqlOp);
            $stmtOp->execute([
                ':orden_producto_id' => $autorizacion['orden_producto_id'],
                ':cantidad_aprobada' => $cantidadAprobada
            ]);
            $this->recalcularEstadoOrden($autorizacion['orden_producto_id']);

            $this->db->commit();

            // Enviar correo de aprobación
            $this->enviarCorreoAprobacion($autorizacion, $paciente, $producto, $orden);

            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al aprobar autorización: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechazar autorización y enviar correo
     */
    public function rechazarConNotificacion($id, $motivo)
    {
        try {
            $this->db->beginTransaction();

            // Obtener la autorización con todos los datos
            $autorizacion = $this->getById($id);
            if (!$autorizacion) {
                return false;
            }

            // Obtener datos del paciente
            $paciente = $this->getPacienteById($autorizacion['paciente_id']);

            // Obtener producto
            $producto = $this->getProductoByOrdenProductoId($autorizacion['orden_producto_id']);

            // Obtener orden
            $orden = $this->getOrdenByOrdenProductoId($autorizacion['orden_producto_id']);

            // Actualizar autorización
            $sql = "UPDATE autorizaciones SET 
                estado = 'rechazada', 
                fecha_autorizacion = NOW(),
                motivo_rechazo = :motivo,
                autorizado_por = :autorizado_por
                WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':motivo' => $motivo,
                ':autorizado_por' => $_SESSION['user_id']
            ]);

            // Actualizar orden_producto
            $sqlOp = "UPDATE ordenes_productos SET 
                  estado_autorizacion = 'rechazada'
                  WHERE id = :orden_producto_id";

            $stmtOp = $this->db->prepare($sqlOp);
            $stmtOp->execute([':orden_producto_id' => $autorizacion['orden_producto_id']]);
            $this->recalcularEstadoOrden($autorizacion['orden_producto_id']);


            $this->db->commit();

            // Enviar correo de rechazo
            $this->enviarCorreoRechazo($autorizacion, $paciente, $producto, $orden);

            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error al rechazar autorización: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar correo de aprobación
     */
    private function enviarCorreoAprobacion($autorizacion, $paciente, $producto, $orden)
    {
        require_once __DIR__ . '/../core/Mailer.php';

        $mailer = Mailer::getInstance();

        // Datos del destinatario
        $destinatario = [
            'nombre' => $paciente['nombre'] . ' ' . ($paciente['apellido'] ?? ''),
            'email' => $paciente['email']
        ];

        // Si el paciente tiene correo, enviar notificación
        if (!empty($paciente['email'])) {
            $mailer->sendAutorizacionAprobada($destinatario, $autorizacion, $paciente, $producto, $orden);
        }

        // Notificar al administrador
        if (MAIL_NOTIFY_ADMIN) {
            $adminDestinatario = [
                'nombre' => 'Administrador',
                'email' => MAIL_ADMIN_EMAIL
            ];
            $mailer->sendAutorizacionAprobada($adminDestinatario, $autorizacion, $paciente, $producto, $orden);
        }
    }

    /**
     * Enviar correo de rechazo
     */
    private function enviarCorreoRechazo($autorizacion, $paciente, $producto, $orden)
    {
        require_once __DIR__ . '/../Core/Mailer.php';

        $mailer = Mailer::getInstance();

        $destinatario = [
            'nombre' => $paciente['nombre'] . ' ' . ($paciente['apellido'] ?? ''),
            'email' => $paciente['email']
        ];

        if (!empty($paciente['email'])) {
            $mailer->sendAutorizacionRechazada($destinatario, $autorizacion, $paciente, $producto, $orden);
        }

        if (MAIL_NOTIFY_ADMIN) {
            $adminDestinatario = [
                'nombre' => 'Administrador',
                'email' => MAIL_ADMIN_EMAIL
            ];
            $mailer->sendAutorizacionRechazada($adminDestinatario, $autorizacion, $paciente, $producto, $orden);
        }
    }

    /**
     * Obtener paciente por ID
     */
    private function getPacienteById($id)
    {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener producto por orden_producto_id
     */
    private function getProductoByOrdenProductoId($ordenProductoId)
    {
        $sql = "SELECT p.* FROM productos p
            INNER JOIN ordenes_productos op ON p.id = op.producto_id
            WHERE op.id = :orden_producto_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':orden_producto_id' => $ordenProductoId]);
        return $stmt->fetch();
    }

    /**
     * Obtener orden por orden_producto_id
     */
    private function getOrdenByOrdenProductoId($ordenProductoId)
    {
        $sql = "SELECT o.* FROM ordenes_medicas o
            INNER JOIN ordenes_productos op ON o.id = op.orden_id
            WHERE op.id = :orden_producto_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':orden_producto_id' => $ordenProductoId]);
        return $stmt->fetch();
    }
    /**
 * Recalcula y actualiza estado_autorizacion en ordenes_medicas
 * según el estado real de todas sus autorizaciones
 */
private function recalcularEstadoOrden($ordenProductoId)
{
    // Obtener orden_id desde ordenes_productos
    $sql = "SELECT orden_id FROM ordenes_productos WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $ordenProductoId]);
    $row = $stmt->fetch();
    if (!$row) return;

    $ordenId = $row['orden_id'];

    // Contar estados de todas las autorizaciones de la orden
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN a.estado = 'pendiente'  THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN a.estado = 'rechazada'  THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN a.estado = 'aprobada'   THEN 1 ELSE 0 END) as aprobadas
            FROM autorizaciones a
            INNER JOIN ordenes_productos op ON a.orden_producto_id = op.id
            WHERE op.orden_id = :orden_id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':orden_id' => $ordenId]);
    $stats = $stmt->fetch();

    // Determinar nuevo estado
    if ($stats['rechazadas'] > 0) {
        $nuevoEstado = 'rechazada';
    } elseif ($stats['pendientes'] > 0) {
        $nuevoEstado = 'pendiente';
    } else {
        $nuevoEstado = 'aprobada'; // todas aprobadas
    }

    // Actualizar ordenes_medicas
    $sql = "UPDATE ordenes_medicas SET estado_autorizacion = :estado WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':estado' => $nuevoEstado, ':id' => $ordenId]);
}
}
