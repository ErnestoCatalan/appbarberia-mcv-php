<?php 

namespace Model;

class Servicio extends ActiveRecord {
    // base de datos
    protected static $tabla = 'servicios';
    protected static $columnasDB = ['id', 'nombre', 'precio'];

    public $id;
    public $nombre;
    public $precio;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? null;
        $this->precio = $args['precio'] ?? null;
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

    return self::$alertas;
}

}

?>