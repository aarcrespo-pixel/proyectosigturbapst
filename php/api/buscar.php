<?php
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim((string) ($_GET['q'] ?? ''));
if (mb_strlen($q) < 3) {
    echo json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$termino = '%' . $q . '%';

/* UNION combina las dos fuentes persistentes del catálogo en una respuesta
   homogénea. Cada SELECT usa parámetros separados porque PDO con prepares
   nativos no permite reutilizar el mismo placeholder varias veces. */
$consulta = $pdo->prepare("\n    SELECT id, titulo AS nombre, 'evento' AS tipo, categoria, imagen_portada AS imagen\n    FROM eventos\n    WHERE titulo LIKE :evento_q OR descripcion LIKE :evento_q_descripcion\n    UNION\n    SELECT id, nombre AS nombre, 'lugar' AS tipo, categoria, imagen AS imagen\n    FROM lugares\n    WHERE nombre LIKE :lugar_q OR descripcion LIKE :lugar_q_descripcion\n    LIMIT 8\n");
$consulta->execute([
    ':evento_q' => $termino,
    ':evento_q_descripcion' => $termino,
    ':lugar_q' => $termino,
    ':lugar_q_descripcion' => $termino,
]);
$resultados = $consulta->fetchAll();

/* Gastronomía es actualmente contenido editorial estático, no una tabla SQL.
   Lo incorporamos al mismo contrato JSON para que el buscador cubra esa vista
   sin inventar una persistencia paralela ni romper instalaciones existentes. */
$gastronomia = [
    ['id' => 'sabores-locales', 'nombre' => 'Sabores locales', 'categoria' => 'Gastronomía', 'imagen' => '../img/gastronomia.jpg'],
    ['id' => 'para-cada-momento', 'nombre' => 'Para cada momento', 'categoria' => 'Gastronomía', 'imagen' => '../img/carta.png'],
    ['id' => 'produccion-regional', 'nombre' => 'Producción regional', 'categoria' => 'Gastronomía', 'imagen' => '../img/feria_emprendedores.jpg'],
];
foreach ($gastronomia as $item) {
    if (mb_stripos($item['nombre'], $q) !== false || mb_stripos($item['categoria'], $q) !== false) {
        $resultados[] = $item + ['tipo' => 'gastronomia'];
    }
}

$documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$projectRoot = str_replace('\\', '/', dirname(__DIR__, 2));
$publicBase = $documentRoot !== '' ? rtrim(str_ireplace($documentRoot, '', $projectRoot), '/') . '/' : '/';
foreach ($resultados as &$resultado) {
    $resultado['tipoLabel'] = $resultado['tipo'] === 'evento' ? 'EVENTO' : ($resultado['tipo'] === 'lugar' ? 'LUGAR' : 'GASTRONOMÍA');
    if (!preg_match('/^https?:\\/\\//i', $resultado['imagen'] ?? '')) {
        $resultado['imagen'] = $publicBase . ltrim(str_replace(['../', './'], '', $resultado['imagen'] ?? ''), '/');
    }
}
unset($resultado);

echo json_encode(array_slice($resultados, 0, 8), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
