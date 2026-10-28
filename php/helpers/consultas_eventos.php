<?php

function sigtur_asegurar_mensajes_consulta_evento(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS `preguntas_eventos_mensajes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `pregunta_id` INT NOT NULL,
        `autor_id` INT NULL,
        `autor_rol` VARCHAR(20) NOT NULL,
        `mensaje` TEXT NOT NULL,
        `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `clave_migracion` VARCHAR(64) NULL,
        UNIQUE KEY `uniq_consulta_evento_migracion` (`pregunta_id`, `clave_migracion`),
        KEY `idx_consulta_evento_mensajes` (`pregunta_id`, `id`),
        CONSTRAINT `fk_consulta_evento_mensajes_pregunta`
            FOREIGN KEY (`pregunta_id`) REFERENCES `preguntas_eventos` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $columnasMensajes = $pdo->query("SHOW COLUMNS FROM `preguntas_eventos_mensajes`")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('eliminado', $columnasMensajes, true)) {
        $pdo->exec("ALTER TABLE `preguntas_eventos_mensajes` ADD COLUMN `eliminado` TINYINT(1) NOT NULL DEFAULT 0");
    }

    $pdo->exec("DELETE original
        FROM `preguntas_eventos_mensajes` original
        INNER JOIN `preguntas_eventos_mensajes` migrado
            ON migrado.`pregunta_id` = original.`pregunta_id`
            AND migrado.`autor_rol` = 'turista'
            AND migrado.`mensaje` = original.`mensaje`
            AND migrado.`clave_migracion` = 'pregunta_inicial'
            AND migrado.`eliminado` = 1
        WHERE original.`clave_migracion` IS NULL
          AND original.`autor_rol` = 'turista'");

    $pdo->exec("DELETE original
        FROM `preguntas_eventos_mensajes` original
        INNER JOIN `preguntas_eventos_mensajes` migrado
            ON migrado.`pregunta_id` = original.`pregunta_id`
            AND migrado.`autor_rol` IN ('organizador', 'administrador')
            AND migrado.`mensaje` = original.`mensaje`
            AND migrado.`clave_migracion` = 'respuesta_inicial'
            AND migrado.`eliminado` = 1
        WHERE original.`clave_migracion` IS NULL
          AND original.`autor_rol` IN ('organizador', 'administrador')");

    $pdo->exec("DELETE migrado
        FROM `preguntas_eventos_mensajes` migrado
        INNER JOIN `preguntas_eventos_mensajes` original
            ON original.`pregunta_id` = migrado.`pregunta_id`
            AND original.`id` <> migrado.`id`
            AND original.`autor_rol` = 'turista'
            AND original.`mensaje` = migrado.`mensaje`
            AND original.`clave_migracion` IS NULL
        INNER JOIN `preguntas_eventos` p ON p.`id` = migrado.`pregunta_id`
        WHERE migrado.`clave_migracion` = 'pregunta_inicial'
          AND migrado.`eliminado` = 0
          AND migrado.`mensaje` = p.`pregunta`");

    $pdo->exec("DELETE migrado
        FROM `preguntas_eventos_mensajes` migrado
        INNER JOIN `preguntas_eventos_mensajes` original
            ON original.`pregunta_id` = migrado.`pregunta_id`
            AND original.`id` <> migrado.`id`
            AND original.`autor_rol` IN ('organizador', 'administrador')
            AND original.`mensaje` = migrado.`mensaje`
            AND original.`clave_migracion` IS NULL
        INNER JOIN `preguntas_eventos` p ON p.`id` = migrado.`pregunta_id`
        WHERE migrado.`clave_migracion` = 'respuesta_inicial'
          AND migrado.`eliminado` = 0
          AND migrado.`mensaje` = p.`respuesta`");

    $pdo->exec("INSERT IGNORE INTO `preguntas_eventos_mensajes`
        (`pregunta_id`, `autor_id`, `autor_rol`, `mensaje`, `fecha_creacion`, `clave_migracion`)
        SELECT `id`, `usuario_id`, 'turista', `pregunta`, `fecha_creacion`, 'pregunta_inicial'
        FROM `preguntas_eventos` p
        WHERE NOT EXISTS (
            SELECT 1 FROM `preguntas_eventos_mensajes` m
            WHERE m.`pregunta_id` = p.`id`
              AND (m.`clave_migracion` = 'pregunta_inicial'
                   OR (m.`autor_rol` = 'turista' AND m.`mensaje` = p.`pregunta`))
        )");

    $pdo->exec("INSERT IGNORE INTO `preguntas_eventos_mensajes`
        (`pregunta_id`, `autor_id`, `autor_rol`, `mensaje`, `fecha_creacion`, `clave_migracion`)
        SELECT p.`id`, NULLIF(e.`organizador_id`, 0),
            CASE WHEN e.`organizador_id` > 0 THEN 'organizador' ELSE 'administrador' END,
            p.`respuesta`, p.`fecha_creacion`, 'respuesta_inicial'
        FROM `preguntas_eventos` p
        INNER JOIN `eventos` e ON e.`slug` = p.`evento_slug`
        WHERE p.`respuesta` IS NOT NULL AND p.`respuesta` <> ''
          AND NOT EXISTS (
              SELECT 1 FROM `preguntas_eventos_mensajes` m
              WHERE m.`pregunta_id` = p.`id`
                AND (m.`clave_migracion` = 'respuesta_inicial'
                     OR (m.`autor_rol` IN ('organizador', 'administrador') AND m.`mensaje` = p.`respuesta`))
          )");
}
