<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración básica de página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo | SIGTUR</title>
    <link rel="icon" href="/img/logoblanco.png"> <!-- Ícono de pestaña -->
    <!-- Importar fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="../css/turismo.css"> <!-- Estilos específicos de turismo -->
</head>

<body>
    <!-- Menú principal -->
    <header class="menu-principal">
        <button class="hamburguesa" aria-label="Abrir menú">☰</button>
        <nav class="menu">
            <a href="index.php" class="logo-link">
                <img src="../img/logoblanco.png" class="logo-menu" alt="SIGTUR">
            </a>
            <a href="eventos.php">Eventos</a>
            <a href="turismo.php" class="pagina-activa">Turismo</a> <!-- Página actual -->
            <a href="lugares.php">Lugares</a>
        </nav>

        <!-- Barra de búsqueda -->
        <div class="barra-busqueda">
            <input type="text" placeholder="Buscar viajes y rutas">
            <img src="../img/lupa.png" alt="Buscar">
        </div>

        <!-- Menú de perfil usuario -->
        <div class="perfil">
            <button class="perfil-btn" aria-label="Abrir perfil">
                <img src="../img/user.png" alt="Perfil">
            </button>
            <div class="perfil-menu">
                <a href="login.php">Ingresar Usuario</a>
                <a href="configuracion.php">Configuración</a>
                <a href="soporte.php">Soporte</a>
                <a href="cerrar-sesion.php">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="index.php" class="bottom-nav-item">
            <img src="../img/logoazul.png" class="bottom-nav-icon" alt="Inicio">
            <span class="bottom-nav-label">Inicio</span>
        </a>
        <a href="eventos.php" class="bottom-nav-item active">
            <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos">
            <span class="bottom-nav-label">Eventos</span>
        </a>
        <a href="turismo.php" class="bottom-nav-item">
            <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo">
            <span class="bottom-nav-label">Turismo</span>
        </a>
        <a href="lugares.php" class="bottom-nav-item">
            <img src="../img/lugares.png" class="bottom-nav-icon" alt="Lugares">
            <span class="bottom-nav-label">Lugares</span>
        </a>
    </nav>

    <main class="pagina-turismo">
        <!-- Sección hero con texto introductorio -->
        <section class="hero-turismo">
            <div class="hero-texto">
                <span>Descubrí el turismo en Salto</span>
                <h1>TURISMO</h1>
                <p>Explorá propuestas de turismo local, recorridos gastronómicos y experiencias al aire libre diseñadas
                    para cada tipo de visitante.</p>
            </div>
        </section>

        <!-- Escapadas recomendadas -->
        <section class="seccion-turismo">
            <h2>Escapadas recomendadas</h2>
            <div class="destinos-grid">
                <article class="tarjeta-destino">
                    <img src="../img/Costanera_Norte.jpeg" alt="Costanera de Salto">
                    <div class="contenido">
                        <h3>Costanera Norte</h3>
                        <p>Un paseo al borde del río para disfrutar de arte urbano, música en vivo y atardeceres junto
                            al agua.</p>
                        <div class="meta">Ideal para familias · 3 km</div>
                    </div>
                </article>
                <article class="tarjeta-destino">
                    <img src="../img/BaSalto.jpg" alt="Basalto">
                    <div class="contenido">
                        <h3>BaSalto</h3>
                        <p>Centro histórico con monumentos, cafés y espacios culturales que invitan a recorrer la
                            identidad local.</p>
                        <div class="meta">Cultura · Centro</div>
                    </div>
                </article>
                <article class="tarjeta-destino">
                    <img src="../img/solari.jfif" alt="Parque Solari">
                    <div class="contenido">
                        <h3>Parque Benito Solari</h3>
                        <p>Un lugar verde donde se mezclan senderos, picnic y actividades deportivas al aire libre.</p>
                        <div class="meta">Naturaleza · Relax</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo">
            <h2>Rutas y experiencias</h2>
            <div class="rutas-grid">
                <article class="tarjeta-ruta">
                    <img src="../img/Trouville.jpg" alt="Trouville">
                    <div class="contenido">
                        <h3>La Trouville</h3>
                        <p>Probá platos típicos y descubrí sabores locales en mercados, parrillas y cafeterías con
                            encanto.</p>
                        <div class="meta">Comida</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="../img/Cine_Sarandi.jpg" alt="Cine Sarandi">
                    <div class="contenido">
                        <h3>Cine Sarandi</h3>
                        <p>Itinerarios para vivir la ciudad de noche con espectáculos, bares y rincones de música en
                            vivo.</p>
                        <div class="meta">Cine</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="../img/fosa.webp" alt="La Fosa">
                    <div class="contenido">
                        <h3>La Fosa</h3>
                        <p>Circuito de skate, bicicleta y barras calistenicas!</p>
                        <div class="meta">Deportes · Guía disponible</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="../img/Shopping_Salto.jpg" alt="Shopping Salto">
                    <div class="contenido">
                        <h3>Salto Shopping</h3>
                        <p>Shopping con variedad de tiendas y zonas para comer!</p>
                        <div class="meta">Shopping</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo">
            <a href="#" class="boton-turismo">Descubrir más</a>
        </section>

        <section class="seccion-turismo">
            <h2>Hoteles</h2>
            <div class="galeria-grid">
                <article class="tarjeta-galeria">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOxxqJdx2__yz4HJSzIdUN71HMmZIDegVg7kkLQz4n5G1Ve6HxjOnV66w&s=10"
                        alt="Salto Hotel & Casino">
                    <div class="contenido">
                        <h3>Salto Hotel & Casino</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas">★★★★☆</div>
                            <div class="precio">$230 por noche</div>
                            <div class="ubicacion">Dirección: 25 de Agosto 05</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/03/75/fd/47/hotel-eldorado.jpg?w=900&h=500&s=1"
                        alt="Hotel Eldorado">
                    <div class="contenido">
                        <h3>Hotel Eldorado</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas">★★★★★</div>
                            <div class="precio">$310 por noche</div>
                            <div class="ubicacion">Dirección: Av. Sarandi 20,</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/max1024x768/28964462.jpg?k=ccae2ff8a5211286850c056eabe84fe4eec302509347514ebd59aaf81e31771a&o="
                        alt="Hotel Español">
                    <div class="contenido">
                        <h3>Hotel Español</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas">★★★★☆</div>
                            <div class="precio">$185 por noche</div>
                            <div class="ubicacion">Dirección: Barrio Hipódromo</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://d3l2053yktv8l0.cloudfront.net/images/DSC_3893.jpg" alt="Hotel Quiroga">
                    <div class="contenido">
                        <h3>Hotel Horacio Quiroga</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas">★★★★★</div>
                            <div class="precio">$270 por noche</div>
                            <div class="ubicacion">Dirección: Brasil 826</div>
                        </div>
                    </div>
                </article>
                <a href="#" class="boton-turismo">Ver todos los hoteles</a>
            </div>
        </section>

        <section class="seccion-turismo">
            <h2>Parques Acuáticos y Termas</h2>
            <div class="rutas-grid">
                <article class="tarjeta-ruta">
                    <img src="https://www.salto.gub.uy/sites/default/files/styles/16_9_lg/public/2025-07/termas-dayman.png.webp?itok=Y_zseTHZ"
                        alt="Dayman">
                    <div class="contenido">
                        <h3>Termas del Dayman</h3>
                        <p>Termas naturales con aguas termales.</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas">★★★★★</div>
                        </div>
                        <div class="meta">Termas</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="https://s3.amazonaws.com/pdugallery2/25801/big_nosotros_01.webp" alt="Aquamania">
                    <div class="contenido">
                        <h3>Acuamania</h3>
                        <p>Parque acuático con diversas atracciones y áreas de descanso.</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas">★★★★☆</div>
                        </div>
                        <div class="meta">Parque Acuático</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="https://images.trvl-media.com/lodging/2000000/1160000/1159500/1159497/e0a87b61.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill"
                        alt="La Fosa">
                    <div class="contenido">
                        <h3>Termas de la Arapey</h3>
                        <p>Termas para relajarse en familia</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas">★★★★★</div>
                        </div>
                        <div class="meta">Termas</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtxRTmV6EPSUjXRqh8S7b3vhCQMugTwAgSTY-maV-T8qUuaUmApX5-SYk&s=10"
                        alt="Aguas Claras">
                    <div class="contenido">
                        <h3>Agua Clara</h3>
                        <p>Piscinas relajantes para disfrutar en las vacaciones</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas">★★★★☆</div>
                        </div>
                        <div class="meta">Piscina</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo">
            <a href="#" class="boton-turismo">Ver todas las termas y parques acuaticos</a>
        </section>

        <section class="seccion-turismo">
            <h2>Gastronomia</h2>
            <div class="galeria-grid">
                <article class="tarjeta-galeria">
                    <img src="https://media-cdn.tripadvisor.com/media/photo-s/04/13/0f/8f/trouville.jpg"
                        alt="La Trouville">
                    <div class="contenido">
                        <h3>La Trouville</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas">★★★★☆</div>
                            <div class="ubicacion">Dirección: Uruguay 702</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/3c/04/55/dominico-salti.jpg?w=1200&h=-1&s=1"
                        alt="Dominico">
                    <div class="contenido">
                        <h3>Dominico</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas">★★★★★</div>
                            <div class="ubicacion">Dirección: Av. Blandengues 320</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTN9naO3vR30QRuFwNtzd7BYe--0vGV4ETKMZpq01-ARRQGb-nsOgRO_WOp&s=10"
                        alt="La2000">
                    <div class="contenido">
                        <h3>La 2000</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas">★★★★☆</div>
                            <div class="ubicacion">Dirección: Av. Blandengues 195</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/6c/1d/1b/puerta-de-ingreso-al.jpg?w=1200&h=1200&s=1"
                        alt="Trattoria">
                    <div class="contenido">
                        <h3>La Trattoria</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas">★★★★★</div>
                                <div class="ubicacion">Dirección: Uruguay 234</div>
                        </div>
                    </div>
                </article>
                <a href="#" class="boton-turismo">Ver toda la gastronomia salteña</a>
            </div>
        </section>

        <section class="seccion-turismo">
            <h2>Galería turística</h2>
            <div class="galeria-grid">
                <article class="tarjeta-galeria"><img src="../img/solari.jfif" alt="Parque Solari">
                    <div class="contenido">
                        <h3>Parque Benito Solari</h3>
                    </div>
                </article>
                <article class="tarjeta-galeria"><img src="../img/fosa.webp" alt="La Fosa">
                    <div class="contenido">
                        <h3>La Fosa</h3>
                    </div>
                </article>
                <article class="tarjeta-galeria"><img src="../img/Shopping_Salto.jpg" alt="Shopping Salto">
                    <div class="contenido">
                        <h3>Shopping Salto</h3>
                    </div>
                </article>
                <article class="tarjeta-galeria"><img src="../img/Cine_Sarandi.jpg" alt="Cine Sarandi">
                    <div class="contenido">
                        <h3>Cine Sarandi</h3>
                    </div>
                </article>
                <a href="#" class="boton-turismo">Ver todas las galerias</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>EVENTOS</h4>    
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/eventos.php" class="footer-link">Eventos destacados</a>
                    <a href="../php/eventos.php" class="footer-link">Próximos eventos</a>
                    <a href="../php/eventos.php" class="footer-link">Eventos anteriores</a>
                    <a href="../php/eventos.php" class="footer-link">Todos los eventos</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/lugares.php" class="footer-link">Plaza Artigas</a>
                    <a href="../php/lugares.php" class="footer-link">Plaza Treinta y Tres Orientales</a>
                    <a href="../php/lugares.php" class="footer-link">Costanera Norte</a>
                    <a href="../php/lugares.php" class="footer-link">Costanera Sur</a>
                    <a href="../php/lugares.php" class="footer-link">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>TURISMO</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/turismo.php" class="footer-link">Hoteles</a>
                    <a href="../php/turismo.php" class="footer-link">Actividades</a>
                    <a href="../php/turismo.php" class="footer-link">Lugares turísticos</a>
                    <a href="../php/turismo.php" class="footer-link">Experiencias</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>SIGTUR</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="#" class="footer-link">Sobre nosotros</a>
                    <a href="#" class="footer-link">Contacto</a>
                    <a href="https://instagram.com/bapstuy" target="_blank" class="footer-link footer-link--icon"><span class="social-icon">IG</span>Instagram</a>
                    <a href="https://facebook.com/sigtur" target="_blank" class="footer-link footer-link--icon"><span class="social-icon">FB</span>Facebook</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <a href="https://bapst.netlify.app">SIGTUR</a> © 2026 by <a href="https://instagram.com/bapstuy">Bapst</a>
            is licensed under <a href="https://creativecommons.org/licenses/by-nc-nd/4.0/">CC BY-NC-ND 4.0</a><img
                src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/nc.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/nd.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;">
        </div>
    </footer>
    <script src="../js/script.js" defer></script>
</body> 

</html>