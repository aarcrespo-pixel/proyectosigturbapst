<?php

function sigtur_ai_limitar_texto(string $texto, int $limite = 500): string
{
    $texto = trim(preg_replace('/\s+/', ' ', $texto) ?? '');
    return function_exists('mb_substr') ? mb_substr($texto, 0, $limite) : substr($texto, 0, $limite);
}

function sigtur_ai_contexto_sigtur(PDO $pdo): string
{
    $secciones = [];

    $eventos = $pdo->query("SELECT e.titulo, e.descripcion, e.fecha, e.ubicacion, e.categoria, e.tipo_entrada, e.precio, u.nombre_completo AS organizador
        FROM eventos e
        LEFT JOIN usuarios u ON u.id = e.organizador_id
        WHERE e.es_pasado = 0 AND e.fecha >= CURDATE()
        ORDER BY e.fecha ASC, e.id DESC
        LIMIT 20")->fetchAll();

    if ($eventos) {
        $lineas = [];
        foreach ($eventos as $evento) {
            $precio = $evento['tipo_entrada'] === 'De Pago' && $evento['precio'] !== null
                ? 'Precio: $' . $evento['precio']
                : 'Entrada: ' . ($evento['tipo_entrada'] ?: 'Consultar');
            $organizador = $evento['organizador'] ? 'Organizador: ' . $evento['organizador'] : '';
            $lineas[] = sprintf(
                '- %s | fecha: %s | lugar: %s | categoría: %s | %s%s | descripción: %s',
                sigtur_ai_limitar_texto((string) $evento['titulo'], 160),
                (string) $evento['fecha'],
                sigtur_ai_limitar_texto((string) $evento['ubicacion'], 160),
                sigtur_ai_limitar_texto((string) $evento['categoria'], 80),
                $precio,
                $organizador !== '' ? ' | ' . sigtur_ai_limitar_texto($organizador, 140) : '',
                sigtur_ai_limitar_texto((string) $evento['descripcion'], 280)
            );
        }
        $secciones[] = "EVENTOS FUTUROS DISPONIBLES EN LA BASE DE DATOS:\n" . implode("\n", $lineas);
    } else {
        $secciones[] = 'EVENTOS FUTUROS: No hay eventos futuros cargados en la base de datos.';
    }

    $lugares = $pdo->query("SELECT nombre, categoria, direccion, descripcion, atractivos, horarios, recomendaciones
        FROM lugares
        ORDER BY nombre ASC
        LIMIT 20")->fetchAll();

    if ($lugares) {
        $lineas = [];
        foreach ($lugares as $lugar) {
            $lineas[] = sprintf(
                '- %s | categoría: %s | dirección: %s | descripción: %s | atractivos: %s | horarios: %s | recomendaciones: %s',
                sigtur_ai_limitar_texto((string) $lugar['nombre'], 140),
                sigtur_ai_limitar_texto((string) $lugar['categoria'], 80),
                sigtur_ai_limitar_texto((string) $lugar['direccion'], 180),
                sigtur_ai_limitar_texto((string) $lugar['descripcion'], 220),
                sigtur_ai_limitar_texto((string) $lugar['atractivos'], 180),
                sigtur_ai_limitar_texto((string) $lugar['horarios'], 140),
                sigtur_ai_limitar_texto((string) $lugar['recomendaciones'], 180)
            );
        }
        $secciones[] = "LUGARES TURÍSTICOS Y GASTRONOMÍA DISPONIBLES EN LA BASE DE DATOS:\n" . implode("\n", $lineas);
    }

    $secciones[] = "SECCIONES PÚBLICAS DEL SITIO:
- Eventos: agenda de actividades culturales, deportivas y sociales.
- Turismo: destinos, rutas, experiencias, termas y actividades al aire libre.
- Lugares: fichas con dirección, descripción, horarios y recomendaciones.
- Gastronomía: propuestas orientativas de cafés, parrillas, almuerzos, productos regionales y recorridos.
- Mensajes: los turistas pueden usar el sistema de mensajes para comunicarse con organizadores o soporte; el contenido de esos mensajes es privado y no forma parte de este contexto.";

    return sigtur_ai_limitar_texto(implode("\n\n", $secciones), 14000);
}

function sigtur_ai_guardar_historial(PDO $pdo, int $usuarioId, string $mensaje, string $respuesta): void
{
    if ($usuarioId <= 0 || trim($mensaje) === '' || trim($respuesta) === '') {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO copilot_ai_historial (usuario_id, mensaje, respuesta) VALUES (:usuario_id, :mensaje, :respuesta)');
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':mensaje' => sigtur_ai_limitar_texto($mensaje, 2000),
        ':respuesta' => sigtur_ai_limitar_texto($respuesta, 6000),
    ]);
}
