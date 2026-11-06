<div class="manual-usuario" id="manual-organizador" hidden>
    <div class="manual-usuario__backdrop" aria-hidden="true"></div>
    <section class="manual-usuario__dialog" role="dialog" aria-modal="true" aria-labelledby="manual-organizador-titulo" tabindex="-1">
        <header class="manual-usuario__header">
            <div>
                <p class="manual-usuario__eyebrow">Ayuda de SIGTUR · Organizador</p>
                <h2 id="manual-organizador-titulo">Manual de organizador</h2>
                <p>Guía para publicar eventos, gestionar contenido y atender consultas.</p>
            </div>
            <button class="manual-usuario__close" type="button" data-manual-close aria-label="Cerrar manual">×</button>
        </header>

        <nav class="manual-usuario__contents" aria-label="Secciones del manual de organizador">
            <a href="#organizador-inicio">Panel</a>
            <a href="#organizador-eventos">Eventos</a>
            <a href="#organizador-contenido">Contenido turístico</a>
            <a href="#organizador-consultas">Consultas y mensajes</a>
            <a href="#organizador-galerias">Galerías y perfil</a>
            <a href="#organizador-buenas-practicas">Buenas prácticas</a>
        </nav>

        <div class="manual-usuario__body">
            <section id="organizador-inicio">
                <h3>1. Entrar al panel</h3>
                <p>Iniciá sesión con tu cuenta de organizador y abrí “Panel de organizador” desde el menú de usuario. También podés ir directamente al <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/panel-organizador.php">panel</a>. Desde allí accedés a la agenda, las consultas y la gestión de Turismo, Lugares y Gastronomía.</p>
            </section>

            <section id="organizador-eventos">
                <h3>2. Crear y administrar eventos</h3>
                <ol>
                    <li>En el panel, elegí “Crear evento” para abrir el formulario de eventos.</li>
                    <li>Completá la información solicitada con datos confirmados: título, descripción, fecha, ubicación, categoría, tipo de entrada y precio o contacto. Agregá una imagen si corresponde.</li>
                    <li>Para modificar un evento del listado, abrí “Editar”, actualizá los campos y guardá los cambios. Si cambiás la imagen, seleccioná una nueva; de lo contrario, se conserva la existente.</li>
                    <li>Los eventos próximos se pueden agregar a destacados o quitar de esa selección. La opción de eliminar solicita confirmación y no se puede deshacer.</li>
                </ol>
                <p>El panel presenta eventos próximos primero y luego los finalizados. Usá “Ver evento” para revisar la ficha pública y “Ver más eventos” para desplegar el resto del listado.</p>
                <p><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/panel-organizador.php#mis-eventos">Ir a la agenda del panel</a> · <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/eventos.php">Ver la agenda pública</a></p>
            </section>

            <section id="organizador-contenido">
                <h3>3. Gestionar Turismo, Lugares y Gastronomía</h3>
                <p>Desde “Gestionar secciones” del panel, abrí la sección que querés actualizar. Podés crear, editar o eliminar propuestas de Turismo y Gastronomía; en Lugares podés mantener la ficha del destino, su visibilidad, dirección, descripción, historia, atractivos, horarios, recomendaciones e imagen.</p>
                <p>Completá los campos obligatorios y revisá el contenido antes de guardar. Para imágenes, usá archivos JPG, PNG o WEBP válidos de hasta 10 MB. Los cambios se reflejan en las páginas públicas correspondientes.</p>
                <p><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/organizar-contenido.php?seccion=turismo">Turismo</a> · <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/organizar-contenido.php?seccion=lugares">Lugares</a> · <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/organizar-contenido.php?seccion=gastronomia">Gastronomía</a></p>
            </section>

            <section id="organizador-consultas">
                <h3>4. Responder consultas y mensajes</h3>
                <p>Entrá a “Consultas de los eventos” desde el panel para revisar preguntas de visitantes. Abrí el chat, leé el historial y respondé desde el formulario; la respuesta se enviará al usuario y quedará en la conversación.</p>
                <p>El panel de mensajes directos está disponible desde el botón de chat cuando está habilitado para tu cuenta. Respondé con información útil y verificable. Vaciar o eliminar un chat son acciones distintas: leé el mensaje de confirmación antes de continuar.</p>
                <p><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/consultas-eventos-organizador.php">Abrir consultas de eventos</a></p>
            </section>

            <section id="organizador-galerias">
                <h3>5. Galerías y perfil</h3>
                <p>En las galerías habilitadas de Turismo y Lugares podés agregar fotos con el control “+”. Seleccioná una o varias imágenes aceptadas y verificá que sean apropiadas para una página pública.</p>
                <p>Desde el menú de usuario también podés abrir tu perfil, tus eventos y favoritos, y la configuración de cuenta.</p>
            </section>

            <section id="organizador-buenas-practicas">
                <h3>6. Buenas prácticas</h3>
                <ul>
                    <li>Confirmá fechas, precios, lugares, horarios y permisos antes de publicar.</li>
                    <li>Usá imágenes propias o con autorización y evitá incluir datos personales de terceros.</li>
                    <li>Actualizá o quitá de destacados la información que ya no esté vigente.</li>
                    <li>Revisá el resultado en la página pública después de guardar.</li>
                </ul>
                <p>La gestión de una sección o un evento depende de los permisos de tu cuenta. Si una opción no aparece o no está disponible, comunicate con administración o con <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/soporte.php">Soporte</a>.</p>
            </section>
        </div>
    </section>
</div>
