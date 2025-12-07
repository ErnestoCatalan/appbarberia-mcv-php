<?php
include_once __DIR__ . '/../templates/barra.php';
?>

<h1 class="nombre-pagina">Nuestras Barberías</h1>
<p class="descripcion-pagina">Elige tu barbería favorita</p>

<div class="listado-barberias">
    <?php if(empty($barberias)): ?>
        <p class="text-center">No hay barberías disponibles en este momento.</p>
    <?php else: ?>
        <?php foreach($barberias as $barberia): ?>
            <div class="barberia">
                <div class="barberia-info">
                    <h3><?php echo $barberia->nombre; ?></h3>
                    <p><?php echo $barberia->direccion; ?></p>
                    <p>Tel: <?php echo $barberia->telefono; ?></p>
                    <p><?php echo $barberia->descripcion; ?></p>
                </div>
                <div class="barberia-acciones">
                    <a href="/barberia?id=<?php echo $barberia->id; ?>" class="boton">Ver Servicios</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if(!isset($_SESSION['admin'])): ?>
<div class="acciones-centro">
    <a href="/solicitud" class="boton">¿Eres barbero? Regístrate aquí</a>
</div>
<?php endif; ?>