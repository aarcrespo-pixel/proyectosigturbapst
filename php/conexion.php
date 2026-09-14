<?php

$host = '127.0.0.1';
$dbname = 'bapst';
$dbUser = 'root';
$dbPassword = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    /* PDO centraliza la conexión y activa excepciones, consultas preparadas y
       resultados asociativos para todos los controladores de la plataforma. */
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nombre_completo` varchar(100) NOT NULL,
            `cedula` varchar(20) NOT NULL,
            `email` varchar(100) NOT NULL,
            `telefono` varchar(20) DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            `avatar` varchar(255) NOT NULL DEFAULT 'default-avatar.png',
            `banner` varchar(255) NOT NULL DEFAULT 'default-banner.png',
            `nickname` varchar(50) DEFAULT NULL,
            `biografia` text DEFAULT NULL,
            `ubicacion` varchar(100) DEFAULT NULL,
            `sitio_web` varchar(255) DEFAULT NULL,
            `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_email` (`email`),
            UNIQUE KEY `uniq_cedula` (`cedula`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `comentarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `item_key` varchar(255) NOT NULL,
            `usuario_id` int(11) NOT NULL,
            `texto` varchar(140) NOT NULL,
            `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_comentarios_item` (`item_key`),
            KEY `idx_comentarios_usuario` (`usuario_id`),
            CONSTRAINT `fk_comentarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");

    $columnasComentarios = $pdo->query("SHOW COLUMNS FROM `comentarios`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('parent_id', $columnasComentarios, true)) {
        $pdo->exec("ALTER TABLE `comentarios` ADD COLUMN `parent_id` INT NULL DEFAULT NULL AFTER `usuario_id`");
        $pdo->exec("ALTER TABLE `comentarios` ADD INDEX `idx_comentarios_parent` (`parent_id`)");
    }

    /* Las preguntas generales viven separadas de preguntas_eventos porque no
       dependen de un evento concreto. Esta migración es idempotente: puede
       ejecutarse en cada request sin borrar preguntas existentes. */
    $pdo->exec("CREATE TABLE IF NOT EXISTS `preguntas_frecuentes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NULL,
        `email` VARCHAR(150) NULL,
        `pregunta` TEXT NOT NULL,
        `estado` VARCHAR(30) NOT NULL DEFAULT 'pendiente',
        `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_faq_estado` (`estado`),
        INDEX `idx_faq_usuario` (`usuario_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `eventos` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `titulo` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) UNIQUE NOT NULL,
        `descripcion` TEXT NOT NULL,
        `fecha` DATE NOT NULL,
        `ubicacion` VARCHAR(255) NOT NULL,
        `categoria` VARCHAR(100) NOT NULL,
        `imagen_portada` VARCHAR(255) DEFAULT 'default.jpg',
        `es_pasado` TINYINT(1) DEFAULT 0,
        `organizador_id` INT NOT NULL,
        `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // La introspección permite migrar instalaciones existentes sin borrar datos.
    $columnasEventos = $pdo->query("SHOW COLUMNS FROM `eventos`")->fetchAll(PDO::FETCH_COLUMN, 0);
    $migracionesEventos = [
        'descripcion' => "ALTER TABLE `eventos` ADD COLUMN `descripcion` TEXT NOT NULL DEFAULT ''",
        'tipo_entrada' => "ALTER TABLE `eventos` ADD COLUMN `tipo_entrada` VARCHAR(30) NOT NULL DEFAULT 'Gratuito'",
        'precio' => "ALTER TABLE `eventos` ADD COLUMN `precio` DECIMAL(10,2) NULL DEFAULT NULL",
        'imagen_portada' => "ALTER TABLE `eventos` ADD COLUMN `imagen_portada` VARCHAR(255) DEFAULT 'default.jpg'",
        'es_pasado' => "ALTER TABLE `eventos` ADD COLUMN `es_pasado` TINYINT(1) DEFAULT 0",
        'organizador_id' => "ALTER TABLE `eventos` ADD COLUMN `organizador_id` INT NOT NULL DEFAULT 0",
        'creado_en' => "ALTER TABLE `eventos` ADD COLUMN `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    ];
    foreach ($migracionesEventos as $nombreCampo => $sqlAlter) {
        if (!in_array($nombreCampo, $columnasEventos, true)) {
            $pdo->exec($sqlAlter);
        }
    }
    if (in_array('imagen', $columnasEventos, true) && in_array('imagen_portada', array_merge($columnasEventos, ['imagen_portada']), true)) {
        $pdo->exec("UPDATE `eventos` SET `imagen_portada` = `imagen` WHERE (`imagen_portada` IS NULL OR `imagen_portada` = 'default.jpg') AND `imagen` IS NOT NULL AND `imagen` <> ''");
    }

    $columnasUsuarios = $pdo->query("SHOW COLUMNS FROM `usuarios`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('rol', $columnasUsuarios, true)) {
        $pdo->exec("ALTER TABLE `usuarios` ADD COLUMN `rol` VARCHAR(30) NOT NULL DEFAULT 'usuario'");
    }

    $columnas = $pdo->query("SHOW COLUMNS FROM `usuarios`")->fetchAll(PDO::FETCH_COLUMN, 0);
    $camposNuevos = [
        'avatar' => "ALTER TABLE `usuarios` ADD COLUMN `avatar` varchar(255) NOT NULL DEFAULT 'default-avatar.png'",
        'foto_perfil' => "ALTER TABLE `usuarios` ADD COLUMN `foto_perfil` varchar(255) DEFAULT NULL",
        'nickname' => "ALTER TABLE `usuarios` ADD COLUMN `nickname` varchar(50) DEFAULT NULL",
        'biografia' => "ALTER TABLE `usuarios` ADD COLUMN `biografia` text DEFAULT NULL",
        'ubicacion' => "ALTER TABLE `usuarios` ADD COLUMN `ubicacion` varchar(100) DEFAULT NULL",
        'sitio_web' => "ALTER TABLE `usuarios` ADD COLUMN `sitio_web` varchar(255) DEFAULT NULL",
        'banner' => "ALTER TABLE `usuarios` ADD COLUMN `banner` varchar(255) NOT NULL DEFAULT 'default-banner.png'",
    ];

    foreach ($camposNuevos as $nombreCampo => $sqlAlter) {
        if (!in_array($nombreCampo, $columnas, true)) {
            $pdo->exec($sqlAlter);
        }
    }

    /* Estas tablas separan la identidad social de los comentarios: publicaciones
       alimenta la galería pública, seguidores guarda una relación única y
       mensajes permite una mensajería directa mínima entre usuarios. */
    $pdo->exec("CREATE TABLE IF NOT EXISTS `publicaciones` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NOT NULL,
        `imagen` VARCHAR(255) NOT NULL,
        `descripcion` VARCHAR(500) DEFAULT NULL,
        `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY `idx_publicaciones_usuario` (`usuario_id`),
        CONSTRAINT `fk_publicaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `seguidores` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `seguidor_id` INT NOT NULL,
        `seguido_id` INT NOT NULL,
        `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `uniq_seguidores_relacion` (`seguidor_id`, `seguido_id`),
        KEY `idx_seguidores_seguido` (`seguido_id`),
        CONSTRAINT `fk_seguidores_seguidor` FOREIGN KEY (`seguidor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_seguidores_seguido` FOREIGN KEY (`seguido_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $columnasSeguidores = $pdo->query("SHOW COLUMNS FROM `seguidores`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('id', $columnasSeguidores, true)) {
        $pdo->exec("ALTER TABLE `seguidores` ADD COLUMN `id` INT AUTO_INCREMENT UNIQUE FIRST");
    }
    if (!in_array('fecha', $columnasSeguidores, true)) {
        $pdo->exec("ALTER TABLE `seguidores` ADD COLUMN `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `seguido_id`");
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS `likes_publicaciones` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NOT NULL,
        `publicacion_id` INT NOT NULL,
        `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `uniq_like_publicacion` (`usuario_id`, `publicacion_id`),
        KEY `idx_likes_publicacion` (`publicacion_id`),
        CONSTRAINT `fk_likes_publicaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_likes_publicaciones_publicacion` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `mensajes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `remitente_id` INT NOT NULL,
        `destinatario_id` INT NOT NULL,
        `mensaje` VARCHAR(500) NOT NULL,
        `foto_url` VARCHAR(255) DEFAULT NULL,
        `audio_url` VARCHAR(255) DEFAULT NULL,
        `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `leido` TINYINT(1) NOT NULL DEFAULT 0,
        KEY `idx_mensajes_destinatario` (`destinatario_id`),
        CONSTRAINT `fk_mensajes_remitente` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_mensajes_destinatario` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $columnasMensajes = $pdo->query("SHOW COLUMNS FROM `mensajes`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('leido', $columnasMensajes, true)) {
        $pdo->exec("ALTER TABLE `mensajes` ADD COLUMN `leido` TINYINT(1) NOT NULL DEFAULT 0");
    }
    foreach (['foto_url' => "ALTER TABLE `mensajes` ADD COLUMN `foto_url` VARCHAR(255) DEFAULT NULL", 'audio_url' => "ALTER TABLE `mensajes` ADD COLUMN `audio_url` VARCHAR(255) DEFAULT NULL"] as $campoMensaje => $sqlMensaje) {
        if (!in_array($campoMensaje, $columnasMensajes, true)) $pdo->exec($sqlMensaje);
    }

    /* La tabla de lugares normaliza los puntos turísticos y permite que una
       misma vista detalle reciba cualquier slug sin duplicar contenido en
       turismo.php o lugares.php. Los textos se guardan en español, idioma
       base que translator.js puede transformar en el navegador. */
    $pdo->exec("CREATE TABLE IF NOT EXISTS `lugares` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `slug` VARCHAR(120) NOT NULL UNIQUE,
        `nombre` VARCHAR(160) NOT NULL,
        `categoria` VARCHAR(80) NOT NULL,
        `direccion` VARCHAR(255) NOT NULL,
        `imagen` VARCHAR(255) NOT NULL,
        `descripcion` TEXT NOT NULL,
        `historia` TEXT NOT NULL,
        `atractivos` TEXT NOT NULL,
        `horarios` VARCHAR(255) NOT NULL,
        `recomendaciones` TEXT NOT NULL,
        `latitud` DECIMAL(10,7) DEFAULT NULL,
        `longitud` DECIMAL(10,7) DEFAULT NULL,
        `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $columnasEventosActualizadas = $pdo->query("SHOW COLUMNS FROM `eventos`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('lugar_id', $columnasEventosActualizadas, true)) {
        $pdo->exec("ALTER TABLE `eventos` ADD COLUMN `lugar_id` INT NULL DEFAULT NULL AFTER `ubicacion`");
        $pdo->exec("ALTER TABLE `eventos` ADD INDEX `idx_eventos_lugar` (`lugar_id`)");
    }

    $lugares = [
        ['costanera-norte', 'Costanera Norte', 'Naturaleza', 'Costanera Norte, Salto, Uruguay', '../img/Costanera_Norte.jpeg', 'Paseo ribereño para caminar, contemplar el río Uruguay y disfrutar actividades al aire libre.', 'La Costanera Norte forma parte del vínculo histórico de Salto con el río Uruguay y funciona como espacio público de encuentro, recreación y celebración comunitaria.', 'Caminatas junto al río|Atardeceres y fotografía|Bicicleta y actividad física|Ferias y eventos al aire libre', 'Acceso libre durante todo el día', 'Llevar agua, protector solar y calzado cómodo. En horarios nocturnos se recomienda recorrer las zonas iluminadas.', -31.3770000, -57.9540000],
        ['basalto', 'BaSalto', 'Cultura', 'Centro histórico de Salto, Uruguay', '../img/BaSalto.jpg', 'Espacio cultural e histórico para conocer la identidad urbana y patrimonial de Salto.', 'BaSalto reúne referencias del centro histórico y de la memoria local, conectando arquitectura, monumentos y expresiones culturales de la ciudad.', 'Recorrer edificios históricos|Visitar espacios culturales|Conocer monumentos y plazas|Disfrutar cafés del centro', 'Consultar horarios de cada espacio cultural', 'Planificar el recorrido a pie y consultar la agenda cultural vigente.', -31.3830000, -57.9610000],
        ['parque-benito-solari', 'Parque Benito Solari', 'Naturaleza', 'Parque Benito Solari, Salto, Uruguay', '../img/solari.jfif', 'Gran parque urbano con áreas verdes para descansar, caminar y realizar actividades familiares.', 'El parque representa uno de los espacios verdes más importantes de Salto y conserva un fuerte valor social como lugar de reunión, deporte y contacto con la naturaleza.', 'Picnic y descanso|Caminatas|Juegos y actividades familiares|Fotografía de flora y paisaje', 'Acceso libre; preferentemente de día', 'Respetar los espacios verdes, retirar residuos y llevar repelente en temporada cálida.', -31.3900000, -57.9700000],
        ['plaza-artigas', 'Plaza Artigas', 'Cultura', 'Plaza Artigas, Salto, Uruguay', '../img/Plaza_artigas.webp', 'Plaza central de Salto, rodeada de actividad urbana, cultura y arquitectura tradicional.', 'La Plaza Artigas es un punto cívico y simbólico de la ciudad, vinculada a la vida pública, los actos y la identidad histórica de Salto.', 'Observar monumentos|Caminar por el centro|Visitar ferias y actividades|Conocer edificios cercanos', 'Espacio público de acceso libre', 'Ideal para combinar con un recorrido por el centro y la gastronomía local.', -31.3840000, -57.9610000],
        ['plaza-treinta-y-tres-orientales', 'Plaza Treinta y Tres Orientales', 'Cultura', 'Plaza Treinta y Tres Orientales, Salto, Uruguay', '../img/plazab.jpeg', 'Paseo urbano con espacios verdes, miradores y zonas de descanso.', 'La plaza integra la memoria cívica y la vida cotidiana de Salto, ofreciendo un espacio de encuentro que conecta patrimonio, paisaje urbano y recreación.', 'Paseo y descanso|Observar arquitectura|Fotografía urbana|Conectar con otros puntos del centro', 'Acceso libre durante todo el día', 'Visitar durante la mañana o el atardecer y respetar las áreas de descanso.', -31.3860000, -57.9620000],
        ['la-trouville', 'La Trouville', 'Gastronomía', 'La Trouville, Salto, Uruguay', '../img/Trouville.jpg', 'Propuesta gastronómica local para descubrir sabores y encuentros en la ciudad.', 'La Trouville representa la tradición de los espacios gastronómicos como lugares de reunión, conversación y construcción de experiencias locales.', 'Probar platos regionales|Compartir una comida|Conocer productos locales|Disfrutar la vida nocturna', 'Consultar horarios del establecimiento', 'Se recomienda verificar disponibilidad y horarios antes de asistir.', -31.3860000, -57.9600000],
        ['cine-sarandi', 'Cine Sarandí', 'Cultura', 'Cine Sarandí, Salto, Uruguay', '../img/Cine_Sarandi.jpg', 'Sala y punto cultural para disfrutar cine, espectáculos y propuestas de la ciudad.', 'El Cine Sarandí forma parte de la memoria cultural de Salto y de la tradición de las salas como espacios de encuentro, arte y participación comunitaria.', 'Consultar cartelera|Asistir a funciones|Participar de actividades culturales|Recorrer la zona céntrica', 'Según cartelera y función programada', 'Consultar la programación oficial y llegar con anticipación a las funciones.', -31.3850000, -57.9600000],
        ['la-fosa', 'La Fosa', 'Deporte', 'La Fosa, Salto, Uruguay', '../img/fosa.webp', 'Circuito urbano para bicicleta, skate y actividades deportivas al aire libre.', 'La Fosa se consolidó como un punto de encuentro para jóvenes y familias, integrando deporte, recreación y apropiación comunitaria del espacio público.', 'Bicicleta y skate|Calistenia|Fotografía urbana|Actividades al aire libre', 'Acceso libre durante el día', 'Usar protección, hidratarse y respetar a quienes comparten el circuito.', -31.3970000, -57.9670000],
        ['salto-shopping', 'Salto Shopping', 'Gastronomía', 'Salto Shopping, Salto, Uruguay', '../img/Shopping_Salto.jpg', 'Centro urbano con comercios, gastronomía y opciones de entretenimiento.', 'El shopping forma parte de la transformación comercial y social de la ciudad, ofreciendo un punto de encuentro contemporáneo para residentes y visitantes.', 'Compras|Gastronomía|Cine y entretenimiento|Servicios', 'Según horarios de los locales', 'Consultar horarios especiales, estacionamiento y actividades vigentes.', -31.3860000, -57.9560000],
        ['termas-del-dayman', 'Termas del Dayman', 'Naturaleza', 'Termas del Dayman, Salto, Uruguay', '../img/Termas_Dayman.webp', 'Complejo termal reconocido por sus aguas cálidas y propuestas de descanso.', 'Las Termas del Dayman son uno de los principales atractivos turísticos del departamento de Salto y forman parte de la identidad termal de la región.', 'Baños termales|Parques acuáticos|Descanso familiar|Gastronomía y servicios', 'Según el complejo visitado', 'Consultar tarifas, temperatura de piscinas y disponibilidad antes de viajar.', -31.4530000, -57.8920000],
        ['acuamania', 'Acuamania', 'Naturaleza', 'Acuamania, Salto, Uruguay', '../img/Termas_Dayman.webp', 'Parque acuático con atracciones y espacios para disfrutar en familia.', 'Acuamania integra la oferta recreativa y termal de Salto, con una propuesta orientada al ocio familiar y al turismo de temporada.', 'Toboganes|Piscinas|Áreas de descanso|Actividades familiares', 'Según temporada y calendario del parque', 'Revisar condiciones de acceso y llevar protección solar.', -31.4550000, -57.8910000],
        ['termas-de-la-arapey', 'Termas de la Arapey', 'Naturaleza', 'Termas de Arapey, Salto, Uruguay', '../img/Termas_Dayman.webp', 'Destino termal rodeado de naturaleza y espacios de descanso.', 'Arapey es uno de los complejos termales más representativos del norte uruguayo y forma parte de la historia turística del departamento.', 'Piscinas termales|Naturaleza|Alojamiento|Descanso', 'Según el complejo y la temporada', 'Planificar transporte y alojamiento con anticipación.', -30.9820000, -57.5290000],
        ['agua-clara', 'Agua Clara', 'Naturaleza', 'Agua Clara, Salto, Uruguay', '../img/Termas_Dayman.webp', 'Propuesta de piscinas y descanso para disfrutar durante las vacaciones.', 'Agua Clara se integra a la oferta de recreación y bienestar que caracteriza al corredor turístico de Salto.', 'Piscinas|Descanso|Actividades familiares|Paisaje', 'Según temporada y operador', 'Consultar apertura y servicios disponibles antes de asistir.', -31.4500000, -57.8900000],
        ['plaza-roosvelt', 'Plaza Roosvelt', 'Naturaleza', 'Plaza Roosvelt, Salto, Uruguay', '../img/plazab.jpeg', 'Plaza tranquila para descansar y disfrutar del entorno urbano.', 'Las plazas de Salto articulan vida barrial, descanso y convivencia, formando parte del paisaje cotidiano de la ciudad.', 'Paseo|Descanso|Mate y encuentro|Fotografía urbana', 'Acceso libre durante todo el día', 'Mantener limpio el espacio y preferir horarios de luz natural.', -31.3820000, -57.9580000],
        ['muelle-negro', 'Muelle Negro', 'Naturaleza', 'Muelle Negro, Salto, Uruguay', '../img/Costanera_Norte.jpeg', 'Punto ribereño para contemplar el río y disfrutar del paisaje.', 'El borde del río Uruguay conserva un valor paisajístico y afectivo central para la identidad de Salto y sus recorridos urbanos.', 'Vistas al río|Atardecer|Pesca recreativa|Fotografía', 'Acceso libre; verificar condiciones del muelle', 'Caminar con precaución y revisar el estado del río antes de acercarse.', -31.3800000, -57.9520000],
    ];

    $guardarLugar = $pdo->prepare('INSERT INTO lugares (slug, nombre, categoria, direccion, imagen, descripcion, historia, atractivos, horarios, recomendaciones, latitud, longitud) VALUES (:slug, :nombre, :categoria, :direccion, :imagen, :descripcion, :historia, :atractivos, :horarios, :recomendaciones, :latitud, :longitud) ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), categoria = VALUES(categoria), direccion = VALUES(direccion), imagen = VALUES(imagen), descripcion = VALUES(descripcion), historia = VALUES(historia), atractivos = VALUES(atractivos), horarios = VALUES(horarios), recomendaciones = VALUES(recomendaciones), latitud = VALUES(latitud), longitud = VALUES(longitud)');
    foreach ($lugares as [$slug, $nombre, $categoria, $direccion, $imagen, $descripcion, $historia, $atractivos, $horarios, $recomendaciones, $latitud, $longitud]) {
        $guardarLugar->execute(compact('slug', 'nombre', 'categoria', 'direccion', 'imagen', 'descripcion', 'historia', 'atractivos', 'horarios', 'recomendaciones', 'latitud', 'longitud'));
    }

    /* Vinculamos registros antiguos por texto para que la relación nueva sea
       útil desde la primera carga, incluso antes de editar cada evento a mano. */
    $pdo->exec("UPDATE eventos e INNER JOIN lugares l ON e.ubicacion LIKE CONCAT('%', l.nombre, '%') SET e.lugar_id = l.id WHERE e.lugar_id IS NULL");
} catch (PDOException $e) {
    http_response_code(503);
    exit('No se pudo conectar a la base de datos.');
}
