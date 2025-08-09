<?php

namespace App\Controllers;

use App\Models\Libro;
use App\Models\Lectura;
use App\Services\ProgresoService;

class BookController extends BaseController
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

    public function show(): void
    {
        $this->authService->requiereAuth();
        
        $usuario = $this->authService->getUsuarioActual();
        $bookName = $_GET['book'] ?? '';
        
        if (empty($bookName)) {
            $this->redirect('/');
            return;
        }

        // Obtener información del libro
        $libro = $this->libroModel->getByName($bookName);
        if (!$libro) {
            $this->redirect('/');
            return;
        }

        // Obtener capítulos del libro
        $capitulos = $this->libroModel->getCapitulosByLibro($libro['id']);
        $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();
        
        // Obtener lecturas del usuario para este libro
        $todasLecturas = $this->lecturaModel->obtenerPorUsuario($usuario['id']);
        $lecturasLibro = array_filter($todasLecturas, function($lectura) use ($bookName) {
            return $lectura['libro_nombre'] === $bookName;
        });

        // Calcular progreso específico del libro
        $versiculosLeidos = $this->progresoService->contarVersiculosLeidos($todasLecturas);
        $versiculosLeidosLibro = $versiculosLeidos[$bookName] ?? 0;
        $totalVersiculosLibro = array_sum($versiculosPorCapitulo[$bookName] ?? []);
        $porcentajeLibro = $totalVersiculosLibro > 0 ? round(($versiculosLeidosLibro / $totalVersiculosLibro) * 100, 1) : 0;

        // Crear mapa de capítulos leídos
        $capitulosLeidos = [];
        foreach ($lecturasLibro as $lectura) {
            for ($cap = $lectura['capitulo_desde']; $cap <= $lectura['capitulo_hasta']; $cap++) {
                if (!isset($capitulosLeidos[$cap])) {
                    $capitulosLeidos[$cap] = [];
                }
                
                $verseStart = ($cap == $lectura['capitulo_desde']) ? $lectura['versiculo_desde'] : 1;
                $verseEnd = ($cap == $lectura['capitulo_hasta']) ? $lectura['versiculo_hasta'] : $versiculosPorCapitulo[$bookName][$cap];
                
                for ($verse = $verseStart; $verse <= $verseEnd; $verse++) {
                    $capitulosLeidos[$cap][] = $verse;
                }
            }
        }

        $this->render('book/show', [
            'usuario' => $usuario,
            'libro' => $libro,
            'bookName' => $bookName,
            'capitulos' => $capitulos,
            'versiculosPorCapitulo' => $versiculosPorCapitulo[$bookName] ?? [],
            'lecturasLibro' => $lecturasLibro,
            'versiculosLeidosLibro' => $versiculosLeidosLibro,
            'totalVersiculosLibro' => $totalVersiculosLibro,
            'porcentajeLibro' => $porcentajeLibro,
            'capitulosLeidos' => $capitulosLeidos
        ]);
    }
}