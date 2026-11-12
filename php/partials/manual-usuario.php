<div class="manual-usuario" id="manual-usuario" hidden>
    <div class="manual-usuario__backdrop" aria-hidden="true"></div>
    <section class="manual-usuario__dialog" role="dialog" aria-modal="true" aria-labelledby="manual-usuario-titulo" tabindex="-1">
        <header class="manual-usuario__header">
            <div>
                <p class="manual-usuario__eyebrow">Ayuda de SIGTUR</p>
                <h2 id="manual-usuario-titulo">Manual de usuario</h2>
                <p>Guía para recorrer la plataforma, crear tu cuenta y usar sus herramientas.</p>
            </div>
            <button class="manual-usuario__close" type="button" data-manual-close aria-label="Cerrar manual">×</button>
        </header>

        <nav class="manual-usuario__contents" aria-label="Secciones del manual">
            <a href="#manual-inicio">Navegación</a>
            <a href="#manual-cuenta">Cuenta</a>
            <a href="#manual-eventos">Eventos</a>
            <a href="#manual-explorar">Lugares y turismo</a>
            <a href="#manual-mensajes">Mensajes</a>
            <a href="#manual-configuracion">Configuración</a>
            <a href="#manual-comunidad">Comunidad y ayuda</a>
        </nav>

        <div class="manual-usuario__body">
            <section id="manual-inicio">
                <h3>1. Recorrer SIGTUR</h3>
                <p>Desde el menú principal o la barra de navegación inferior podés entrar a <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/eventos.php">Eventos</a>, <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/turismo.php">Turismo</a>, <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/lugares.php">Lugares</a> y <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/gastronomia.php">Gastronomía</a>. En Inicio encontrás una presentación y selecciones destacadas. El encabezado también puede mostrar el clima de Salto y el buscador general.</p>
                <p>Usá el buscador de la parte superior para localizar eventos, lugares y propuestas. En teléfonos, el botón de menú abre la navegación; si el encabezado está contraído, tocá la flecha para mostrar sus secciones y buscador.</p>
                <p>Las tarjetas y botones “Ver más” abren fichas o amplían listas. En las fichas podés consultar descripciones, información disponible y, cuando existe, abrir la ubicación en el mapa.</p>
            </section>

            <section id="manual-cuenta">
                <h3>2. Registrarte e iniciar sesión</h3>
                <ol>
                    <li>Abrí el menú de usuario y elegí “Ingresar Usuario”. En la pantalla de acceso, seleccioná <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/registrarse.php">Registrarse</a>.</li>
                    <li>Completá nombre, cédula, correo y contraseña; el teléfono es opcional. Confirmá la contraseña y aceptá los términos y la política de privacidad.</li>
                    <li>Al terminar, entrá a <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/login.php">Iniciar sesión</a> con tu correo o usuario y contraseña.</li>
                    <li>Si no recordás la contraseña, usá “Olvidé mi contraseña” en el formulario de acceso y seguí las instrucciones.</li>
                </ol>
                <p>Podés consultar las secciones públicas sin cuenta. Algunas acciones personales, como guardar favoritos, seguir cuentas, inscribirte o participar, pueden pedir que inicies sesión.</p>
            </section>

            <section id="manual-eventos">
                <h3>3. Buscar y participar en eventos</h3>
                <p>En <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/eventos.php">Eventos</a> revisá los destacados, el listado y el calendario. Usá el buscador y los filtros de categoría, tipo de entrada, precio y fechas para acotar resultados; para consultar eventos pasados, navegá a los meses anteriores.</p>
                <p>Abrí una tarjeta o tocá “Ver más” para entrar en el detalle. Allí podés consultar fecha, lugar, descripción y datos de acceso; según el evento, también inscribirte, guardar en favoritos, hacer una pregunta o participar en comentarios. Iniciá sesión si la acción lo requiere.</p>
                <p>Las fechas y condiciones pueden cambiar: confirmá los detalles con el organizador antes de asistir. Los organizadores habilitados pueden publicar eventos desde “Crear Evento”; el envío queda sujeto a las reglas y permisos de la plataforma.</p>
            </section>

            <section id="manual-explorar">
                <h3>4. Turismo, lugares y gastronomía</h3>
                <p>En <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/turismo.php">Turismo</a> explorá escapadas, recorridos y experiencias. En <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/lugares.php">Lugares</a> consultá sitios culturales, recreativos y naturales, su ficha y mapa. En <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/gastronomia.php">Gastronomía</a> buscá establecimientos y filtrá por categoría; el enlace “Ver ubicación” abre el mapa. Revisá siempre los horarios y servicios con el establecimiento antes de ir.</p>
            </section>

            <section id="manual-mensajes">
                <h3>5. Mensajes y consultas</h3>
                <p>Para consultar sobre un evento, abrí su ficha y usá la sección de preguntas o consultas si está disponible. Las respuestas y conversaciones relacionadas quedan asociadas al evento.</p>
                <p>Los mensajes directos están disponibles cuando la plataforma habilita el panel “Mensajes” para tu rol. Abrilo desde el botón de chat, elegí una conversación y escribí en el campo del chat. En los perfiles podés ver la actividad pública y seguir al usuario cuando la función esté disponible.</p>
                <p>Si el panel de mensajes no aparece, esa cuenta o página no tiene habilitada esa herramienta; usá la consulta del evento o la sección <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/soporte.php">Soporte</a>.</p>
            </section>

            <section id="manual-configuracion">
                <h3>6. Configurar tu experiencia</h3>
                <p>Desde “Configuraciones” elegí tema claro u oscuro, idioma español, inglés o portugués y tamaño de letra. Tocá “Guardar cambios” para aplicar tus preferencias o “Restablecer” para volver a los valores predeterminados. La configuración de visitante también se puede usar sin iniciar sesión.</p>
                <p>Con una cuenta, esa página incluye controles de notificaciones: podés habilitar avisos del navegador, revisar avisos personales y marcarlos como leídos. El navegador puede pedirte permiso para enviar notificaciones.</p>
                <p>En el menú de usuario podés abrir tu perfil, tus eventos y favoritos, y otras opciones disponibles para tu rol. Los organizadores y administradores ven herramientas de gestión; las opciones privadas requieren los permisos correspondientes.</p>
            </section>

            <section id="manual-comunidad">
                <h3>7. Perfiles, publicaciones y ayuda</h3>
                <p>Desde una tarjeta de usuario podés visitar su perfil público, ver fotos compartidas y, si corresponde, seguirlo. En publicaciones y tarjetas interactivas usá los controles visibles para indicar “Me gusta” o comentar. Tratá a las demás personas con respeto y no publiques información privada.</p>
                <p>Los usuarios autorizados pueden compartir fotos desde las herramientas de publicación o galerías habilitadas. Revisá la vista previa y la información antes de enviar; la disponibilidad depende de la página y del rol.</p>
                <p>En <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/soporte.php">Soporte</a> encontrás ayuda para consultas frecuentes y contacto. También podés revisar los <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/terminos-privacidad.php#terminos">términos</a> y la <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/terminos-privacidad.php#privacidad">política de privacidad</a>.</p>
            </section>
        </div>
    </section>
</div>
