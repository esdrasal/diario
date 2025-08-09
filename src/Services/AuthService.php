<?php

namespace App\Services;

use App\Models\Usuario;

class AuthService
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function iniciarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function requiereAuth(): void
    {
        $this->iniciarSesion();
        
        if (!$this->estaLogueado()) {
            header('Location: /login.php');
            exit;
        }
    }

    public function estaLogueado(): bool
    {
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    public function login(string $email, string $password): bool
    {
        $usuario = $this->usuarioModel->verificar($email, $password);
        
        if ($usuario) {
            $this->iniciarSesion();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            return true;
        }
        
        return false;
    }

    public function logout(): void
    {
        $this->iniciarSesion();
        session_destroy();
        header('Location: /login.php');
        exit;
    }

    public function registrar(string $nombre, string $email, string $password): bool
    {
        if ($this->usuarioModel->emailExiste($email)) {
            return false;
        }
        
        return $this->usuarioModel->crear($nombre, $email, $password);
    }

    public function getUsuarioActual(): ?array
    {
        if (!$this->estaLogueado()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email']
        ];
    }
}