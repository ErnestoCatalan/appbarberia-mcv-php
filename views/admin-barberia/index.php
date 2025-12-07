<h1 class="nombre-pagina">Panel de Mi Barbería</h1>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php'; 
include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<!-- Información de la barbería -->
<div class="info-barberia">
    <h2>Información de tu Barbería</h2>
    <div class="info-card">
        <h3><?php echo htmlspecialchars($barberia->nombre); ?></h3>
        
        <form action="/admin-barberia/actualizar-direccion" method="POST" class="form-direccion" id="formDireccion">
            <!-- Mapa de OpenStreetMap -->
            <div class="mapa-container">
                <h4><i class="fas fa-map-marked-alt"></i> Selecciona la ubicación exacta de tu barbería:</h4>
                <div id="mapa" style="height: 400px; width: 100%; border-radius: 8px; margin: 1rem 0;"></div>
                <div class="controles-mapa">
                    <button type="button" class="boton-secundario" onclick="usarMiUbicacion()">
                        <i class="fas fa-location-arrow"></i> Usar mi ubicación actual
                    </button>
                </div>
                <p class="instrucciones-mapa">
                    <i class="fas fa-mouse-pointer"></i> 
                    Arrastra el marcador para ajustar la ubicación exacta de tu barbería.
                </p>
            </div>
            
            <!-- Campos ocultos para coordenadas y dirección -->
            <input type="hidden" id="latitud" name="latitud" value="<?php echo htmlspecialchars($barberia->latitud ?? ''); ?>">
            <input type="hidden" id="longitud" name="longitud" value="<?php echo htmlspecialchars($barberia->longitud ?? ''); ?>">
            <input type="hidden" id="direccion" name="direccion" value="<?php echo htmlspecialchars($barberia->direccion); ?>">
            
            <div class="coordenadas-info">
                <p><strong>Coordenadas actuales:</strong> 
                    <span id="coordenadasActuales">
                        <?php if($barberia->latitud && $barberia->longitud): ?>
                            Lat: <?php echo number_format($barberia->latitud, 6); ?>, Lng: <?php echo number_format($barberia->longitud, 6); ?>
                        <?php else: ?>
                            No definidas
                        <?php endif; ?>
                    </span>
                </p>
                <p id="direccionActual" style="margin-top: 0.5rem; color: rgba(255, 255, 255, 0.8);">
                    <?php if($barberia->direccion): ?>
                        <strong>Dirección:</strong> <?php echo htmlspecialchars($barberia->direccion); ?>
                    <?php endif; ?>
                </p>
            </div>
            
            <button type="submit" class="boton">
                <i class="fas fa-save"></i> Guardar Dirección y Ubicación
            </button>
        </form>
        
        <?php if($barberia->latitud && $barberia->longitud): ?>
        <div class="mini-mapa-container">
            <h4><i class="fas fa-map-pin"></i> Ubicación actual guardada:</h4>
            <p class="direccion-actual"><?php echo htmlspecialchars($barberia->direccion); ?></p>
            <div id="miniMapa" style="height: 300px; width: 100%; border-radius: 8px;"></div>
        </div>
        <?php endif; ?>
        
        <div class="info-detalles">
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($barberia->telefono); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($barberia->email); ?></p>
            <p><strong>Horario:</strong> 
                <?php echo date('g:i A', strtotime($barberia->horario_apertura)); ?> - 
                <?php echo date('g:i A', strtotime($barberia->horario_cierre)); ?>
            </p>
            <p><strong>Estado:</strong> 
                <span class="estado estado-<?php echo $barberia->estado; ?>">
                    <?php echo ucfirst($barberia->estado); ?>
                </span>
            </p>
        </div>
    </div>
</div>

<!-- Resto del código (buscador de citas y listado) permanece igual -->
<h2>Buscar Citas</h2>
<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input
                type="date"
                id="fecha"
                name="fecha"
                value="<?php echo $fecha; ?>"
            >
        </div>
    </form>
