<?php

namespace App\Models;

class Lectura
{
    private $pdo;
    private $libroModel;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
        $this->libroModel = new Libro();
    }

    public function guardar(int $usuario_id, string $fecha, string $libro_nombre, int $capitulo_desde, int $versiculo_desde, int $capitulo_hasta, int $versiculo_hasta, string $notas = '', array $favoritos = []): bool
    {
        $libro = $this->libroModel->getByName($libro_nombre);
        if (!$libro) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("
            INSERT INTO lecturas (usuario_id, fecha, libro_id, capitulo_desde, versiculo_desde, capitulo_hasta, versiculo_hasta, notas, favoritos) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $usuario_id,
            $fecha,
            $libro['id'],
            $capitulo_desde,
            $versiculo_desde,
            $capitulo_hasta,
            $versiculo_hasta,
            $notas,
            json_encode($favoritos)
        ]);
    }

    public function obtenerPorUsuario(int $usuario_id): array
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, lib.nombre as libro_nombre 
            FROM lecturas l 
            JOIN libros lib ON l.libro_id = lib.id 
            WHERE l.usuario_id = ? 
            ORDER BY l.fecha DESC, l.fecha_creacion DESC
        ");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll();
    }

    public function actualizar(int $lectura_id, int $usuario_id, string $fecha, string $libro_nombre, int $capitulo_desde, int $versiculo_desde, int $capitulo_hasta, int $versiculo_hasta, string $notas = '', array $favoritos = []): bool
    {
        $libro = $this->libroModel->getByName($libro_nombre);
        if (!$libro) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE lecturas 
            SET fecha = ?, libro_id = ?, capitulo_desde = ?, versiculo_desde = ?, capitulo_hasta = ?, versiculo_hasta = ?, notas = ?, favoritos = ?
            WHERE id = ? AND usuario_id = ?
        ");
        
        return $stmt->execute([
            $fecha,
            $libro['id'],
            $capitulo_desde,
            $versiculo_desde,
            $capitulo_hasta,
            $versiculo_hasta,
            $notas,
            json_encode($favoritos),
            $lectura_id,
            $usuario_id
        ]);
    }

    public function eliminar(int $lectura_id, int $usuario_id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM lecturas WHERE id = ? AND usuario_id = ?");
        return $stmt->execute([$lectura_id, $usuario_id]);
    }
}