<?php
function sigtur_inicializar_contenido_publico(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contenido_turistico` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `slug` VARCHAR(120) NOT NULL UNIQUE,
        `tipo` VARCHAR(20) NOT NULL,
        `nombre` VARCHAR(160) NOT NULL,
        `imagen` VARCHAR(500) NOT NULL,
        `descripcion` TEXT NOT NULL,
        `detalle` VARCHAR(255) NOT NULL DEFAULT '',
        `estrellas` TINYINT DEFAULT NULL,
        `orden` INT NOT NULL DEFAULT 0,
        KEY `idx_contenido_turistico_tipo_orden` (`tipo`, `orden`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $columnasTurismo = $pdo->query("SHOW COLUMNS FROM `contenido_turistico`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('estrellas', $columnasTurismo, true)) {
        $pdo->exec("ALTER TABLE `contenido_turistico` ADD COLUMN `estrellas` TINYINT DEFAULT NULL AFTER `detalle`");
        $pdo->exec("UPDATE `contenido_turistico` SET `estrellas` = CASE `slug` WHEN 'termas-dayman' THEN 5 WHEN 'acuamania' THEN 4 WHEN 'termas-arapey' THEN 5 WHEN 'agua-clara' THEN 4 END, `detalle` = CASE `slug` WHEN 'termas-dayman' THEN 'Termas' WHEN 'acuamania' THEN 'Parque Acuático' WHEN 'termas-arapey' THEN 'Termas' WHEN 'agua-clara' THEN 'Termas' ELSE `detalle` END WHERE `slug` IN ('termas-dayman', 'acuamania', 'termas-arapey', 'agua-clara')");
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS `gastronomia_catalogo` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `slug` VARCHAR(120) NOT NULL UNIQUE,
        `nombre` VARCHAR(160) NOT NULL,
        `imagen` VARCHAR(500) NOT NULL,
        `descripcion` TEXT NOT NULL,
        `categoria` VARCHAR(80) NOT NULL DEFAULT 'Restaurante',
        `url_mapa` VARCHAR(500) NOT NULL DEFAULT '',
        `orden` INT NOT NULL DEFAULT 0,
        KEY `idx_gastronomia_orden` (`orden`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contenido_catalogo_meta` (
        `clave` VARCHAR(80) NOT NULL PRIMARY KEY,
        `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $columnasLugares = $pdo->query("SHOW COLUMNS FROM `lugares`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('seccion_publica', $columnasLugares, true)) {
        $pdo->exec("ALTER TABLE `lugares` ADD COLUMN `seccion_publica` VARCHAR(30) NOT NULL DEFAULT 'oculto' AFTER `categoria`");
        $pdo->exec("UPDATE `lugares` SET `seccion_publica` = 'emblematico' WHERE `slug` IN ('plaza-treinta-y-tres-orientales', 'plaza-artigas', 'parque-benito-solari')");
        $pdo->exec("UPDATE `lugares` SET `seccion_publica` = 'recreacion' WHERE `slug` IN ('plaza-roosvelt', 'costanera-norte', 'muelle-negro')");
    }
    $pdo->exec("UPDATE `lugares` SET `seccion_publica` = 'complementario' WHERE `seccion_publica` = 'oculto' AND `slug` IN ('plaza-general-flores', 'plaza-de-deportes-salto', 'parque-acuatico-horacio-quiroga', 'museo-del-hombre-y-la-tecnologia', 'museo-horacio-quiroga', 'usina-cultural-salto', 'biblioteca-municipal-salto', 'termas-del-dayman', 'acuamania', 'termas-de-la-arapey', 'agua-clara')");
    $pdo->exec("UPDATE `lugares` SET `nombre` = 'Termas Agua Clara', `direccion` = 'Termas Agua Clara, Salto, Uruguay' WHERE `slug` = 'agua-clara' AND `nombre` = 'Agua Clara'");

    $marcadorTurismo = $pdo->prepare('SELECT 1 FROM contenido_catalogo_meta WHERE clave = ?');
    $marcadorTurismo->execute(['turismo_base_v1']);
    if (!$marcadorTurismo->fetchColumn()) {
        $destinos = [
            ['costanera-norte', 'escapada', 'Costanera Norte', '../img/Costanera_Norte.jpeg', 'Un paseo al borde del río para disfrutar de arte urbano, música en vivo y atardeceres junto al agua.', 'Ideal para familias · 3 km'],
            ['basalto', 'escapada', 'BaSalto', '../img/BaSalto.jpg', 'Centro histórico con monumentos, cafés y espacios culturales que invitan a recorrer la identidad local.', 'Cultura · Centro'],
            ['parque-benito-solari', 'escapada', 'Parque Benito Solari', '../img/solari.jfif', 'Un lugar verde donde se mezclan senderos, picnic y actividades deportivas al aire libre.', 'Naturaleza · Relax'],
            ['la-trouville', 'ruta', 'La Trouville', '../img/Trouville.jpg', 'Probá platos típicos y descubrí sabores locales en mercados, parrillas y cafeterías con encanto.', 'Comida'],
            ['cine-sarandi', 'ruta', 'Cine Sarandí', '../img/Cine_Sarandi.jpg', 'Itinerarios para vivir la ciudad de noche con espectáculos, bares y rincones de música en vivo.', 'Cine'],
            ['la-fosa', 'ruta', 'La Fosa', '../img/fosa.webp', 'Circuito de skate, bicicleta y barras calisténicas.', 'Deportes · Guía disponible'],
            ['salto-shopping', 'ruta', 'Salto Shopping', '../img/Shopping_Salto.jpg', 'Shopping con variedad de tiendas y zonas para comer.', 'Shopping'],
            ['termas-dayman', 'termas', 'Termas del Dayman', 'https://www.salto.gub.uy/sites/default/files/styles/16_9_lg/public/2025-07/termas-dayman.png.webp?itok=Y_zseTHZ', 'Termas naturales con aguas termales.', 'Termas', 5],
            ['acuamania', 'termas', 'Acuamania', 'https://s3.amazonaws.com/pdugallery2/25801/big_nosotros_01.webp', 'Parque acuático con diversas atracciones y áreas de descanso.', 'Parque Acuático', 4],
            ['termas-arapey', 'termas', 'Termas de la Arapey', 'https://images.trvl-media.com/lodging/2000000/1160000/1159500/1159497/e0a87b61.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill', 'Termas para relajarse en familia.', 'Termas', 5],
            ['agua-clara', 'termas', 'Termas Agua Clara', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtxRTmV6EPSUjXRqh8S7b3vhCQMugTwAgSTY-maV-T8qUuaUmApX5-SYk&s=10', 'Piscinas relajantes para disfrutar en las vacaciones.', 'Termas', 4],
        ];
        $insertar = $pdo->prepare('INSERT IGNORE INTO contenido_turistico (slug, tipo, nombre, imagen, descripcion, detalle, estrellas, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($destinos as $orden => $destino) {
            $insertar->execute([...array_pad($destino, 7, null), $orden]);
        }
        $pdo->prepare('INSERT IGNORE INTO contenido_catalogo_meta (clave) VALUES (?)')->execute(['turismo_base_v1']);
    }

    $marcadorGastronomia = $pdo->prepare('SELECT 1 FROM contenido_catalogo_meta WHERE clave = ?');
    $marcadorGastronomia->execute(['gastronomia_base_v1']);
    $restaurantes = [
        ['la-vieja-bodega', 'La Vieja Bodega', '../img/r1.jfif', 'Conocé La Vieja Bodega, una de las propuestas gastronómicas de Salto. Esta ficha reúne la información disponible del establecimiento y te permite consultar su ubicación. Para conocer su propuesta vigente, servicios o disponibilidad, contactá directamente con el local.'],
        ['trattoria', 'Trattoria', '../img/gastronomia.jpg', 'Trattoria forma parte de las opciones para disfrutar una salida gastronómica en Salto. Revisá la información publicada y usá el enlace para ubicar el establecimiento. Consultá directamente con el local por su propuesta vigente y los servicios disponibles.'],
        ['toscana', 'Toscana', '../img/toscana.jpg', 'Toscana es una de las alternativas gastronómicas incluidas en la guía de SIGTUR. La ficha permite conocer la información publicada y abrir su ubicación; para detalles actuales sobre la propuesta o la atención, consultá al establecimiento.'],
        ['punto-y-aparte', 'Punto y Aparte', '../img/r4.jpg', 'Punto y Aparte suma una opción para tener en cuenta al recorrer la oferta gastronómica de Salto. Encontrá aquí los datos disponibles y accedé al mapa para planificar tu visita. La información sobre servicios y propuesta actual debe confirmarse con el local.'],
        ['rodelu', 'Rodelu', '../img/r5.jfif', 'Rodelu integra el catálogo gastronómico local de SIGTUR. Esta ficha te ayuda a identificar el establecimiento y llegar a su ubicación; contactá directamente con el local para consultar su propuesta y los servicios disponibles actualmente.'],
        ['el-tony', 'El Tony', '../img/r6.jfif', 'El Tony es una de las propuestas gastronómicas que podés explorar en Salto. Consultá la información disponible en esta ficha y abrí el mapa para ubicar el lugar. Para conocer opciones vigentes o hacer consultas específicas, comunicate con el establecimiento.'],
        ['la-canilla', 'La Canilla', '../img/lacanilla.jpg', 'La Canilla forma parte de la oferta gastronómica que reúne SIGTUR. Desde esta ficha podés identificar el establecimiento y consultar su ubicación. Para información actualizada sobre su propuesta, servicios o disponibilidad, lo mejor es contactar directamente con el local.'],
        ['el-dominico', 'El Dominico', '../img/r8.jfif', 'El Dominico es una alternativa para considerar al organizar una salida gastronómica por Salto. La ficha reúne los datos publicados y facilita el acceso a su ubicación; consultá al establecimiento para confirmar la información vigente.'],
        ['la-2000', 'La 2000', '../img/la2000.jpg', 'La 2000 integra el catálogo de lugares gastronómicos de SIGTUR. Revisá la información disponible, abrí la ubicación en el mapa y contactá directamente con el establecimiento para conocer su propuesta actual y resolver consultas antes de visitarlo.'],
        ['buffet-del-chef', 'Buffet del Chef', '../img/buffet_del_chef.jpg', 'Buffet del Chef es una de las opciones gastronómicas listadas para Salto. Esta ficha te permite conocer los datos publicados y encontrar su ubicación. Para detalles sobre la oferta vigente, servicios o disponibilidad, consultá directamente con el local.'],
        ['don-diego', 'Don Diego', '../img/Don-diego.jpg', 'Don Diego forma parte de las propuestas gastronómicas registradas en SIGTUR. Encontrá aquí la información disponible y el acceso al mapa para planificar cómo llegar. Los detalles de la propuesta y los servicios actuales se confirman directamente con el establecimiento.'],
        ['gaucho', 'GAUCHO', '../img/gaucho.jpg', 'GAUCHO es una propuesta incluida en la guía gastronómica de Salto. Usá esta ficha para identificar el establecimiento y consultar su ubicación; contactá directamente con el local para obtener información actualizada sobre su propuesta y servicios.']
    ];
    $imagenesGastronomiaMigradas = [
        'toscana' => ['../img/toscana.jpg', ['../img/r3.jfif', 'r3.jfif', '../img/toscana-instagram.jpg']],
        'la-canilla' => ['../img/lacanilla.jpg', ['../img/r7.jfif', 'r7.jfif']],
        'la-2000' => ['../img/la2000.jpg', ['../img/gastronomia.jpg']],
        'buffet-del-chef' => ['../img/buffet_del_chef.jpg', ['../img/Trouville.jpg']],
        'don-diego' => ['../img/Don-diego.jpg', ['../img/Shopping_Salto.jpg']],
        'gaucho' => ['../img/gaucho.jpg', ['../img/feria_emprendedores.jpg']],
    ];
    $actualizarImagenGastronomia = $pdo->prepare('UPDATE gastronomia_catalogo SET imagen = :imagen_nueva WHERE slug = :slug AND imagen = :imagen_anterior');
    foreach ($imagenesGastronomiaMigradas as $slug => [$rutaNueva, $rutasAnteriores]) {
        foreach ($rutasAnteriores as $rutaAnterior) {
            $actualizarImagenGastronomia->execute([
                ':imagen_nueva' => $rutaNueva,
                ':slug' => $slug,
                ':imagen_anterior' => $rutaAnterior,
            ]);
        }
    }
    $descripcionAnterior = 'Consultá la propuesta, los horarios y los precios directamente con el restaurante.';
    $actualizarDescripcionBase = $pdo->prepare('UPDATE gastronomia_catalogo SET descripcion = :descripcion WHERE slug = :slug AND descripcion = :anterior');
    foreach ($restaurantes as [$slug, $nombre, $imagen, $descripcion]) {
        $actualizarDescripcionBase->execute([
            ':descripcion' => $descripcion,
            ':slug' => $slug,
            ':anterior' => $descripcionAnterior,
        ]);
    }
    if (!$marcadorGastronomia->fetchColumn()) {
        $insertar = $pdo->prepare('INSERT IGNORE INTO gastronomia_catalogo (slug, nombre, imagen, descripcion, categoria, url_mapa, orden) VALUES (?, ?, ?, ?, ?, ?, ?)');
        foreach ($restaurantes as $orden => [$slug, $nombre, $imagen, $descripcion]) {
            $insertar->execute([$slug, $nombre, $imagen, $descripcion, 'Restaurante', '', $orden]);
        }
        $pdo->prepare('INSERT IGNORE INTO contenido_catalogo_meta (clave) VALUES (?)')->execute(['gastronomia_base_v1']);
    }
    $insertarDonPancho = $pdo->prepare('INSERT IGNORE INTO gastronomia_catalogo (slug, nombre, imagen, descripcion, categoria, url_mapa, orden) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $insertarDonPancho->execute([
        'don-pancho-salto-nuevo',
        'Don Pancho, Salto Nuevo',
        '../img/gastronomia.jpg',
        'Restaurante ubicado en la zona de Salto Nuevo. Consultá directamente con el establecimiento para confirmar su propuesta y horarios.',
        'Restaurante',
        'https://www.google.com/maps/search/?api=1&query=Don+Pancho+Salto+Nuevo+Salto+Uruguay',
        100,
    ]);
    $restaurantesSinMapa = $pdo->query("SELECT id, nombre FROM gastronomia_catalogo WHERE url_mapa = ''")->fetchAll(PDO::FETCH_ASSOC);
    $actualizarMapaRestaurante = $pdo->prepare("UPDATE gastronomia_catalogo SET url_mapa = :url_mapa WHERE id = :id AND url_mapa = ''");
    foreach ($restaurantesSinMapa as $restauranteSinMapa) {
        $actualizarMapaRestaurante->execute([
            ':url_mapa' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode((string) $restauranteSinMapa['nombre'] . ', Salto, Uruguay'),
            ':id' => (int) $restauranteSinMapa['id'],
        ]);
    }

    $marcadorEventosOficiales = $pdo->prepare('SELECT 1 FROM contenido_catalogo_meta WHERE clave = ?');
    $marcadorEventosOficiales->execute(['eventos_oficiales_2026_v1']);
    if (!$marcadorEventosOficiales->fetchColumn()) {
        $eventosOficiales = [
            ['instituto-especial-tugua-2026', 'Instituto Especial TUGUÁ – Está de fiesta', 'Desfile de moda, música y danza con expresión corporal a cargo de Nazareno Lima. Viernes 6 de noviembre de 2026, 19:00, Ateneo de Salto (Artigas 529). Entrada: $250. Fuente: https://www.salto.gub.uy/eventos/instituto-especial-tugua-esta-de-fiesta', '2026-11-06', 'Ateneo de Salto, Artigas 529', 'Cultura', 'De Pago', '$250', '../img/danza.jpg'],
            ['fiesta-270-anos-salto-2026', 'Fiesta por los 270 años de Salto', 'Celebración con bandas locales y entrada libre. Sábado 7 y domingo 8 de noviembre de 2026, desde las 20:00, Plaza Treinta y Tres. Fuente: https://www.salto.gub.uy/eventos/fiesta-por-los-270-anos-de-salto', '2026-11-07', 'Plaza Treinta y Tres, Salto', 'Cultura', 'Gratuito', 'Entrada libre y gratuita', '../img/festival_naranja.jpg'],
            ['musica-nuestra-chalet-las-nubes-2026', 'Música Nuestra – Dúo de folclore en guitarras', 'Sábado 7 de noviembre de 2026, 20:00, Chalet Las Nubes (avenida Enrique Amorim 1700). Actúan Dúo Folclore en Guitarras, Liliana Forti en piano y Male Borba en canto. Entradas: $300 anticipada y $400 en puerta. Fuente: https://www.salto.gub.uy/eventos/musica-nuestra-duo-de-folclore-en-guitarras', '2026-11-07', 'Chalet Las Nubes, avenida Enrique Amorim 1700', 'Música', 'De Pago', '$300 anticipada; $400 en puerta', '../img/danza.jpg'],
            ['taller-candombe-danzas-2026', 'Taller de candombe y danzas', 'Domingo 8 de noviembre de 2026, de 14:00 a 16:00, Espacio Cultural Eduardo Piñeyro (Uruguay 374). Taller integral de candombe. Entrada libre y gratuita. Fuente: https://www.salto.gub.uy/eventos/taller-de-candombe-y-danzas', '2026-11-08', 'Espacio Cultural Eduardo Piñeyro, Uruguay 374', 'Cultura', 'Gratuito', 'Entrada libre y gratuita', '../img/danza.jpg'],
            ['noche-librerias-libreria-33-salto-2026', 'Noche de las Librerías 2026 en Librería 33 Salto', 'Viernes 13 de noviembre de 2026, en Librería 33 Salto (Treinta y Tres 32). Actividades literarias durante el día, presentación del libro California baby a las 19:30 y cierre musical a las 21:00. Entrada libre y gratuita. Fuente: https://www.salto.gub.uy/eventos/noche-de-las-librerias-2026-en-libreria-33-salto', '2026-11-13', 'Librería 33 Salto, Treinta y Tres 32', 'Literatura', 'Gratuito', 'Entrada libre y gratuita', '../img/cultura.jpg'],
            ['encuentro-coros-primavera-salto-2026', '2.º Encuentro de Coros de Primavera', 'La música nos une, la primavera nos inspira. Del 27 al 29 de noviembre de 2026, en la Sociedad Italiana de Salto (Artigas 651). Fuente: https://www.salto.gub.uy/eventos/2do-encuentro-de-coros-de-primavera', '2026-11-27', 'Sociedad Italiana de Salto, Artigas 651', 'Música', 'Consultar', 'Consultar', '../img/danza.jpg'],
        ];
        $insertarEventoOficial = $pdo->prepare('INSERT IGNORE INTO eventos (slug, titulo, descripcion, fecha, ubicacion, categoria, tipo_entrada, precio_texto, imagen_portada, es_pasado, organizador_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)');
        foreach ($eventosOficiales as $eventoOficial) {
            $insertarEventoOficial->execute($eventoOficial);
        }
        $pdo->prepare('INSERT IGNORE INTO contenido_catalogo_meta (clave) VALUES (?)')->execute(['eventos_oficiales_2026_v1']);
    }
}

function sigtur_texto_contenido(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
