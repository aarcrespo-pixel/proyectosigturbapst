<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
header('Vary: Cookie');

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';

$usuario = sigtur_usuario_actual($pdo);
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['error' => 'Iniciá sesión para ver las estadísticas.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!in_array($usuario['rol'], ['administrador', 'organizador'], true)) {
    http_response_code(403);
    echo json_encode(['error' => 'Tu cuenta no tiene permisos para ver las estadísticas.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$periodo = is_string($_GET['periodo'] ?? null) ? $_GET['periodo'] : '30';
if (!in_array($periodo, ['7', '30', '6m', 'todo'], true)) {
    http_response_code(422);
    echo json_encode(['error' => 'El período solicitado no es válido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$esAdministrador = $usuario['rol'] === 'administrador';
$usuarioId = (int) $usuario['id'];
$filtroEventos = $esAdministrador ? '1 = 1' : 'e.organizador_id = :organizador_id';
$parametrosEventos = $esAdministrador ? [] : [':organizador_id' => $usuarioId];
$hoyTexto = (string) $pdo->query('SELECT CURDATE()')->fetchColumn();
$hoy = DateTimeImmutable::createFromFormat('!Y-m-d', $hoyTexto);
if (!$hoy) {
    throw new RuntimeException('No se pudo determinar la fecha del servidor.');
}
$finExclusivo = $hoy->modify('+1 day');

if ($periodo === 'todo') {
    $parametrosMinimos = [];
    $filtroEventosMinimo = $esAdministrador ? '1 = 1' : 'e.organizador_id = :organizador_eventos';
    $filtroVisitasMinimo = $esAdministrador ? '1 = 1' : 'e.organizador_id = :organizador_visitas';
    if (!$esAdministrador) {
        $parametrosMinimos[':organizador_eventos'] = $usuarioId;
        $parametrosMinimos[':organizador_visitas'] = $usuarioId;
    }
    $minimaActividad = $pdo->prepare("SELECT MIN(fecha_actividad) FROM (
        SELECT DATE(e.creado_en) AS fecha_actividad
        FROM eventos e
        WHERE {$filtroEventosMinimo}
        UNION ALL
        SELECT DATE(v.ocurrido_en) AS fecha_actividad
        FROM evento_visitas v
        INNER JOIN eventos e ON e.id = v.evento_id
        WHERE {$filtroVisitasMinimo}
    ) actividad");
    $minimaActividad->execute($parametrosMinimos);
    $primeraFecha = $minimaActividad->fetchColumn();
    $inicio = $primeraFecha
        ? DateTimeImmutable::createFromFormat('!Y-m-d', (string) $primeraFecha)
        : $hoy;
} elseif ($periodo === '6m') {
    $inicio = $hoy->modify('-6 months');
} else {
    $diasPeriodo = (int) $periodo;
    $inicio = $hoy->modify('-' . ($diasPeriodo - 1) . ' days');
}
if (!$inicio) {
    throw new RuntimeException('No se pudo determinar el inicio del período.');
}

$esDiario = in_array($periodo, ['7', '30'], true);
$duracionPeriodo = (int) $inicio->diff($finExclusivo)->format('%a');
$periodoAnterior = $periodo === 'todo' ? null : [
    'desde' => $inicio->modify('-' . $duracionPeriodo . ' days'),
    'hasta' => $inicio,
];
$rangos = [
    'actual' => ['desde' => $inicio, 'hasta' => $finExclusivo],
];
if ($periodoAnterior) {
    $rangos['anterior'] = $periodoAnterior;
}

$consultarConteo = static function (string $sql, array $parametros) use ($pdo): int {
    $consulta = $pdo->prepare($sql);
    $consulta->execute($parametros);
    return (int) $consulta->fetchColumn();
};

$consultarPeriodo = static function (string $metrica, array $rango) use (
    $pdo,
    $esAdministrador,
    $usuarioId
): int {
    $desde = $rango['desde']->format('Y-m-d H:i:s');
    $hasta = $rango['hasta']->format('Y-m-d H:i:s');
    $filtro = $esAdministrador ? '1 = 1' : 'e.organizador_id = :organizador_id';
    $parametros = [':desde' => $desde, ':hasta' => $hasta];
    if (!$esAdministrador) {
        $parametros[':organizador_id'] = $usuarioId;
    }

    if ($metrica === 'eventos') {
        $sql = "SELECT COUNT(*) FROM eventos e
            WHERE {$filtro} AND e.creado_en >= :desde AND e.creado_en < :hasta";
    } elseif ($metrica === 'visitas') {
        $sql = "SELECT COUNT(*) FROM evento_visitas v
            INNER JOIN eventos e ON e.id = v.evento_id
            WHERE {$filtro} AND v.ocurrido_en >= :desde AND v.ocurrido_en < :hasta";
    } else {
        $sql = "SELECT COUNT(DISTINCT f.usuario_id) FROM favoritos_eventos f
            INNER JOIN eventos e ON e.slug = f.evento_slug
            WHERE {$filtro} AND f.fecha_registro >= :desde AND f.fecha_registro < :hasta";
    }
    $consulta = $pdo->prepare($sql);
    $consulta->execute($parametros);
    return (int) $consulta->fetchColumn();
};

$totalEventos = $consultarConteo(
    "SELECT COUNT(*) FROM eventos e WHERE {$filtroEventos}",
    $parametrosEventos
);
$eventosActivos = $consultarConteo(
    "SELECT COUNT(*) FROM eventos e
        WHERE {$filtroEventos} AND e.es_pasado = 0 AND e.fecha >= CURDATE()",
    $parametrosEventos
);
$visitasActuales = $consultarPeriodo('visitas', $rangos['actual']);
$eventosCreadosActuales = $consultarPeriodo('eventos', $rangos['actual']);
$usuariosGuardaronActual = $consultarPeriodo('favoritos', $rangos['actual']);
$visitasAnteriores = $periodoAnterior ? $consultarPeriodo('visitas', $periodoAnterior) : null;
$eventosCreadosAnteriores = $periodoAnterior ? $consultarPeriodo('eventos', $periodoAnterior) : null;
$usuariosGuardaronAnterior = $periodoAnterior ? $consultarPeriodo('favoritos', $periodoAnterior) : null;

$rangoActualParametros = [
    ':desde' => $rangos['actual']['desde']->format('Y-m-d H:i:s'),
    ':hasta' => $rangos['actual']['hasta']->format('Y-m-d H:i:s'),
];
$parametrosConversion = [
    ':visita_desde' => $rangoActualParametros[':desde'],
    ':visita_hasta' => $rangoActualParametros[':hasta'],
    ':favorito_desde' => $rangoActualParametros[':desde'],
    ':favorito_hasta' => $rangoActualParametros[':hasta'],
];
if (!$esAdministrador) {
    $parametrosConversion[':organizador_id'] = $usuarioId;
}
$filtroConversion = $esAdministrador ? '1 = 1' : 'e.organizador_id = :organizador_id';
$conversionQuery = $pdo->prepare("SELECT
        COUNT(DISTINCT v.usuario_id) AS visitantes_identificables,
        COUNT(DISTINCT CASE WHEN f.id IS NOT NULL THEN v.usuario_id END) AS usuarios_convertidos
    FROM evento_visitas v
    INNER JOIN eventos e ON e.id = v.evento_id
    LEFT JOIN favoritos_eventos f
        ON f.usuario_id = v.usuario_id
        AND f.evento_slug = e.slug
        AND f.fecha_registro >= v.ocurrido_en
        AND f.fecha_registro >= :favorito_desde
        AND f.fecha_registro < :favorito_hasta
    WHERE {$filtroConversion}
        AND v.usuario_id IS NOT NULL
        AND v.ocurrido_en >= :visita_desde
        AND v.ocurrido_en < :visita_hasta");
$conversionQuery->execute($parametrosConversion);
$conversionData = $conversionQuery->fetch();
$visitantesIdentificables = (int) ($conversionData['visitantes_identificables'] ?? 0);
$usuariosConvertidos = (int) ($conversionData['usuarios_convertidos'] ?? 0);
$conversion = $visitantesIdentificables > 0
    ? round(($usuariosConvertidos / $visitantesIdentificables) * 100, 1)
    : null;

$totalFavoritosQuery = $pdo->prepare("SELECT COUNT(*) FROM favoritos_eventos f
    INNER JOIN eventos e ON e.slug = f.evento_slug
    WHERE {$filtroConversion}
        AND f.fecha_registro >= :favorito_desde AND f.fecha_registro < :favorito_hasta");
$parametrosGuardados = [
    ':favorito_desde' => $rangoActualParametros[':desde'],
    ':favorito_hasta' => $rangoActualParametros[':hasta'],
];
if (!$esAdministrador) {
    $parametrosGuardados[':organizador_id'] = $usuarioId;
}
$totalFavoritosQuery->execute($parametrosGuardados);
$totalFavoritosActual = (int) $totalFavoritosQuery->fetchColumn();

$registrados = null;
$registradosActuales = null;
$registradosAnteriores = null;
if ($esAdministrador) {
    $registrados = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $queryRegistrados = $pdo->prepare('SELECT COUNT(*) FROM usuarios
        WHERE fecha_registro >= :desde AND fecha_registro < :hasta');
    $queryRegistrados->execute($rangoActualParametros);
    $registradosActuales = (int) $queryRegistrados->fetchColumn();
    if ($periodoAnterior) {
        $queryRegistrados->execute([
            ':desde' => $periodoAnterior['desde']->format('Y-m-d H:i:s'),
            ':hasta' => $periodoAnterior['hasta']->format('Y-m-d H:i:s'),
        ]);
        $registradosAnteriores = (int) $queryRegistrados->fetchColumn();
    }
}

$visitasParaGrafica = $pdo->prepare("SELECT "
    . ($esDiario ? 'DATE(v.ocurrido_en)' : "DATE_FORMAT(v.ocurrido_en, '%Y-%m-01')")
    . " AS periodo, COUNT(*) AS total
    FROM evento_visitas v
    INNER JOIN eventos e ON e.id = v.evento_id
    WHERE {$filtroConversion}
        AND v.ocurrido_en >= :desde AND v.ocurrido_en < :hasta
    GROUP BY periodo ORDER BY periodo");
$visitasParaGrafica->execute($rangoActualParametros + ($esAdministrador ? [] : [':organizador_id' => $usuarioId]));
$visitasPorPeriodo = [];
foreach ($visitasParaGrafica->fetchAll() as $fila) {
    $visitasPorPeriodo[(string) $fila['periodo']] = (int) $fila['total'];
}

$eventosParaGrafica = $pdo->prepare("SELECT DATE_FORMAT(e.creado_en, '%Y-%m-01') AS periodo, COUNT(*) AS total
    FROM eventos e
    WHERE {$filtroEventos} AND e.creado_en >= :desde AND e.creado_en < :hasta
    GROUP BY periodo ORDER BY periodo");
$eventosParaGrafica->execute([
    ':desde' => $inicio->format('Y-m-d H:i:s'),
    ':hasta' => $finExclusivo->format('Y-m-d H:i:s'),
] + $parametrosEventos);
$eventosPorMes = [];
foreach ($eventosParaGrafica->fetchAll() as $fila) {
    $eventosPorMes[(string) $fila['periodo']] = (int) $fila['total'];
}

$clavesTiempo = [];
$cursor = $esDiario ? $inicio : $inicio->modify('first day of this month');
$ultimoPeriodo = $esDiario ? $hoy : $hoy->modify('first day of this month');
while ($cursor <= $ultimoPeriodo) {
    $clavesTiempo[] = $cursor->format('Y-m-d');
    $cursor = $esDiario ? $cursor->modify('+1 day') : $cursor->modify('first day of next month');
}
$nombresMeses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$etiquetasTiempo = [];
$visitasTiempo = [];
$eventosCreadosTiempo = [];
foreach ($clavesTiempo as $clave) {
    $indiceMes = (int) substr($clave, 5, 2) - 1;
    $etiquetasTiempo[] = $esDiario
        ? (int) substr($clave, 8, 2) . ' ' . $nombresMeses[$indiceMes]
        : $nombresMeses[$indiceMes] . ' ' . substr($clave, 0, 4);
    $claveConsulta = $esDiario ? $clave : substr($clave, 0, 7) . '-01';
    $visitasTiempo[] = $visitasPorPeriodo[$claveConsulta] ?? 0;
    $eventosCreadosTiempo[] = $eventosPorMes[$claveConsulta] ?? 0;
}

$eventosPopulares = $pdo->prepare("SELECT e.id, e.titulo,
        COALESCE(v.visitas, 0) AS visitas,
        COALESCE(f.guardados, 0) AS guardados
    FROM eventos e
    LEFT JOIN (
        SELECT v.evento_id, COUNT(*) AS visitas
        FROM evento_visitas v
        INNER JOIN eventos ev ON ev.id = v.evento_id
        WHERE " . ($esAdministrador ? '1 = 1' : 'ev.organizador_id = :organizador_visitas') . "
            AND v.ocurrido_en >= :visitas_desde AND v.ocurrido_en < :visitas_hasta
        GROUP BY v.evento_id
    ) v ON v.evento_id = e.id
    LEFT JOIN (
        SELECT ev.id AS evento_id, COUNT(*) AS guardados
        FROM favoritos_eventos f
        INNER JOIN eventos ev ON ev.slug = f.evento_slug
        WHERE " . ($esAdministrador ? '1 = 1' : 'ev.organizador_id = :organizador_favoritos') . "
            AND f.fecha_registro >= :favoritos_desde AND f.fecha_registro < :favoritos_hasta
        GROUP BY ev.id
    ) f ON f.evento_id = e.id
    WHERE {$filtroEventos}
    ORDER BY COALESCE(v.visitas, 0) DESC, COALESCE(f.guardados, 0) DESC, e.fecha DESC
    LIMIT 8");
$parametrosPopulares = [
    ':visitas_desde' => $rangoActualParametros[':desde'],
    ':visitas_hasta' => $rangoActualParametros[':hasta'],
    ':favoritos_desde' => $rangoActualParametros[':desde'],
    ':favoritos_hasta' => $rangoActualParametros[':hasta'],
];
if (!$esAdministrador) {
    $parametrosPopulares[':organizador_visitas'] = $usuarioId;
    $parametrosPopulares[':organizador_favoritos'] = $usuarioId;
    $parametrosPopulares[':organizador_id'] = $usuarioId;
}
$eventosPopulares->execute($parametrosPopulares);
$populares = array_map(static fn (array $evento): array => [
    'titulo' => (string) $evento['titulo'],
    'visitas' => (int) $evento['visitas'],
    'guardados' => (int) $evento['guardados'],
], $eventosPopulares->fetchAll());

$porcentajeCambio = static function (?int $actual, ?int $anterior): ?float {
    if ($actual === null || $anterior === null || $anterior === 0) {
        return null;
    }
    return round((($actual - $anterior) / $anterior) * 100, 1);
};

echo json_encode([
    'periodo' => $periodo,
    'esAdministrador' => $esAdministrador,
    'totalEventos' => $totalEventos,
    'eventosActivos' => $eventosActivos,
    'visitas' => $visitasActuales,
    'usuariosGuardaron' => $usuariosGuardaronActual,
    'usuariosGuardaronAnteriores' => $usuariosGuardaronAnterior,
    'registrados' => $registrados,
    'registradosPeriodo' => $registradosActuales,
    'registradosPeriodoAnterior' => $registradosAnteriores,
    'promedioVisitas' => $totalEventos > 0 ? round($visitasActuales / $totalEventos, 1) : 0,
    'conversion' => $conversion,
    'eventosCreadosPeriodo' => $eventosCreadosActuales,
    'eventosCreadosAnteriores' => $eventosCreadosAnteriores,
    'visitasAnteriores' => $visitasAnteriores,
    'cambioEventos' => $porcentajeCambio($eventosCreadosActuales, $eventosCreadosAnteriores),
    'cambioVisitas' => $porcentajeCambio($visitasActuales, $visitasAnteriores),
    'cambioGuardados' => $porcentajeCambio($usuariosGuardaronActual, $usuariosGuardaronAnterior),
    'cambioRegistrados' => $porcentajeCambio($registradosActuales, $registradosAnteriores),
    'conversionMuestra' => $visitantesIdentificables,
    'series' => [
        'etiquetas' => $etiquetasTiempo,
        'visitas' => $visitasTiempo,
        'eventos' => $eventosCreadosTiempo,
        'meses' => array_values(array_map(
            static fn (string $clave): string => $nombresMeses[(int) substr($clave, 5, 2) - 1] . ' ' . substr($clave, 0, 4),
            array_values(array_unique(array_map(static fn (string $clave): string => substr($clave, 0, 7) . '-01', $clavesTiempo)))
        )),
        'eventosPorMes' => array_values(array_map(
            static fn (string $clave): int => $eventosPorMes[$clave] ?? 0,
            array_values(array_unique(array_map(static fn (string $clave): string => substr($clave, 0, 7) . '-01', $clavesTiempo)))
        )),
        'esDiario' => $esDiario,
    ],
    'totalGuardados' => $totalFavoritosActual,
    'populares' => $populares,
    'rango' => [
        'desde' => $inicio->format('Y-m-d'),
        'hasta' => $hoy->format('Y-m-d'),
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
