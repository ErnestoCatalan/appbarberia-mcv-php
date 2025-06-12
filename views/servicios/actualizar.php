<h1 class="nombre-pagina">Actualizar Servicio</h1>
<p class="descripcion-pagina">Llena todos los campos para actualizar el servicio</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';
    include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<form method="POST" class="formulario">
    <?php include_once __DIR__ . '/formulario.php' ?>
    <input type="submit" class="boton" value="Actualizar Servicio">
</form>
