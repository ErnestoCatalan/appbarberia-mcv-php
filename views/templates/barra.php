<div class="barra">
    <p>Hola, <?php echo $nombre ?? '' ?></p>
    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

<?php if(isset($_SESSION['tipo'])): ?>
    <div class="barra-servicios">
        <?php if($_SESSION['tipo'] === 'superadmin'): ?>
            <a class="boton" href="/superadmin">Dashboard SuperAdmin</a>
            <a class="boton" href="/superadmin/barberias">Barberías</a>
            <a class="boton" href="/superadmin/solicitudes">Solicitudes</a>
            <a class="boton" href="/admin">Ver Todas las Citas</a>
            <a class="boton" href="/solicitudes/gestionar">Solicitudes Pendientes</a>
            
        <?php elseif($_SESSION['tipo'] === 'admin_barberia'): ?>
            <a class="boton" href="/admin-barberia">Mi Panel</a>
            <a class="boton" href="/servicios">Mis Servicios</a>
            <a class="boton" href="/servicios/crear">Nuevo Servicio</a>
            
        <?php else: ?>
            <a class="boton" href="/barberias">Barberías</a>
            <a class="boton" href="/cita">Mis Citas</a>
        <?php endif; ?>
    </div>
<?php endif; ?>