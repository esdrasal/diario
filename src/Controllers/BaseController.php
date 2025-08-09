<?php

namespace App\Controllers;

use App\Services\AuthService;

abstract class BaseController
{
    protected $authService;
    protected $viewsPath;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->viewsPath = __DIR__ . '/../../templates/';
    }

    protected function render(string $template, array $data = []): void
    {
        // Extraer variables para la vista
        extract($data);
        
        // Incluir template
        $templatePath = $this->viewsPath . $template . '.php';
        
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            throw new \Exception("Template no encontrado: $template");
        }
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}