</div>

<?php
    if(count($citas) === 0) {
        echo "<h2>No Hay Citas en esta fecha</h2>";
    }
?>

<div id="citas-admin">
    <ul class="citas">
        <?php
        $idCita = 0;
        foreach ($citas as $key => $cita) {
            if ($idCita !== $cita->id) {
                $total = 0;
        ?>
                <li>
                    <p>ID: <span><?php echo $cita->id; ?></span></p>
                    <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                    <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                    <p>Email: <span><?php echo $cita->email; ?></span></p>
                    <p>Telefono: <span><?php echo $cita->telefono; ?></span></p>

                    <h3>Servicios</h3>
                <?php
                $idCita = $cita->id;
            }
            $total += $cita->precio;
                ?>
                <p class="servicio"><?php echo $cita->servicio . " $" . $cita->precio; ?></p>

                <?php
                $actual = $cita->id;
                $proximo = $citas[$key + 1]->id ?? 0;

                if (esUltimo($actual, $proximo)) { ?>
                    <p class="total">Total: <span>$<?php echo $total; ?></span></p>

                    <form action="/api/eliminar" method="POST">
                        <input type="hidden" name="id" value="<?php echo $cita->id; ?>">
                        <input type="submit" class="boton-eliminar" value="Eliminar">
                    </form>
            <?php }
            }
            ?>
    </ul>
</div>

<?php 
$script = "
<!-- Leaflet CSS -->
<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' crossorigin='' />
<!-- Leaflet JS -->
<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js' crossorigin=''></script>
<!-- Leaflet Control Geocoder (para búsqueda) -->
<link rel='stylesheet' href='https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css' />
<script src='https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js'></script>

