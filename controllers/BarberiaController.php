<?php 
namespace Controllers;

use Model\Barberia;
use Model\Servicio;
use MVC\Router;

class BarberiaController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $barberias = Barberia::obtenerAprobadas();

        $router->render('barberias/index', [
            'nombre' => $_SESSION['nombre'],
            'barberias' => $barberias
        ]);
    }

    public static function mostrar(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /barberias');
            return;
        }

        $barberia = Barberia::find($id);
        if (!$barberia || $barberia->estado !== 'aprobada') {
            header('Location: /barberias');
            return;
        }

        $servicios = Servicio::porBarberia($id);

        $router->render('barberias/mostrar', [
            'nombre' => $_SESSION['nombre'],
            'barberia' => $barberia,
            'servicios' => $servicios
        ]);
    }
}