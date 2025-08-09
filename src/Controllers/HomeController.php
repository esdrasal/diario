<?php

namespace App\Controllers;

use App\Models\Libro;
use App\Models\Lectura;
use App\Services\ProgresoService;

class HomeController extends BaseController
{
    private $libroModel;
    private $lecturaModel;
    private $progresoService;

    public function __construct()
    {
        parent::__construct();
        $this->libroModel = new Libro();
        $this->lecturaModel = new Lectura();
        $this->progresoService = new ProgresoService();
    }

    public function index(): void
    {
        $this->authService->requiereAuth();
        
        $usuario = $this->authService->getUsuarioActual();
        
        // Manejar eliminación si existe
        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
            $id = (int)$_GET['id'];
            $this->lecturaModel->eliminar($id, $usuario['id']);
            $this->redirect('/');
        }
        
        // Obtener datos necesarios
        $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();
        $capitulosPorLibro = $this->libroModel->getCapitulosPorLibro();
        
        // Obtener lecturas del usuario
        $lecturasDB = $this->lecturaModel->obtenerPorUsuario($usuario['id']);
        
        // Convertir lecturas al formato esperado
        $lecturas = $this->formatearLecturas($lecturasDB);
        
        // Calcular progreso
        $versiculosLeidos = $this->progresoService->contarVersiculosLeidos($lecturasDB);
        $progreso = $this->progresoService->calcularProgresoTotal($versiculosLeidos);
        
