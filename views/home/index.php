<?php
// Vista para la homepage
?>

<div class="homepage">
    <!-- Header -->
    <header class="main-header">
        <div class="nav-container">
            <a href="/" class="logo">
                <i class="fas fa-cut logo-icon"></i>
                <span class="logo-text">ELITE<span>BARBER</span></span>
            </a>
            
            <nav class="nav-links">
                <a href="#inicio" class="nav-link">Inicio</a>
                <a href="#barberias" class="nav-link">Barberías</a>
                <a href="#servicios" class="nav-link">Servicios</a>
                <a href="#contacto" class="nav-link">Contacto</a>
                
                <?php if(isset($_SESSION['login']) && $_SESSION['login']): ?>
                    <span class="user-greeting">Hola, <?php echo $_SESSION['nombre'] ?? ''; ?></span>
                    <a href="/logout" class="btn btn-outline">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline">Iniciar Sesión</a>
                    <a href="/crear-cuenta" class="btn">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="container">
            <h1>ESTILO QUE DEFINE TU CARÁCTER</h1>
            <p>Encuentra la barbería perfecta para tu próximo corte. Reserva fácilmente y disfruta de un servicio de primera clase.</p>
            <a href="#barberias" class="btn">Explorar Barberías</a>
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="servicios">
        <div class="container">
            <div class="section-title">
                <h2>¿Por qué elegir Elite Barber?</h2>
                <p>Ofrecemos la mejor experiencia en cuidado masculino con profesionales certificados y un ambiente premium.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-calendar-check feature-icon"></i>
                    <h3>Reserva Online</h3>
                    <p>Reserva tu cita en segundos desde cualquier dispositivo.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-star feature-icon"></i>
                    <h3>Barberías Certificadas</h3>
                    <p>Todas nuestras barberías pasan por un riguroso proceso de selección.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3>Horarios Flexibles</h3>
                    <p>Abrimos hasta tarde para adaptarnos a tu horario.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3>Seguridad Garantizada</h3>
                    <p>Protocolos de higiene y seguridad de primer nivel.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Barberías -->
    <section class="barberias-section" id="barberias">
        <div class="container">
            <div class="section-title">
                <h2>Nuestras Barberías</h2>
                <p>Descubre las mejores barberías asociadas a nuestro sistema</p>
            </div>
            
            <div class="barberias-grid">
                <?php if(empty($barberias)): ?>
                    <div class="no-barberias">
                        <p>No hay barberías disponibles en este momento.</p>
                        <?php if(isset($_SESSION['login']) && $_SESSION['tipo'] === 'cliente'): ?>
                            <a href="/solicitud" class="btn" style="margin-top: 2rem; display: inline-block;">¿Eres barbero? Regístrate aquí</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach($barberias as $barberia): ?>
                        <div class="barberia-card">
                            <div class="barberia-img">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div class="barberia-info">
                                <h3><?php echo htmlspecialchars($barberia->nombre); ?></h3>
                                <div class="barberia-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($barberia->direccion); ?></span>
                                </div>
                                <p class="barberia-desc">
                                    <?php 
                                        $descripcion = htmlspecialchars($barberia->descripcion ?? 'Barbería profesional con los mejores servicios.');
                                        echo strlen($descripcion) > 100 ? substr($descripcion, 0, 100) . '...' : $descripcion;
                                    ?>
                                </p>
                                
                                <div class="barberia-features">
                                    <span class="feature-tag">Tel: <?php echo htmlspecialchars($barberia->telefono); ?></span>
                                    <span class="feature-tag">Email: <?php echo htmlspecialchars($barberia->email); ?></span>
                                    <span class="feature-tag">
                                        <?php echo date('g:i A', strtotime($barberia->horario_apertura)); ?> - 
                                        <?php echo date('g:i A', strtotime($barberia->horario_cierre)); ?>
                                    </span>
                                </div>
                                
                                <div class="barberia-actions">
                                    <?php if(isset($_SESSION['login']) && $_SESSION['login']): ?>
                                        <a href="/barberia?id=<?php echo $barberia->id; ?>" class="btn">
                                            Ver Servicios
                                        </a>
                                    <?php else: ?>
                                        <button onclick="mostrarModalLogin(<?php echo $barberia->id; ?>)" class="btn">
                                            Ver Servicios
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="contacto">
        <div class="container">
            <div class="cta-content">
                <h2>¿Eres barbero?</h2>
                <p>Únete a nuestra plataforma y lleva tu negocio al siguiente nivel. Gestiona tus citas, clientes y servicios desde un solo lugar.</p>
                <a href="/solicitud" class="btn">Registrar Mi Barbería</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">ELITE<span>BARBER</span></div>
                    <p>La plataforma líder para reservas de barbería. Conectamos a los mejores barberos con clientes exigentes.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Enlaces Rápidos</h4>
                    <ul>
                        <li><a href="/">Inicio</a></li>
                        <li><a href="#barberias">Barberías</a></li>
                        <li><a href="#servicios">Servicios</a></li>
                        <li><a href="/login">Iniciar Sesión</a></li>
                        <li><a href="/crear-cuenta">Registrarse</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Contacto</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> elitebarber043@gmail.com</li>
                        <li><i class="fas fa-phone"></i> +52 1 747 529 0570</li>
                        <li><i class="fas fa-map-marker-alt"></i> Chilpancingo, Guerrero</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Elite Barber. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Login -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="cerrarModal()">&times;</button>
            <h2>Iniciar Sesión</h2>
            <p>Para ver los servicios y reservar una cita, necesitas iniciar sesión.</p>
            
            <div class="alertas"></div>
            
            <form id="loginForm" method="POST" action="/login" class="formulario">
                <input type="hidden" id="redirectBarberia" name="redirect_barberia" value="">
                
                <div class="campo">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="campo">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
            
            <div class="alternate-action">
                <p>¿No tienes cuenta? <a href="/crear-cuenta">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</div>

<?php 
$script = "
<script>
    // Modal Functions
    function mostrarModalLogin(barberiaId) {
        document.getElementById('redirectBarberia').value = barberiaId;
        document.getElementById('loginModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function cerrarModal() {
        document.getElementById('loginModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        // Limpiar alertas
        const alertasDiv = document.querySelector('.alertas');
        if (alertasDiv) {
            alertasDiv.innerHTML = '';
        }
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('loginModal');
        if (event.target === modal) {
            cerrarModal();
        }
    });
    
    // Smooth scrolling
    document.querySelectorAll('a[href^=\"#\"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            if(this.getAttribute('href') !== '#') {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // Form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const alertasDiv = document.querySelector('.alertas');
            if (alertasDiv) {
                alertasDiv.innerHTML = '<div class=\"alerta\">Iniciando sesión...</div>';
            }
            
            fetch('/login', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    const barberiaId = document.getElementById('redirectBarberia').value;
                    if (barberiaId) {
                        window.location.href = `/barberia?id=\${barberiaId}`;
                    } else {
                        window.location.href = response.url;
                    }
                } else {
                    return response.text();
                }
            })
            .then(data => {
                if (data && alertasDiv) {
                    // Parse HTML to find alerts
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const errors = doc.querySelectorAll('.alerta.error');
                    
                    if (errors.length > 0) {
                        alertasDiv.innerHTML = '';
                        errors.forEach(error => {
                            alertasDiv.innerHTML += error.outerHTML;
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (alertasDiv) {
                    alertasDiv.innerHTML = '<div class=\"alerta error\">Error al iniciar sesión. Intenta nuevamente.</div>';
                }
            });
        });
    }
</script>
";
?>