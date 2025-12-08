<?php
include_once __DIR__ . '/../templates/barra.php';

// Asegurar que los valores no sean null antes de usar htmlspecialchars
$barberiaNombre = $barberia->nombre ?? '';
$barberiaDescripcion = $barberia->descripcion ?? '';
?>

<h1 class="nombre-pagina"><?php echo htmlspecialchars($barberiaNombre); ?></h1>
<p class="descripcion-pagina"><?php echo htmlspecialchars($barberiaDescripcion); ?></p>

<div class="barberia-detalle">
    <div class="info-contacto">
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($barberia->direccion ?? ''); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($barberia->telefono ?? ''); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($barberia->email ?? ''); ?></p>
        <p><strong>Horario:</strong> 
            <?php 
            $horarioApertura = $barberia->horario_apertura ?? '09:00:00';
            $horarioCierre = $barberia->horario_cierre ?? '19:00:00';
            echo date('g:i A', strtotime($horarioApertura)) . ' - ' . date('g:i A', strtotime($horarioCierre)); 
            ?>
        </p>
    </div>

    <h2>Servicios Disponibles</h2>
    
    <?php if(empty($servicios)): ?>
        <div class="no-servicios">
            <i class="fas fa-cut"></i>
            <h3>No hay servicios disponibles</h3>
            <p>Esta barbería aún no ha agregado servicios.</p>
        </div>
    <?php else: ?>
        <div class="servicios-grid-cliente">
            <?php foreach($servicios as $servicio): ?>
                <div class="servicio-card-cliente">
                    <?php 
                    // Obtener la ruta completa del archivo - CORREGIDO
                    $rutaImagen = __DIR__ . '/../../uploads/servicios/' . $servicio->imagen;
                    if($servicio->imagen && file_exists($rutaImagen) && filesize($rutaImagen) > 0): ?>
                    <div class="servicio-imagen-cliente">
                        <img src="/uploads/servicios/<?php echo htmlspecialchars($servicio->imagen); ?>" 
                            alt="<?php echo htmlspecialchars($servicio->nombre ?? ''); ?>"
                            onerror="this.onerror=null; this.src='/build/img/servicio-default.jpg'">
                    </div>
                    <?php else: ?>
                    <div class="servicio-imagen-cliente default">
                        <i class="fas fa-cut"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="servicio-info-cliente">
                        <h3><?php echo htmlspecialchars($servicio->nombre ?? ''); ?></h3>
                        
                        <?php if(!empty($servicio->descripcion)): ?>
                        <p class="servicio-descripcion-cliente"><?php echo htmlspecialchars($servicio->descripcion); ?></p>
                        <?php endif; ?>
                        
                        <div class="servicio-detalles-cliente">
                            <p class="precio"><i class="fas fa-tag"></i> $<?php echo number_format($servicio->precio ?? 0, 2); ?></p>
                            <p class="duracion"><i class="fas fa-clock"></i> <?php echo $servicio->duracion ?? 30; ?> min</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="acciones-centro">
        <a href="/cita?barberia_id=<?php echo $barberia->id; ?>" class="boton">
            <i class="fas fa-calendar-alt"></i> Agendar Cita
        </a>
        <a href="/barberias" class="boton btn-outline">
            <i class="fas fa-arrow-left"></i> Volver a Barberías
        </a>
    </div>
</div>

<style>
.barberia-detalle {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.info-contacto {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 3rem;
    border-left: 4px solid #d4af37;
}

.info-contacto p {
    margin-bottom: 0.8rem;
    font-size: 1.6rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-contacto p strong {
    color: #d4af37;
    min-width: 120px;
    display: inline-block;
}

.no-servicios {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    margin: 2rem 0;
    
    i {
        font-size: 5rem;
        color: rgba(212, 175, 55, 0.3);
        margin-bottom: 2rem;
    }
    
    h3 {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 1rem;
    }
    
    p {
        color: rgba(255, 255, 255, 0.6);
    }
}

/* Grid de servicios para clientes */
.servicios-grid-cliente {
    display: grid;
    gap: 2rem;
    margin: 3rem 0;
    
    @media (min-width: 768px) {
        grid-template-columns: repeat(2, 1fr);
    }
    
    @media (min-width: 1024px) {
        grid-template-columns: repeat(3, 1fr);
    }
}

.servicio-card-cliente {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid rgba(212, 175, 55, 0.1);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    
    &:hover {
        transform: translateY(-5px);
        border-color: #d4af37;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .servicio-imagen-cliente {
        height: 200px;
        overflow: hidden;
        
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        &:hover img {
            transform: scale(1.05);
        }
        
        &.default {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(26, 26, 26, 0.3));
            display: flex;
            align-items: center;
            justify-content: center;
            
            i {
                font-size: 4rem;
                color: rgba(212, 175, 55, 0.5);
            }
        }
    }
    
    .servicio-info-cliente {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        
        h3 {
            font-size: 1.8rem;
            color: #d4af37;
            margin-bottom: 0.8rem;
            text-align: left;
        }
        
        .servicio-descripcion-cliente {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.4rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            flex: 1;
        }
        
        .servicio-detalles-cliente {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            
            p {
                margin: 0;
                font-size: 1.4rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                
                i {
                    color: #d4af37;
                }
                
                &.precio {
                    color: #4caf50;
                    font-weight: bold;
                    font-size: 1.6rem;
                }
                
                &.duracion {
                    color: rgba(255, 255, 255, 0.6);
                }
            }
        }
    }
}

.acciones-centro {
    text-align: center;
    margin-top: 3rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    
    .boton {
        padding: 1rem 2rem;
        font-size: 1.6rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        
        i {
            font-size: 1.4rem;
        }
    }
    
    .btn-outline {
        background: transparent;
        border: 2px solid #d4af37;
        color: #d4af37;
        
        &:hover {
            background: #d4af37;
            color: #1a1a1a;
        }
    }
}
</style>