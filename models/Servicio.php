<?php 

namespace Model;

class Servicio extends ActiveRecord {
    protected static $tabla = 'servicios';
    protected static $columnasDB = ['id', 'nombre', 'precio', 'barberia_id', 'duracion', 'imagen', 'descripcion'];

    public $id;
    public $nombre;
    public $precio;
    public $barberia_id;
    public $duracion;
    public $imagen;
    public $descripcion;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->precio = $args['precio'] ?? null;
        $this->barberia_id = $args['barberia_id'] ?? null;
        $this->duracion = $args['duracion'] ?? 30;
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
    }

    public function validar() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Servicio es Obligatorio';
        }
        if ($this->precio === '' || $this->precio === null) {
            self::$alertas['error'][] = 'El Precio del Servicio es Obligatorio';
        } elseif (!is_numeric($this->precio)) {
            self::$alertas['error'][] = 'El precio debe ser un valor numérico';
        }
        if (!$this->barberia_id) {
            self::$alertas['error'][] = 'El servicio debe estar asociado a una barbería';
        }
        
        // Validar imagen si se sube
        if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if(!in_array($extension, $extensionesPermitidas)) {
                self::$alertas['error'][] = 'Formato de imagen no permitido. Use JPG, PNG o GIF';
            }
            
            if($_FILES['imagen']['size'] > 5 * 1024 * 1024) { // 5MB
                self::$alertas['error'][] = 'La imagen es muy grande. Máximo 5MB';
            }
        }

        return self::$alertas;
    }

    // Obtener servicios por barbería
    public static function porBarberia($barberia_id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE barberia_id = {$barberia_id}";
        return self::consultarSQL($query);
    }

}

?>