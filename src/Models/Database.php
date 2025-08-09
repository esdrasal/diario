<?php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        // Detectar si es local o producción
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        if ($httpHost === 'localhost' || 
            $httpHost === '127.0.0.1' || 
            strpos($httpHost, 'localhost:') === 0 ||
            empty($httpHost)) {
            // Configuración local
            $host = 'localhost';
            $dbname = 'diario_biblico';
            $username = 'diario_user';
            $password = 'diario_password';
        } else {
            // Configuración InfinityFree
            $host = 'sql106.infinityfree.com';
            $dbname = 'if0_39655750_diario';
            $username = 'if0_39655750';
            $password = 'CMYVnWgmZXA8FYQ';
        }

        try {
            $dsn = "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}