<script src='build/js/buscador.js'></script>
<script>
    let mapa;
    let marcador;
    let geocoderControl;
    
    // Coordenadas iniciales (México City como fallback)
    const latInicial = " . ($barberia->latitud ?? '19.4326') . ";
    const lngInicial = " . ($barberia->longitud ?? '-99.1332') . ";
    
    function initMap() {
        // Inicializar mapa
        mapa = L.map('mapa').setView([latInicial, lngInicial], 15);
        
        // Agregar capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(mapa);
        
        // Agregar control de búsqueda directamente en el mapa
        geocoderControl = L.Control.geocoder({
            defaultMarkGeocode: true,
            placeholder: 'Buscar dirección de tu barbería...',
            errorMessage: 'Dirección no encontrada'
        }).on('markgeocode', function(e) {
            const { center, name } = e.geocode;
            mapa.setView(center, 17);
            
            // Actualizar campo de dirección oculto
            document.getElementById('direccion').value = name;
            
            // Colocar marcador
            colocarMarcador(center.lat, center.lng);
            
            // Actualizar coordenadas y mostrar dirección
            actualizarCoordenadas(center.lat, center.lng);
            document.getElementById('direccionActual').innerHTML = '<strong>Dirección:</strong> ' + name;
        }).addTo(mapa);
        
        // Si ya hay coordenadas, colocar marcador
        if(latInicial && lngInicial) {
            colocarMarcador(latInicial, lngInicial);
        } else {
            // Si no hay coordenadas, usar geolocalización
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(posicion) {
                        const lat = posicion.coords.latitude;
                        const lng = posicion.coords.longitude;
                        
                        mapa.setView([lat, lng], 15);
                        colocarMarcador(lat, lng);
                        actualizarCoordenadas(lat, lng);
                        
                        // Obtener dirección por coordenadas
                        obtenerDireccionDesdeCoordenadas(lat, lng);
                    },
                    function(error) {
                        console.log('Error geolocalización:', error);
                        colocarMarcador(latInicial, lngInicial);
                    }
                );
            } else {
                colocarMarcador(latInicial, lngInicial);
            }
        }
        
        // Permitir agregar marcador al hacer clic en el mapa
        mapa.on('click', function(e) {
            colocarMarcador(e.latlng.lat, e.latlng.lng);
            actualizarCoordenadas(e.latlng.lat, e.latlng.lng);
            
            // Obtener dirección por coordenadas
            obtenerDireccionDesdeCoordenadas(e.latlng.lat, e.latlng.lng);
        });
    }
    
    function colocarMarcador(lat, lng) {
        // Remover marcador existente
        if(marcador) {
            mapa.removeLayer(marcador);
        }
        
        // Crear nuevo marcador
        marcador = L.marker([lat, lng], {
            draggable: true
        }).addTo(mapa);
        
        // Evento cuando se arrastra el marcador
        marcador.on('dragend', function(e) {
            const posicion = marcador.getLatLng();
            actualizarCoordenadas(posicion.lat, posicion.lng);
            obtenerDireccionDesdeCoordenadas(posicion.lat, posicion.lng);
        });
        
        // Mostrar popup
        marcador.bindPopup('Ubicación de tu barbería').openPopup();
    }
    
    function actualizarCoordenadas(lat, lng) {
        document.getElementById('latitud').value = lat;
        document.getElementById('longitud').value = lng;
        
        // Actualizar display
        document.getElementById('coordenadasActuales').textContent = 
            'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);
    }
    
    function obtenerDireccionDesdeCoordenadas(lat, lng) {
        // Usar Nominatim (servicio de geocodificación de OpenStreetMap)
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=\${lat}&lon=\${lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                if(data.display_name) {
                    document.getElementById('direccion').value = data.display_name;
                    document.getElementById('direccionActual').innerHTML = '<strong>Dirección:</strong> ' + data.display_name;
                }
            })
            .catch(error => console.log('Error obteniendo dirección:', error));
    }
    
    function usarMiUbicacion() {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(posicion) {
                    const lat = posicion.coords.latitude;
                    const lng = posicion.coords.longitude;
                    
                    mapa.setView([lat, lng], 17);
                    colocarMarcador(lat, lng);
                    actualizarCoordenadas(lat, lng);
                    obtenerDireccionDesdeCoordenadas(lat, lng);
                },
                function(error) {
                    alert('No se pudo obtener tu ubicación. Asegúrate de permitir el acceso a la ubicación.');
                }
            );
        } else {
            alert('Tu navegador no soporta geolocalización');
        }
    }
    
    // Validar formulario
    document.getElementById('formDireccion').addEventListener('submit', function(e) {
        const direccion = document.getElementById('direccion').value.trim();
        const latitud = document.getElementById('latitud').value;
        const longitud = document.getElementById('longitud').value;
        
        if(!direccion) {
            e.preventDefault();
            alert('Por favor, selecciona la ubicación de tu barbería en el mapa');
            return false;
        }
        
        if(!latitud || !longitud) {
            e.preventDefault();
            alert('Por favor, selecciona la ubicación exacta en el mapa');
            return false;
        }
        
        return true;
    });
    
    // Inicializar mapa cuando se cargue la página
    document.addEventListener('DOMContentLoaded', initMap);
    
    // Mini mapa para mostrar ubicación guardada
    document.addEventListener('DOMContentLoaded', function() {
        const lat = " . ($barberia->latitud ?? '0') . ";
        const lng = " . ($barberia->longitud ?? '0') . ";
        
        if(lat && lng && lat != 0 && lng != 0) {
            const miniMapa = L.map('miniMapa').setView([lat, lng], 16);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(miniMapa);
            
            L.marker([lat, lng]).addTo(miniMapa)
                .bindPopup('" . htmlspecialchars(addslashes($barberia->nombre)) . "')
                .openPopup();
            
            // Agregar círculo para resaltar
            L.circle([lat, lng], {
                color: '#0da6f3',
                fillColor: '#0da6f3',
                fillOpacity: 0.2,
                radius: 50
            }).addTo(miniMapa);
        }
    });
</script>
";
?>