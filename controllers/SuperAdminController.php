<?php 
namespace Controllers;

use Model\Barberia;
use Model\SolicitudRegistro;
use Model\Usuario;
use MVC\Router;

class SuperAdminController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        // SOLUCIÓN SIMPLE: Usar métodos que ya sabemos que funcionan
        $todasBarberias = Barberia::all();
        $todasSolicitudes = SolicitudRegistro::all();
        
        // Contar manualmente
        $totalBarberias = count($todasBarberias);
        $totalSolicitudes = count($todasSolicitudes);
        
        // Contar barberías por estado
        $barberiasAprobadas = 0;
        $barberiasPendientes = 0;
        
        foreach($todasBarberias as $barberia) {
            if($barberia->estado === 'aprobada') {
                $barberiasAprobadas++;
            } elseif($barberia->estado === 'pendiente') {
                $barberiasPendientes++;
            }
        }

        $router->render('superadmin/index', [
            'nombre' => $_SESSION['nombre'],
            'totalBarberias' => $totalBarberias,
            'totalSolicitudes' => $totalSolicitudes,
            'barberiasAprobadas' => $barberiasAprobadas,
            'barberiasPendientes' => $barberiasPendientes
        ]);
    }

    public static function barberias(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        // Usar consulta SQL simple
        $query = "SELECT b.*, u.nombre as nombre_usuario, u.apellido as apellido_usuario 
                  FROM barberias b
                  LEFT JOIN usuarios u ON b.usuario_id = u.id
                  ORDER BY b.creado_en DESC";
        
        $barberias = Barberia::SQL($query);
        $alertas = [];

        $router->render('superadmin/barberias', [
            'nombre' => $_SESSION['nombre'],
            'barberias' => $barberias,
            'alertas' => $alertas
        ]);
    }

    public static function eliminarBarberia() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $barberia = Barberia::find($id);

            if($barberia) {
                // Buscar usuario asociado a la barbería
                $usuario = Usuario::where('barberia_id', $id);
                
                if($usuario) {
                    // Restablecer usuario a cliente
                    $usuario->tipo = 'cliente';
                    $usuario->barberia_id = null;
                    $usuario->actualizar();
                }

                // Eliminar la barbería
                $resultado = $barberia->eliminar();
                
                if($resultado) {
                    $_SESSION['exito'] = 'Barbería eliminada correctamente';
                } else {
                    $_SESSION['error'] = 'Error al eliminar la barbería';
                }
            } else {
                $_SESSION['error'] = 'Barbería no encontrada';
            }
            header('Location: /superadmin/barberias');
        }
    }

    public static function cambiarEstadoBarberia() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            
            $barberia = Barberia::find($id);
            
            if($barberia) {
                $barberia->estado = $estado;
                $resultado = $barberia->actualizar();
                
                if($resultado) {
                    $_SESSION['exito'] = 'Estado de la barbería actualizado correctamente';
                    
                    // Si se aprueba, actualizar usuario asociado
                    if($estado === 'aprobada' && $barberia->usuario_id) {
                        $usuario = Usuario::find($barberia->usuario_id);
                        if($usuario) {
                            $usuario->tipo = 'admin_barberia';
                            $usuario->barberia_id = $barberia->id;
                            $usuario->actualizar();
                        }
                    }
                } else {
                    $_SESSION['error'] = 'Error al actualizar el estado';
                }
            } else {
                $_SESSION['error'] = 'Barbería no encontrada';
            }
            header('Location: /superadmin/barberias');
        }
    }

    public static function solicitudes(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        // Usar consulta SQL simple
        $query = "SELECT sr.*, u.nombre as nombre_usuario, u.apellido as apellido_usuario, u.email as email_usuario
                  FROM solicitudes_registro sr
                  LEFT JOIN usuarios u ON sr.usuario_id = u.id
                  ORDER BY sr.creado_en DESC";
        
        $solicitudes = SolicitudRegistro::SQL($query);
        $alertas = [];

        $router->render('superadmin/solicitudes', [
            'nombre' => $_SESSION['nombre'],
            'solicitudes' => $solicitudes,
            'alertas' => $alertas
        ]);
    }

    public static function eliminarSolicitud() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $solicitud = SolicitudRegistro::find($id);

            if($solicitud) {
                $resultado = $solicitud->eliminar();
                
                if($resultado) {
                    $_SESSION['exito'] = 'Solicitud eliminada correctamente';
                } else {
                    $_SESSION['error'] = 'Error al eliminar la solicitud';
                }
            } else {
                $_SESSION['error'] = 'Solicitud no encontrada';
            }
            header('Location: /superadmin/solicitudes');
        }
    }
}