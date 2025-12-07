<h1 class="nombre-pagina">Panel SuperAdmin</h1>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<div class="dashboard-cards">
    <div class="card">
        <h3>Barberías Totales</h3>
        <p class="numero"><?php echo $totalBarberias; ?></p>
        <a href="/superadmin/barberias" class="btn">Ver Todas</a>
    </div>
    
    <div class="card">
        <h3>Solicitudes Totales</h3>
        <p class="numero"><?php echo $totalSolicitudes; ?></p>
        <a href="/superadmin/solicitudes" class="btn">Ver Solicitudes</a>
    </div>
    
    <div class="card">
        <h3>Barberías Aprobadas</h3>
        <p class="numero"><?php echo $barberiasAprobadas; ?></p>
    </div>
    
    <div class="card">
        <h3>Barberías Pendientes</h3>
        <p class="numero"><?php echo $barberiasPendientes; ?></p>
    </div>
</div>

<div class="acciones-rapidas">
    <h2>Acciones Rápidas</h2>
    <div class="botones">
        <a href="/admin" class="boton">Ver Todas las Citas</a>
        <a href="/superadmin/barberias" class="boton">Gestionar Barberías</a>
        <a href="/superadmin/solicitudes" class="boton">Gestionar Solicitudes</a>
        <a href="/solicitudes/gestionar" class="boton">Solicitudes Pendientes</a>
    </div>
</div>

<?php
$script = "
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmar eliminaciones
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                if(!confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
";