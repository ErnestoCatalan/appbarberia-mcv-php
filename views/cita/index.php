<?php
include_once __DIR__ . '/../templates/barra.php';

$barberia_id = $_GET['barberia_id'] ?? null;
?>

<h1 class="nombre-pagina">Crear Nueva Cita</h1>
<p class="descripcion-pagina">Elige tus servicios y coloca tus datos</p>

<?php if(!$barberia_id): ?>
    <div class="alerta error">
        <p>No se ha seleccionado una barbería. <a href="/barberias">Elige una barbería primero</a></p>
    </div>
<?php else: ?>
    <div id="app">
        <nav class="tabs">
            <button class="actual" type="button" data-paso="1">Servicios</button>
            <button type="button" data-paso="2">Información Cita</button>
            <button type="button" data-paso="3">Resumen</button>
        </nav>

        <div id="paso-1" class="seccion mostrar">
            <h2>Servicios Disponibles</h2>
            <p class="text-center">Elige tus servicios a continuación</p>
            <div id="servicios" class="listado-servicios-cita"></div>
        </div>
        <div id="paso-2" class="seccion">
            <h2>Tus Datos y Cita</h2>
            <p class="text-center">Coloca tus datos y fecha de tu cita</p>

            <form class="formulario">
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input
                        id="nombre"
                        type="text"
                        placeholder="Tu Nombre"
                        value="<?php echo htmlspecialchars($nombre ?? ''); ?>"
                        disabled
                    />
                </div>

                <div class="campo">
                    <label for="fecha">Fecha</label>
                    <input
                        id="fecha"
                        type="date"
                        min="<?php echo date('Y-m-d', strtotime('+1 day') ); ?>"
                    />
                </div>

                <div class="campo">
                    <label for="hora">Hora</label>
                    <input
                        id="hora"
                        type="time"
                    />
                </div>
                <input type="hidden" id="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">
                <input type="hidden" id="barberia_id" value="<?php echo htmlspecialchars($barberia_id); ?>">
            </form>
        </div>
        <div id="paso-3" class="seccion">
            <h2>Resumen</h2>
            <p class="text-center">Verifica que la información sea correcta</p>
            <div class="contenido-resumen"></div>
        </div>

        <div class="paginacion">
            <button
                id="anterior"
                class="boton ocultar"
            >&laquo; Anterior</button>

            <button
                id="siguiente"
                class="boton"
            >Siguiente &raquo;</button>
        </div>
    </div>
    
    <style>
    /* Tu CSS existente para listado-servicios-cita */
    .listado-servicios-cita {
        display: grid;
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    @media (min-width: 768px) {
        .listado-servicios-cita {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .listado-servicios-cita {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    .servicio-cita {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }
    
    .servicio-cita:hover {
        transform: translateY(-5px);
        border-color: rgba(212, 175, 55, 0.3);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .servicio-cita.seleccionado {
        border-color: #d4af37;
        background: rgba(212, 175, 55, 0.1);
    }
    
    .servicio-cita.seleccionado .servicio-info-cita h3,
    .servicio-cita.seleccionado .servicio-info-cita .precio-servicio {
        color: #d4af37;
    }
    
    .servicio-imagen-cita {
        height: 150px;
        overflow: hidden;
        position: relative;
    }
    
    .servicio-imagen-cita img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .servicio-imagen-cita:hover img {
        transform: scale(1.05);
    }
    
    .servicio-info-cita {
        padding: 1.5rem;
        text-align: center;
    }
    
    .servicio-info-cita h3 {
        font-size: 1.6rem;
        color: white;
        margin-bottom: 0.5rem;
        transition: color 0.3s ease;
    }
    
    .descripcion-servicio {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.3rem;
        line-height: 1.4;
        margin-bottom: 1rem;
        min-height: 40px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .precio-servicio {
        color: #4caf50;
        font-weight: bold;
        font-size: 1.8rem;
        margin: 0;
        transition: color 0.3s ease;
    }
    
    .duracion-servicio {
        color: rgba(255, 255, 255, 0.6);
        font-size: 1.3rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }
    
    .duracion-servicio i {
        font-size: 1.2rem;
    }
    
    /* Estilos para las secciones */
    .seccion {
        padding: 5rem 0;
        display: none;
    }
    
    .seccion.mostrar {
        display: block;
    }
    
    .ocultar {
        display: none;
    }
    </style>
<?php endif; ?>

<?php 
    $script = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script src='build/js/app.js'></script>
    ";
?>