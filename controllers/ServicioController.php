<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        $servicios = Servicio::all();

        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }
    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios?exito=1');
                exit;
            }
        }

        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas,
        ]);
    }

    public static function actualizar(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        $id = $_GET['id'];
        if (!is_numeric($id)) return;
        $servicio =  Servicio::find($id);
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                $_SESSION['exito'] = 'Servicio actualizado correctamente';
                header('Location: /servicios/actualizar?id=' . $servicio->id);
                exit;
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }
    
    public static function eliminar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $servicio = Servicio::find($id);
            $servicio->eliminar();
            $_SESSION['exito'] = 'Servicio eliminado correctamente';
            header('Location: /servicios');
            exit;
        }
    }
}