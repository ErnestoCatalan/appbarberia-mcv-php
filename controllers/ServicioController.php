<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdminBarberia();

        // Obtener solo servicios de la barbería del usuario
        $servicios = Servicio::porBarberia($_SESSION['barberia_id']);

        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        isAdminBarberia();

        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $servicio->barberia_id = $_SESSION['barberia_id'];

            $alertas = $servicio->validar();

            if(empty($alertas)) {
                // Procesar imagen si se subió
                if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                    $nombreImagen = self::subirImagen($_FILES['imagen']);
                    if($nombreImagen) {
                        $servicio->imagen = $nombreImagen;
                    }
                }

                $resultado = $servicio->guardar();
                if($resultado['resultado']) {
                    $_SESSION['exito'] = 'Servicio creado correctamente';
                    header('Location: /servicios');
                    exit;
                } else {
                    error_log("Error al guardar servicio: " . print_r($resultado, true));
                    $alertas['error'][] = 'Error al guardar el servicio. Verifica los datos.';
                }
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
        isAdminBarberia();

        $id = $_GET['id'];
        if (!is_numeric($id)) {
            header('Location: /servicios');
            return;
        }
        
        $servicio = Servicio::find($id);
        
        // Verificar que el servicio pertenezca a la barbería del usuario
        if(!$servicio || $servicio->barberia_id !== $_SESSION['barberia_id']) {
            header('Location: /servicios');
            return;
        }

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $servicio->barberia_id = $_SESSION['barberia_id']; // Reforzar por seguridad

            $alertas = $servicio->validar();

            if(empty($alertas)) {
                // Procesar eliminación de imagen
                if(isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] === 'on') {
                    if($servicio->imagen) {
                        self::eliminarImagen($servicio->imagen);
                        $servicio->imagen = '';
                    }
                }
                
                // Procesar nueva imagen (si no se está eliminando)
                if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0 && 
                   (!isset($_POST['eliminar_imagen']) || $_POST['eliminar_imagen'] !== 'on')) {
                    // Eliminar imagen anterior si existe
                    if($servicio->imagen) {
                        self::eliminarImagen($servicio->imagen);
                    }
                    
                    $nombreImagen = self::subirImagen($_FILES['imagen']);
                    if($nombreImagen) {
                        $servicio->imagen = $nombreImagen;
                    }
                }

                $resultado = $servicio->actualizar();
                if($resultado) {
                    $_SESSION['exito'] = 'Servicio actualizado correctamente';
                    header('Location: /servicios');
                    exit;
                } else {
                    $alertas['error'][] = 'Error al actualizar el servicio';
                }
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
        isAdminBarberia();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $servicio = Servicio::find($id);
            
            // Verificar que el servicio pertenezca a la barbería del usuario
            if($servicio && $servicio->barberia_id === $_SESSION['barberia_id']) {
                // Eliminar imagen si existe
                if($servicio->imagen) {
                    self::eliminarImagen($servicio->imagen);
                }
                
                $servicio->eliminar();
                $_SESSION['exito'] = 'Servicio eliminado correctamente';
            }
            header('Location: /servicios');
            exit;
        }
    }

    // Método para subir imagen
    private static function subirImagen($imagen) {
        $extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
        $nombreUnico = md5(uniqid(rand(), true)) . '.' . $extension;
        
        // Directorio de uploads
        $ruta = '/home/appbarberia/www/public/uploads/servicios/' . $nombreImagen;
        
        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0775, true);
        }
        
        $rutaDestino = $directorio . $nombreUnico;
        
        if(move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            // Redimensionar imagen si es muy grande
            self::optimizarImagen($rutaDestino, 800, 600);
            return $nombreUnico;
        }
        
        error_log("Error al subir imagen: " . print_r($imagen, true));
        return null;
    }

    // Método para optimizar imagen (CORREGIDO)
    private static function optimizarImagen($ruta, $anchoMax = 800, $altoMax = 600) {
        // Verificar si GD está instalado
        if (!function_exists('gd_info')) {
            error_log("GD library no está instalada");
            return false;
        }
        
        $info = @getimagesize($ruta);
        
        if($info === false) {
            error_log("No se pudo obtener información de la imagen: $ruta");
            return false;
        }
        
        list($ancho, $alto, $tipo) = $info;
        
        // No redimensionar si es más pequeño que el máximo
        if($ancho <= $anchoMax && $alto <= $altoMax) {
            return true;
        }
        
        // Calcular nuevas dimensiones manteniendo proporción
        $ratio = $ancho / $alto;
        
        if($ancho > $alto) {
            $nuevoAncho = $anchoMax;
            $nuevoAlto = (int) round($anchoMax / $ratio);
        } else {
            $nuevoAlto = $altoMax;
            $nuevoAncho = (int) round($altoMax * $ratio);
        }
        
        // Asegurar dimensiones mínimas
        $nuevoAncho = max($nuevoAncho, 100);
        $nuevoAlto = max($nuevoAlto, 100);
        
        // Crear imagen según tipo
        switch($tipo) {
            case IMAGETYPE_JPEG:
                $origen = @imagecreatefromjpeg($ruta);
                break;
            case IMAGETYPE_PNG:
                $origen = @imagecreatefrompng($ruta);
                break;
            case IMAGETYPE_GIF:
                $origen = @imagecreatefromgif($ruta);
                break;
            case IMAGETYPE_WEBP:
                $origen = @imagecreatefromwebp($ruta);
                break;
            default:
                error_log("Tipo de imagen no soportado: $tipo");
                return false;
        }
        
        if(!$origen) {
            error_log("No se pudo crear imagen desde archivo: $ruta");
            return false;
        }
        
        // Crear lienzo para nueva imagen
        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        
        // Preservar transparencia en PNG y GIF
        if($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
            imagecolortransparent($destino, imagecolorallocatealpha($destino, 0, 0, 0, 127));
            imagealphablending($destino, false);
            imagesavealpha($destino, true);
        }
        
        // Redimensionar
        imagecopyresampled(
            $destino, $origen, 
            0, 0, 0, 0, 
            $nuevoAncho, $nuevoAlto, 
            $ancho, $alto
        );
        
        // Guardar imagen optimizada
        $resultado = false;
        switch($tipo) {
            case IMAGETYPE_JPEG:
                $resultado = imagejpeg($destino, $ruta, 85);
                break;
            case IMAGETYPE_PNG:
                $resultado = imagepng($destino, $ruta, 8);
                break;
            case IMAGETYPE_GIF:
                $resultado = imagegif($destino, $ruta);
                break;
            case IMAGETYPE_WEBP:
                $resultado = imagewebp($destino, $ruta, 85);
                break;
        }
        
        // Liberar memoria
        imagedestroy($origen);
        imagedestroy($destino);
        
        if(!$resultado) {
            error_log("Error al guardar imagen optimizada: $ruta");
        }
        
        return $resultado;
    }

    // Método para eliminar imagen
    private static function eliminarImagen($nombreImagen) {
        $ruta = '/home/appbarberia/www/public/uploads/servicios/' . $nombreImagen;
        if(file_exists($ruta)) {
            return unlink($ruta);
        }
        return false;
    }
}