<?php

function sigtur_eventos_visibles_en_mapa(PDO $pdo): array
{
    $consulta = $pdo->query("SELECT titulo, ubicacion
        FROM eventos
        WHERE (es_pasado = 0 AND fecha >= CURDATE())
            OR (fecha < CURDATE() AND fecha >= DATE_SUB(CURDATE(), INTERVAL 14 DAY))
        ORDER BY fecha ASC, id DESC");

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}
