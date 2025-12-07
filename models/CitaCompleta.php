<?php
namespace Model;

class CitaCompleta extends ActiveRecord {
    protected static $tabla = 'citas';
    protected static $columnasDB = [
        'id', 'fecha', 'hora', 'usuarioId', 'barberia_id',
        'barberia_nombre', 'barberia_direccion', 'barberia_telefono', 'barberia_email',
        'servicios', 'total', 'cantidad_servicios'
    ];

    public $id;
    public $fecha;
    public $hora;
    public $usuarioId;
    public $barberia_id;
    public $barberia_nombre;
    public $barberia_direccion;
    public $barberia_telefono;
    public $barberia_email;
    public $servicios;
    public $total;
    public $cantidad_servicios;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->usuarioId = $args['usuarioId'] ?? null;
        $this->barberia_id = $args['barberia_id'] ?? null;
        $this->barberia_nombre = $args['barberia_nombre'] ?? '';
        $this->barberia_direccion = $args['barberia_direccion'] ?? '';
        $this->barberia_telefono = $args['barberia_telefono'] ?? '';
        $this->barberia_email = $args['barberia_email'] ?? '';
        $this->servicios = $args['servicios'] ?? '';
        $this->total = $args['total'] ?? 0;
        $this->cantidad_servicios = $args['cantidad_servicios'] ?? 0;
    }
}