        // Manejar edición si existe
        $lecturaEditar = null;
        if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
            $id = (int)$_GET['id'];
            foreach ($lecturas as $lectura) {
                if ($lectura['id'] === $id) {
                    $lecturaEditar = $lectura;
                    break;
                }
            }
        }
        
        $this->render('home/index', [
            'usuario' => $usuario,
            'versiculosPorCapitulo' => $versiculosPorCapitulo,
            'capitulosPorLibro' => $capitulosPorLibro,
            'lecturas' => $lecturas,
            'versiculosLeidos' => $versiculosLeidos,
            'progreso' => $progreso,
            'lecturaEditar' => $lecturaEditar,
            'errors' => []
        ]);
    }

    public function store(): void
    {
        $this->authService->requiereAuth();
        
        $usuario = $this->authService->getUsuarioActual();
        $errors = [];
        
        // Validar datos
        $book = $_POST['book'] ?? '';
        $fromCap = $_POST['from_chapter'] ?? '';
        $fromVer = $_POST['from_verse'] ?? '';
        $toCap = $_POST['to_chapter'] ?? '';
        $toVer = $_POST['to_verse'] ?? '';
        
        $errors = $this->validarDatos($book, $fromCap, $fromVer, $toCap, $toVer);
        
        if (empty($errors)) {
            $favoritesInput = array_filter(array_map('trim', explode(',', $_POST['favorites'] ?? '')));
            $favorites = [];
            
            foreach ($favoritesInput as $fav) {
                if (preg_match('/^\d+:\d+$/', $fav)) {
                    $favorites[] = $book . ' ' . $fav;
                }
            }
            
            if (!empty($_POST['edit_id'])) {
                // Actualizar
                $editId = (int)$_POST['edit_id'];
                $this->lecturaModel->actualizar(
                    $editId,
                    $usuario['id'],
                    $_POST['date'] ?? date('Y-m-d'),
                    $book,
                    (int)$fromCap,
                    (int)$fromVer,
                    (int)$toCap,
                    (int)$toVer,
                    $_POST['notes'] ?? '',
                    $favorites
                );
            } else {
                // Crear nueva
                $this->lecturaModel->guardar(
                    $usuario['id'],
                    $_POST['date'] ?? date('Y-m-d'),
                    $book,
                    (int)$fromCap,
                    (int)$fromVer,
                    (int)$toCap,
                    (int)$toVer,
                    $_POST['notes'] ?? '',
                    $favorites
                );
            }
            
            $this->redirect('/');
        }
        
        // Si hay errores, volver a mostrar la página con errores
        $this->mostrarConErrores($errors);
    }
    
    public function delete(): void
    {
        $this->authService->requiereAuth();
        
        if (isset($_GET['id'])) {
            $usuario = $this->authService->getUsuarioActual();
            $id = (int)$_GET['id'];
            $this->lecturaModel->eliminar($id, $usuario['id']);
        }
        
        $this->redirect('/');
    }

    private function formatearLecturas(array $lecturasDB): array
    {
        $lecturas = [];
        foreach ($lecturasDB as $lecturaDB) {
            $favoritos_decoded = json_decode($lecturaDB['favoritos'], true) ?? [];
            $favoritos_formatted = [];
            foreach ($favoritos_decoded as $fav) {
                if (is_string($fav)) {
                    $favoritos_formatted[] = $fav;
                }
            }
            
            $lecturas[] = [
                'id' => $lecturaDB['id'],
                'date' => $lecturaDB['fecha'],
                'book' => $lecturaDB['libro_nombre'],
                'from' => $lecturaDB['libro_nombre'] . ' ' . $lecturaDB['capitulo_desde'] . ':' . $lecturaDB['versiculo_desde'],
                'to' => $lecturaDB['libro_nombre'] . ' ' . $lecturaDB['capitulo_hasta'] . ':' . $lecturaDB['versiculo_hasta'],
                'notes' => $lecturaDB['notas'],
                'favorites' => $favoritos_formatted,
            ];
        }
        return $lecturas;
    }

    private function validarDatos(string $book, string $fromCap, string $fromVer, string $toCap, string $toVer): array
    {
        $errors = [];
        
        if (!$book) {
            $errors[] = "Debe seleccionar un libro.";
        }
        if (!$fromCap || !ctype_digit($fromCap)) {
            $errors[] = "Debe seleccionar capítulo inicial válido.";
        }
        if (!$fromVer || !ctype_digit($fromVer)) {
            $errors[] = "Debe seleccionar versículo inicial válido.";
        }
        if (!$toCap || !ctype_digit($toCap)) {
            $errors[] = "Debe seleccionar capítulo final válido.";
        }
        if (!$toVer || !ctype_digit($toVer)) {
            $errors[] = "Debe seleccionar versículo final válido.";
        }
        
        if (empty($errors)) {
            $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();
            
            if (!isset($versiculosPorCapitulo[$book])) {
                $errors[] = "Libro no válido.";
            } else {
                // Más validaciones...
                $fromCapInt = (int)$fromCap;
                $fromVerInt = (int)$fromVer;
                $toCapInt = (int)$toCap;
                $toVerInt = (int)$toVer;
                
                if ($fromCapInt > $toCapInt || ($fromCapInt === $toCapInt && $fromVerInt > $toVerInt)) {
                    $errors[] = "'Desde' debe ser menor o igual que 'Hasta'.";
                }
            }
        }
        
        return $errors;
    }
    
    private function mostrarConErrores(array $errors): void
    {
        $usuario = $this->authService->getUsuarioActual();
        $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();
        $capitulosPorLibro = $this->libroModel->getCapitulosPorLibro();
        $lecturasDB = $this->lecturaModel->obtenerPorUsuario($usuario['id']);
        $lecturas = $this->formatearLecturas($lecturasDB);
        $versiculosLeidos = $this->progresoService->contarVersiculosLeidos($lecturasDB);
        $progreso = $this->progresoService->calcularProgresoTotal($versiculosLeidos);
        
        $this->render('home/index', [
            'usuario' => $usuario,
            'versiculosPorCapitulo' => $versiculosPorCapitulo,
            'capitulosPorLibro' => $capitulosPorLibro,
            'lecturas' => $lecturas,
            'versiculosLeidos' => $versiculosLeidos,
            'progreso' => $progreso,
            'lecturaEditar' => null,
            'errors' => $errors
        ]);
    }
}