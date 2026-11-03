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
            ['agua-clara', 'termas', 'Agua Clara', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtxRTmV6EPSUjXRqh8S7b3vhCQMugTwAgSTY-maV-T8qUuaUmApX5-SYk&s=10', 'Piscinas relajantes para disfrutar en las vacaciones.', 'Termas', 4],
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
}

function sigtur_texto_contenido(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
