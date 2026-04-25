<?php
// app/Models/UserModel.php

require_once __DIR__ . '/Database.php';

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Método público para ejecutar consultas SQL personalizadas
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Método público para obtener la conexión (si es necesario)
     */
    public function getConnection()
    {
        return $this->db;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT id, name, email, password, role, status, last_login 
                FROM users 
                WHERE email = :email AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $sql = "SELECT id, name, email, role, status, last_login, created_at 
                FROM users 
                WHERE id = :id AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function updateLastLogin($userId)
    {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $userId]);
    }

    public function create($data)
    {
        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES (:name, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 10]);

        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $hashedPassword,
            ':role' => $data['role'] ?? 'doctor'
        ]);
    }

    public function getStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN role = 'doctor' THEN 1 ELSE 0 END) as total_doctors,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_users
                FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Guardar token de restablecimiento
     */
    public function saveResetToken($userId, $token, $expires)
    {
        $sql = "UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':id' => $userId
        ]);
    }
    
    /**
     * Verificar token de restablecimiento
     */
    public function verifyResetToken($token)
    {
        $sql = "SELECT id, email FROM users WHERE reset_token = :token AND reset_expires > NOW() AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }
    
    /**
     * Actualizar contraseña y limpiar token
     */
    public function updatePassword($userId, $newPassword)
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => $hash,
            ':id' => $userId
        ]);
    }
}