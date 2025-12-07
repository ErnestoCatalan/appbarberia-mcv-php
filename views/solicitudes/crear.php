<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<h1 class="nombre-pagina">Solicitud de Registro</h1>
<p class="descripcion-pagina">Completa el formulario para registrar tu barbería</p>

<form class="formulario" method="POST" action="/solicitud">
    <div class="campo">
        <label for="nombre_barberia">Nombre de la Barbería</label>
        <input
            type="text"
            id="nombre_barberia"
            name="nombre_barberia"
            placeholder="Nombre de tu barbería"
            value="<?php echo $solicitud->nombre_barberia; ?>"
            required
        />
    </div>

    <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea
            id="direccion"
            name="direccion"
            placeholder="Dirección completa de tu barbería"
            required
        ><?php echo $solicitud->direccion; ?></textarea>
    </div>

    <div class="campo">
        <label for="telefono">Teléfono</label>
        <input
            type="tel"
            id="telefono"
            name="telefono"
            placeholder="Teléfono de contacto"
            value="<?php echo $solicitud->telefono; ?>"
            required
        />
    </div>

    <div class="campo">
        <label for="email">Email de la Barbería</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Email de contacto"
            value="<?php echo $solicitud->email; ?>"
            required
        />
    </div>

    <div class="campo">
        <label for="nombre_propietario">Nombre del Propietario</label>
        <input
            type="text"
            id="nombre_propietario"
            name="nombre_propietario"
            placeholder="Tu nombre completo"
            value="<?php echo $solicitud->nombre_propietario; ?>"
            required
        />
    </div>

    <input type="submit" class="boton" value="Enviar Solicitud">
</form>

<div class="acciones">
    <a href="/barberias">Volver a Barberías</a>
</div>