<?php
namespace Controllers;

use Model\SolicitudRegistro;
use Model\Barberia;
use Model\Usuario;
use Classes\Email;
use MVC\Router;

class SolicitudController {
    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAuth();
        $alertas = [];
        $solicitud = new SolicitudRegistro;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $solicitud = new SolicitudRegistro($_POST);
            $solicitud->usuario_id = $_SESSION['id'];

            $alertas = $solicitud->validar();

            if(empty($alertas)) {
                $resultado = $solicitud->guardar();
                if($resultado) {
                    // Enviar email de notificación al superadmin
                    self::notificarSuperAdmin($solicitud);
                    
                    $_SESSION['exito'] = 'Solicitud enviada correctamente. Te notificaremos cuando sea revisada.';
                    header('Location: /solicitud/mensaje');
                    return;
                }
            }
        }

        $router->render('solicitudes/crear', [
            'nombre' => $_SESSION['nombre'],
            'solicitud' => $solicitud,
            'alertas' => $alertas
        ]);
    }

    public static function gestionar(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        $solicitudes = SolicitudRegistro::all();
        $alertas = [];

        $router->render('solicitudes/gestionar', [
            'nombre' => $_SESSION['nombre'],
            'solicitudes' => $solicitudes,
            'alertas' => $alertas
        ]);
    }

   public static function aprobar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $solicitud = SolicitudRegistro::find($id);

            if($solicitud && $solicitud->estado === 'pendiente') {
                // 1. Crear la barbería con el horario proporcionado
                $barberia = new Barberia([
                    'nombre' => $solicitud->nombre_barberia,
                    'direccion' => $solicitud->direccion,
                    'telefono' => $solicitud->telefono,
                    'email' => $solicitud->email,
                    'descripcion' => '',
                    'imagen' => '',
                    'horario_apertura' => $solicitud->horario_apertura, // Usar horario del formulario
                    'horario_cierre' => $solicitud->horario_cierre, // Usar horario del formulario
                    'estado' => 'aprobada',
                    'usuario_id' => $solicitud->usuario_id
                ]);
                
                $resultadoBarberia = $barberia->guardar();
                
                if($resultadoBarberia['resultado']) {
                    // 2. Obtener el usuario y actualizar su tipo y barberia_id
                    $usuario = Usuario::find($solicitud->usuario_id);
                    
                    if($usuario) {
                        $usuario->tipo = 'admin_barberia';
                        $usuario->barberia_id = $resultadoBarberia['id'];
                        
                        $resultadoUsuario = $usuario->actualizar();
                        
                        if($resultadoUsuario) {
                            // 3. Actualizar estado de la solicitud
                            $solicitud->estado = 'aprobada';
                            $solicitud->guardar();

                            // 4. Enviar email de notificación al barbero
                            $email = new Email($usuario->email, $usuario->nombre, '');
                            $email->enviarAprobacionBarberia();

                            $_SESSION['exito'] = 'Solicitud aprobada correctamente y barbero notificado';
                        } else {
                            $_SESSION['error'] = 'Error al actualizar el usuario';
                            $barberia->eliminar();
                        }
                    } else {
                        $_SESSION['error'] = 'Usuario no encontrado';
                        $barberia->eliminar();
                    }
                } else {
                    $_SESSION['error'] = 'Error al crear la barbería: ' . ($resultadoBarberia['error'] ?? 'Error desconocido');
                }
            } else {
                $_SESSION['error'] = 'Solicitud no encontrada o ya procesada';
            }
            header('Location: /solicitudes/gestionar');
        }
    }

   public static function rechazar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isSuperAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $solicitud = SolicitudRegistro::find($id);

            if($solicitud && $solicitud->estado === 'pendiente') {
                $solicitud->estado = 'rechazada';
                $solicitud->guardar();

                // Enviar email de rechazo
                $usuario = Usuario::find($solicitud->usuario_id);
                if($usuario) {
                    $email = new Email($usuario->email, $usuario->nombre, '');
                    $email->enviarRechazoBarberia();
                }

                $_SESSION['exito'] = 'Solicitud rechazada correctamente';
            } else {
                $_SESSION['error'] = 'Solicitud no encontrada o ya procesada';
            }
            header('Location: /solicitudes/gestionar');
        }
    }

    public static function mensaje(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAuth();

        $router->render('solicitudes/mensaje', [
            'nombre' => $_SESSION['nombre']
        ]);
    }

    private static function notificarSuperAdmin($solicitud) {
        // Obtener el usuario que hizo la solicitud para su información
        $usuario = Usuario::find($solicitud->usuario_id);
        
        // Obtener el email del superadmin (buscamos al usuario con tipo 'superadmin')
        $superAdmin = Usuario::SQL("SELECT * FROM usuarios WHERE tipo = 'superadmin' LIMIT 1");
        
        if($usuario && !empty($superAdmin)) {
            $superAdminEmail = $superAdmin[0]->email;
            $superAdminNombre = $superAdmin[0]->nombre;
            
            // Crear instancia de Email y enviar notificación
            $email = new Email($superAdminEmail, $superAdminNombre, '');
            $email->enviarNotificacionSolicitudSuperAdmin(
                $solicitud->nombre_barberia,
                $usuario->nombre . " " . $usuario->apellido,
                $usuario->email,
                $solicitud->telefono,
                $solicitud->direccion
            );
        }
    }
}