<?php 
namespace Controllers;

use Model\AdminCita;
use Model\Barberia;
use MVC\Router;

class AdminBarberiaController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdminBarberia();

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode('-', $fecha);

        if( !checkdate( $fechas[1], $fechas[2], $fechas[0] )) {
            header('Location: /404');
        }

        // Obtener información de la barbería
        $barberia_id = $_SESSION['barberia_id'];
        $barberia = Barberia::find($barberia_id);

        // Consulta para las citas
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasServicios ";
        $consulta .= " ON citasServicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasServicios.servicioId ";
        $consulta .= " WHERE fecha =  '{$fecha}' AND citas.barberia_id = '{$barberia_id}'";

        $citas = AdminCita::SQL($consulta);

        $alertas = []; // INICIALIZAR LA VARIABLE alertas

        $router->render('admin-barberia/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha,
            'barberia' => $barberia,
            'alertas' => $alertas // PASAR LA VARIABLE
        ]);
    }

    public static function actualizarDireccion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdminBarberia();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barberia_id = $_SESSION['barberia_id'];
            $direccion = $_POST['direccion'] ?? '';
            $latitud = $_POST['latitud'] ?? null;
            $longitud = $_POST['longitud'] ?? null;
            
            $barberia = Barberia::find($barberia_id);
            
            if($barberia) {
                $barberia->direccion = $direccion;
                if($latitud && $longitud) {
                    $barberia->latitud = $latitud;
                    $barberia->longitud = $longitud;
                }
                
                $resultado = $barberia->actualizar();
                
                if($resultado) {
                    $_SESSION['exito'] = 'Dirección actualizada correctamente';
                } else {
                    $_SESSION['error'] = 'Error al actualizar la dirección';
                }
            } else {
                $_SESSION['error'] = 'Barbería no encontrada';
            }
            header('Location: /admin-barberia');
        }
    }
}