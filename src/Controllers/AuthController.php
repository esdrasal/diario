<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function loginForm(): void
    {
        $this->render('auth/login');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($this->authService->login($email, $password)) {
            $this->redirect('/');
        } else {
            $this->render('auth/login', [
                'error' => 'Credenciales inválidas',
                'email' => $email
            ]);
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
    }

    public function registroForm(): void
    {
        $this->render('auth/registro');
    }

    public function registro(): void
    {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($this->authService->registrar($nombre, $email, $password)) {
            if ($this->authService->login($email, $password)) {
                $this->redirect('/');
            }
        }
        
        $this->render('auth/registro', [
            'error' => 'Error al registrar usuario',
            'nombre' => $nombre,
            'email' => $email
        ]);
    }
}