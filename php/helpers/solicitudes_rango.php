<?php

function sigtur_longitud_utf8(string $texto): ?int
{
    $longitud = preg_match_all('/./us', $texto);
    return $longitud === false ? null : $longitud;
}

function sigtur_asegurar_tabla_solicitudes_rango(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS `solicitudes_rango` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NOT NULL,
        `rol_solicitado` VARCHAR(30) NOT NULL,
        `domicilio` VARCHAR(255) NOT NULL,
        `motivo` VARCHAR(500) NOT NULL,
        `estado` VARCHAR(20) NOT NULL DEFAULT 'pendiente',
        `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `revisado_por` INT NULL,
        `fecha_revision` TIMESTAMP NULL DEFAULT NULL,
        INDEX `idx_solicitudes_rango_estado_fecha` (`estado`, `fecha_creacion`),
        INDEX `idx_solicitudes_rango_usuario_fecha` (`usuario_id`, `fecha_creacion`),
        CONSTRAINT `fk_solicitudes_rango_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_solicitudes_rango_revisor` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
