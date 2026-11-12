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
        $pdo->exec("UPDATE `lugares` SET `seccion_publica` = 'recreacion' WHERE `slug` IN ('costanera-norte', 'muelle-negro')");
    }
    $pdo->exec("UPDATE `lugares` SET `seccion_publica` = 'complementario' WHERE `seccion_publica` = 'oculto' AND `slug` IN ('basalto', 'costanera-sur', 'la-fosa', 'cine-sarandi', 'salto-shopping', 'la-trouville', 'plaza-general-flores', 'plaza-de-deportes-salto', 'museo-del-hombre-y-la-tecnologia', 'museo-horacio-quiroga', 'usina-cultural-salto', 'biblioteca-municipal-salto', 'termas-del-dayman', 'acuamania', 'termas-de-la-arapey', 'agua-clara')");
    $pdo->exec("UPDATE `lugares` SET `nombre` = 'Termas Agua Clara', `direccion` = 'Termas Agua Clara, Salto, Uruguay' WHERE `slug` = 'agua-clara' AND `nombre` = 'Agua Clara'");

    $marcadorLugaresOsm = $pdo->prepare('SELECT 1 FROM contenido_catalogo_meta WHERE clave = ?');
    $marcadorLugaresOsm->execute(['lugares_openstreetmap_referencias_v1']);
    if (!$marcadorLugaresOsm->fetchColumn()) {
        $lugaresOpenStreetMap = [
            ['acuamania', 'way', '180603817', -31.4565808, -57.9112481],
            ['costanera-norte', 'way', '836228745', -31.3563630, -57.9759917],
            ['costanera-sur', 'way', '177870127', -31.4103858, -57.9995006],
            ['la-fosa', 'way', '428549866', -31.3845820, -57.9737650],
            ['muelle-negro', 'node', '4531926891', -31.3841657, -57.9744017],
            ['parque-benito-solari', 'way', '206468371', -31.3792326, -57.9488497],
            ['plaza-treinta-y-tres-orientales', 'way', '546444654', -31.3876512, -57.9702276],
            ['salto-shopping', 'node', '1910509259', -31.3843784, -57.9442561],
            ['termas-de-la-arapey', 'relation', '3414623', -30.9474241, -57.5232084],
            ['termas-del-dayman', 'relation', '3423903', -31.4525017, -57.9083609],
        ];
        $guardarReferenciaOsm = $pdo->prepare('UPDATE lugares SET latitud = :latitud, longitud = :longitud, osm_type = :osm_type, osm_id = :osm_id WHERE slug = :slug AND osm_type IS NULL AND osm_id IS NULL');
        foreach ($lugaresOpenStreetMap as [$slugOsm, $tipoOsm, $idOsm, $latitudOsm, $longitudOsm]) {
            $guardarReferenciaOsm->execute([
                ':slug' => $slugOsm,
                ':osm_type' => $tipoOsm,
                ':osm_id' => $idOsm,
                ':latitud' => $latitudOsm,
                ':longitud' => $longitudOsm,
            ]);
        }
        $pdo->prepare('INSERT IGNORE INTO contenido_catalogo_meta (clave) VALUES (?)')->execute(['lugares_openstreetmap_referencias_v1']);
    }

    $marcadorCatalogoSalto = $pdo->prepare('SELECT 1 FROM contenido_catalogo_meta WHERE clave = ?');
    $marcadorCatalogoSalto->execute(['turismo_salto_ampliado_v1']);
    if (!$marcadorCatalogoSalto->fetchColumn()) {
        $lugaresSalto = [
            ['costanera-norte', 'Costanera Norte', 'Paseo costero', 'Costanera Norte, Salto, Uruguay', '../img/Costanera_Norte.jpeg', 'Paseo ribereño de Salto para caminar, contemplar el río Uruguay y disfrutar del espacio público.', 'La costanera forma parte del vínculo histórico de Salto con el río Uruguay y es un espacio de encuentro y recreación.', 'Paseo junto al río|Caminatas|Atardeceres|Actividades al aire libre', 'Espacio público; consultar condiciones de cada sector', 'Llevar agua y protección solar; respetar la costa y retirar los residuos.', -31.3770000, -57.9540000, 'recreacion'],
            ['costanera-sur', 'Costanera Sur', 'Paseo costero', 'Costanera Sur, Salto, Uruguay', '../img/turismo/rio-uruguay-atardecer.jpg', 'Tramo costero para recorrer y contemplar el paisaje del río Uruguay en Salto.', 'La ribera sur integra los paseos públicos vinculados al río y al paisaje urbano de Salto.', 'Vistas del río|Paseos|Fotografía de paisaje', 'Espacio público; consultar condiciones de acceso', 'Verificar accesos y condiciones del río antes de la visita.', -31.3910000, -57.9660000, 'recreacion'],
            ['muelle-negro', 'Muelle Negro', 'Muelle y costa', 'Muelle Negro, Paseo de la Costa, Salto, Uruguay', '../img/turismo/rio-uruguay-vista.jpg', 'Punto de referencia del Paseo de la Costa, junto al río Uruguay en Salto.', 'El Muelle Negro forma parte del paisaje portuario y ribereño de la ciudad.', 'Paisaje del río|Recorrido costero|Fotografía', 'Consultar accesos y condiciones del lugar', 'La foto muestra el río desde Salto, no una toma específica del muelle. Respetar las zonas de acceso restringido.', -31.3841657, -57.9744017, 'recreacion'],
            ['parque-benito-solari', 'Parque Benito Solari', 'Parque', 'Parque Benito Solari, Salto, Uruguay', '../img/turismo/parque-benito-solari.jpg', 'Parque urbano histórico con áreas verdes y espacios para recorrer al aire libre.', 'El Parque Benito Solari es uno de los espacios verdes emblemáticos de Salto.', 'Caminatas|Paisaje y patrimonio|Actividades al aire libre', 'Espacio público; consultar horarios y actividades', 'Respetar las áreas verdes y retirar los residuos.', -31.3900000, -57.9700000, 'emblematico'],
            ['plaza-artigas', 'Plaza Artigas', 'Plaza', 'Plaza Artigas, Salto, Uruguay', '../img/turismo/plaza-artigas.jpg', 'Plaza céntrica y espacio cívico de Salto, reconocible por su monumento a José Gervasio Artigas.', 'La plaza es un punto de referencia de la vida pública y del patrimonio urbano de Salto.', 'Monumento a Artigas|Paseo urbano|Arquitectura del centro', 'Espacio público de acceso libre', 'Combinar la visita con un recorrido por el centro histórico.', -31.3840000, -57.9610000, 'emblematico'],
            ['plaza-treinta-y-tres-orientales', 'Plaza Treinta y Tres Orientales', 'Plaza', 'Plaza Treinta y Tres Orientales, Salto, Uruguay', '../img/turismo/plaza-treinta-y-tres.jpg', 'Plaza céntrica de Salto con espacios verdes y patrimonio urbano.', 'La plaza integra la memoria cívica y la vida cotidiana de la ciudad.', 'Paseo|Espacios verdes|Patrimonio urbano', 'Espacio público de acceso libre', 'Visitar durante el día y respetar las áreas de descanso.', -31.3860000, -57.9620000, 'emblematico'],
            ['biblioteca-municipal-salto', 'Biblioteca Municipal de Salto', 'Biblioteca', 'Biblioteca Municipal, Salto, Uruguay', '../img/turismo/biblioteca-ilustrativa.jpg', 'Espacio público de lectura y consulta bibliográfica en Salto. Confirmar dirección y horarios con la oficina municipal de Cultura antes de visitarlo.', 'La biblioteca integra la oferta cultural y de lectura de la ciudad.', 'Lectura|Consulta bibliográfica|Actividades culturales', 'Consultar horarios de atención con la oficina municipal de Cultura', 'La fotografía es ilustrativa y corresponde a una biblioteca universitaria de Finlandia; no es la sede de Salto.', null, null, 'complementario'],
            ['museo-del-hombre-y-la-tecnologia', 'Museo del Hombre y la Tecnología', 'Museo', 'Brasil 511, Salto, Uruguay', '../img/turismo/museo-hombre-tecnologia.jpg', 'Museo municipal dedicado a la historia de Salto, la tecnología y su relación con las personas.', 'El museo ocupa un edificio patrimonial del centro y forma parte de la oferta cultural de Salto.', 'Exposiciones|Historia y tecnología|Patrimonio', 'Consultar días y horarios de apertura', 'Confirmar horarios y actividades antes de la visita.', -31.3857947, -57.9690985, 'complementario'],
            ['termas-del-dayman', 'Termas del Daymán', 'Termas', 'Termas del Daymán, Salto, Uruguay', '../img/Termas_Dayman.webp', 'Complejo termal de Salto con piscinas de aguas cálidas y opciones de descanso.', 'Las Termas del Daymán son uno de los principales destinos termales del departamento.', 'Piscinas termales|Parques acuáticos|Descanso|Servicios turísticos', 'Según el complejo y la temporada', 'Consultar tarifas, horarios y disponibilidad antes de viajar.', -31.4530000, -57.8920000, 'recreacion'],
            ['termas-de-la-arapey', 'Termas de Arapey', 'Termas', 'Termas de Arapey, Salto, Uruguay', '../img/experiencias.webp', 'Complejo termal del norte de Salto, rodeado de naturaleza y espacios de descanso.', 'Arapey integra la oferta termal del departamento y es un destino para estadías y recreación.', 'Piscinas termales|Naturaleza|Alojamiento|Descanso', 'Según el complejo y la temporada', 'Confirmar servicios, tarifas y disponibilidad antes de viajar.', null, null, 'recreacion'],
            ['acuamania', 'Acuamania', 'Parque acuático', 'Acuamania, Termas del Daymán, Salto, Uruguay', '../img/turismo/acuamania.jpg', 'Parque acuático de Termas del Daymán con hidrotubos y espacios de recreación.', 'Acuamania forma parte de la propuesta recreativa y turística de Termas del Daymán.', 'Hidrotubos|Piscinas|Actividades familiares', 'Consultar temporada y horarios del parque', 'Revisar las condiciones de acceso y las normas de seguridad del parque.', -31.4550000, -57.8910000, 'recreacion'],
        ];
        $guardarLugarSalto = $pdo->prepare('INSERT INTO lugares (slug, nombre, categoria, direccion, imagen, descripcion, historia, atractivos, horarios, recomendaciones, latitud, longitud, seccion_publica)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), categoria = VALUES(categoria), direccion = VALUES(direccion),
                imagen = VALUES(imagen), descripcion = VALUES(descripcion), historia = VALUES(historia), atractivos = VALUES(atractivos),
                horarios = VALUES(horarios), recomendaciones = VALUES(recomendaciones), latitud = VALUES(latitud),
                longitud = VALUES(longitud), seccion_publica = VALUES(seccion_publica)');
        foreach ($lugaresSalto as $lugarSalto) {
            $guardarLugarSalto->execute($lugarSalto);
        }
        $pdo->prepare('INSERT IGNORE INTO contenido_catalogo_meta (clave) VALUES (?)')->execute(['turismo_salto_ampliado_v1']);
    }

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
        ['trattoria', 'Trattoria', '../img/r2.jfif', 'Trattoria forma parte de las opciones para disfrutar una salida gastronómica en Salto. Revisá la información publicada y usá el enlace para ubicar el establecimiento. Consultá directamente con el local por su propuesta vigente y los servicios disponibles.'],
        ['toscana', 'Toscana', '../img/toscana-instagram.jpg', 'Toscana es una de las alternativas gastronómicas incluidas en la guía de SIGTUR. La ficha permite conocer la información publicada y abrir su ubicación; para detalles actuales sobre la propuesta o la atención, consultá al establecimiento.'],
        ['punto-y-aparte', 'Punto y Aparte', '../img/r4.jpg', 'Punto y Aparte suma una opción para tener en cuenta al recorrer la oferta gastronómica de Salto. Encontrá aquí los datos disponibles y accedé al mapa para planificar tu visita. La información sobre servicios y propuesta actual debe confirmarse con el local.'],
        ['rodelu', 'Rodelu', '../img/r5.jfif', 'Rodelu integra el catálogo gastronómico local de SIGTUR. Esta ficha te ayuda a identificar el establecimiento y llegar a su ubicación; contactá directamente con el local para consultar su propuesta y los servicios disponibles actualmente.'],
        ['el-tony', 'El Tony', '../img/r6.jfif', 'El Tony es una de las propuestas gastronómicas que podés explorar en Salto. Consultá la información disponible en esta ficha y abrí el mapa para ubicar el lugar. Para conocer opciones vigentes o hacer consultas específicas, comunicate con el establecimiento.'],
        ['la-canilla', 'La Canilla', '../img/r7.jfif', 'La Canilla forma parte de la oferta gastronómica que reúne SIGTUR. Desde esta ficha podés identificar el establecimiento y consultar su ubicación. Para información actualizada sobre su propuesta, servicios o disponibilidad, lo mejor es contactar directamente con el local.'],
        ['el-dominico', 'El Dominico', '../img/r8.jfif', 'El Dominico es una alternativa para considerar al organizar una salida gastronómica por Salto. La ficha reúne los datos publicados y facilita el acceso a su ubicación; consultá al establecimiento para confirmar la información vigente.'],
        ['la-2000', 'La 2000', '../img/gastronomia.jpg', 'La 2000 integra el catálogo de lugares gastronómicos de SIGTUR. Revisá la información disponible, abrí la ubicación en el mapa y contactá directamente con el establecimiento para conocer su propuesta actual y resolver consultas antes de visitarlo.'],
        ['buffet-del-chef', 'Buffet del Chef', '../img/Trouville.jpg', 'Buffet del Chef es una de las opciones gastronómicas listadas para Salto. Esta ficha te permite conocer los datos publicados y encontrar su ubicación. Para detalles sobre la oferta vigente, servicios o disponibilidad, consultá directamente con el local.'],
        ['don-diego', 'Don Diego', '../img/Shopping_Salto.jpg', 'Don Diego forma parte de las propuestas gastronómicas registradas en SIGTUR. Encontrá aquí la información disponible y el acceso al mapa para planificar cómo llegar. Los detalles de la propuesta y los servicios actuales se confirman directamente con el establecimiento.'],
        ['gaucho', 'GAUCHO', '../img/feria_emprendedores.jpg', 'GAUCHO es una propuesta incluida en la guía gastronómica de Salto. Usá esta ficha para identificar el establecimiento y consultar su ubicación; contactá directamente con el local para obtener información actualizada sobre su propuesta y servicios.'],
    ];
    $actualizarImagenToscana = $pdo->prepare("UPDATE gastronomia_catalogo SET imagen = :imagen WHERE slug = 'toscana' AND imagen IN ('../img/r3.jfif', 'r3.jfif')");
    $actualizarImagenToscana->execute([':imagen' => '../img/toscana-instagram.jpg']);
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
