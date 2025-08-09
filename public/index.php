<?php

// Router básico para la aplicación modernizada
require_once '../vendor/autoload.php';

// Configurar errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\ChaptersController;

// Obtener la URL solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Definir rutas
$routes = [
    'GET' => [
        '/' => [HomeController::class, 'index'],
        '/login' => [AuthController::class, 'loginForm'],
        '/registro' => [AuthController::class, 'registroForm'],
        '/logout' => [AuthController::class, 'logout'],
        '/book' => [BookController::class, 'show'],
        '/chapters' => [ChaptersController::class, 'index'],
    ],
    'POST' => [
        '/' => [HomeController::class, 'store'],
        '/login' => [AuthController::class, 'login'],
        '/registro' => [AuthController::class, 'registro'],
    ]
];

// Buscar ruta
if (isset($routes[$method][$path])) {
    $handler = $routes[$method][$path];
    $controller = new $handler[0]();
    $action = $handler[1];
    $controller->$action();
} else {
    // 404 - Ruta no encontrada
    http_response_code(404);
    echo "Página no encontrada";
}