<div class="manual-usuario" id="manual-administrador" hidden>
    <div class="manual-usuario__backdrop" aria-hidden="true"></div>
    <section class="manual-usuario__dialog" role="dialog" aria-modal="true" aria-labelledby="manual-administrador-titulo" tabindex="-1">
        <header class="manual-usuario__header">
            <div>
                <p class="manual-usuario__eyebrow">Ayuda de SIGTUR · Administración</p>
                <h2 id="manual-administrador-titulo">Manual de administrador</h2>
                <p>Guía para gestionar cuentas, solicitudes, comunicación y contenido de SIGTUR.</p>
            </div>
            <button class="manual-usuario__close" type="button" data-manual-close aria-label="Cerrar manual">×</button>
        </header>

        <nav class="manual-usuario__contents" aria-label="Secciones del manual de administrador">
            <a href="#admin-inicio">Panel</a>
            <a href="#admin-usuarios">Usuarios y roles</a>
            <a href="#admin-solicitudes">Solicitudes de rango</a>
            <a href="#admin-comunicacion">Mensajes y avisos</a>
            <a href="#admin-contenido">Contenido</a>
            <a href="#admin-seguridad">Seguridad y buenas prácticas</a>
        </nav>

        <div class="manual-usuario__body">
            <section id="admin-inicio">
                <h3>1. Entrar al panel de administración</h3>
                <p>Iniciá sesión con una cuenta autorizada y elegí “Panel de administración” en el menú de usuario. El <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/panel-admin.php">panel</a> resume las cuentas registradas, las solicitudes de rango pendientes y los mensajes sin leer.</p>
            </section>

            <section id="admin-usuarios">
                <h3>2. Gestionar usuarios y roles</h3>
                <p>En “Gestionar usuarios” consultá las cuentas y su rol actual. Para cambiarlo, desplegá el control de rol de la cuenta, seleccioná el nuevo rol y confirmá la operación en el diálogo.</p>
                <p>El sistema no permite quitar el rol al único administrador. Asigná permisos según las responsabilidades verificadas y confirmá que seleccionaste la cuenta correcta antes de guardar.</p>
                <p><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/admin-usuarios.php">Abrir gestión de usuarios</a></p>
            </section>

            <section id="admin-solicitudes">
                <h3>3. Revisar solicitudes de rango</h3>
                <p>En “Solicitud de rango” revisá el rango solicitado, el motivo y los datos presentados. Evaluá la información antes de aprobar o rechazar.</p>
                <p>Al resolver una solicitud, sus datos personales se eliminan. Esta acción no debe usarse como sustituto de la gestión de roles en la lista de usuarios.</p>
                <p><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/admin-solicitudes-rango.php">Abrir solicitudes pendientes</a></p>
            </section>

            <section id="admin-comunicacion">
                <h3>4. Mensajes y avisos a turistas</h3>
                <p>En “Mensajes de usuarios” atendé las conversaciones disponibles para administración y respondé con información clara. Revisá las confirmaciones antes de vaciar conversaciones o eliminar mensajes.</p>
                <p>Para enviar un aviso general, abrí “Redactar aviso” en el panel. Indicá título, tipo y mensaje; el límite del mensaje es de 500 caracteres. Al enviar, el aviso se dirige a las cuentas de turista.</p>
                <p><a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/mensajes-usuarios.php">Abrir mensajes de usuarios</a></p>
            </section>

            <section id="admin-contenido">
                <h3>5. Contenido, eventos y galerías</h3>
                <p>Además del panel, administración puede abrir la gestión de <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/organizar-contenido.php?seccion=turismo">Turismo</a>, <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/organizar-contenido.php?seccion=lugares">Lugares</a> y <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/organizar-contenido.php?seccion=gastronomia">Gastronomía</a>. Allí podés crear, editar o eliminar registros; en Lugares también se define su visibilidad pública. Revisá los datos y la página pública después de guardar.</p>
                <p>En las galerías públicas, las herramientas habilitadas permiten moderar fotos. Evitá publicar contenido sin autorización y verificá que la información turística esté vigente.</p>
                <p>Para las funciones comunes, usá el menú principal y las opciones de perfil y configuración disponibles en el menú de usuario.</p>
            </section>

            <section id="admin-seguridad">
                <h3>6. Seguridad y buenas prácticas</h3>
                <ul>
                    <li>Usá permisos administrativos solo para tareas necesarias y otorgá roles con prudencia.</li>
                    <li>Verificá la identidad y la solicitud antes de cambiar permisos.</li>
                    <li>No compartas información personal de usuarios en avisos públicos o mensajes.</li>
                    <li>Revisá el destinatario, el tipo y el texto antes de enviar una comunicación.</li>
                    <li>Las eliminaciones y resoluciones pueden ser permanentes; leé la confirmación antes de continuar.</li>
                </ul>
                <p>Ante un problema de acceso o una acción inesperada, consultá <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>php/soporte.php">Soporte</a>.</p>
            </section>
        </div>
    </section>
</div>
