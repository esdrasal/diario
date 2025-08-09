<?php

namespace App\Models;

use PDO;

class Libro
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, nombre, testamento, capitulos, versiculos, abreviatura FROM libros ORDER BY id");
        return $stmt->fetchAll();
    }

    public function getByName(string $nombre): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nombre, testamento, capitulos, versiculos, abreviatura FROM libros WHERE nombre = ?");
        $stmt->execute([$nombre]);
        return $stmt->fetch() ?: null;
    }

    public function getCapitulosByLibro(int $libro_id): array
    {
        $stmt = $this->pdo->prepare("SELECT numero, versiculos FROM capitulos WHERE libro_id = ? ORDER BY numero");
        $stmt->execute([$libro_id]);
        return $stmt->fetchAll();
    }

    public function getTestamentos(): array
    {
        $stmt = $this->pdo->query("SELECT nombre, testamento FROM libros ORDER BY id");
        $libros = $stmt->fetchAll();
        
        $testamentos = ['antiguo' => [], 'nuevo' => []];
        foreach ($libros as $libro) {
            if ($libro['testamento'] === 'Antiguo') {
                $testamentos['antiguo'][] = $libro['nombre'];
            } else {
                $testamentos['nuevo'][] = $libro['nombre'];
            }
        }
        
        return $testamentos;
    }

    public function getVersiculosPorCapitulo(): array
    {
        $stmt = $this->pdo->query("
            SELECT l.nombre as libro, c.numero as capitulo, c.versiculos 
            FROM libros l 
            JOIN capitulos c ON l.id = c.libro_id 
            ORDER BY l.id, c.numero
        ");
        
        $versiculos = [];
        while ($row = $stmt->fetch()) {
            $versiculos[$row['libro']][$row['capitulo']] = $row['versiculos'];
        }
        
        return $versiculos;
    }

    public function getCapitulosPorLibro(): array
    {
        $stmt = $this->pdo->query("SELECT nombre, capitulos FROM libros ORDER BY id");
        $capitulos = [];
        while ($row = $stmt->fetch()) {
            $capitulos[$row['nombre']] = $row['capitulos'];
        }
        return $capitulos;
    }
}