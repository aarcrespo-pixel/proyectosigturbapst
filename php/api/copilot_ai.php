<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/copilot_ai.php';

header('Content-Type: application/json; charset=utf-8');

function sigtur_ai_respuesta_local(string $mensaje): string
{
    $texto = strtolower(trim($mensaje));

    if ($texto === '') {
        return 'Podés preguntarme por eventos, lugares, gastronomía o recomendaciones de Salto.';
    }

    if (preg_match('/(evento|agenda|hoy|mañana|qué pasa|recomendac|actividad)/i', $mensaje)) {
        return 'Te recomiendo mirar la agenda de eventos del sitio y filtrar por fecha, ubicación o categoría. Si querés, podés preguntarme por un tipo de evento concreto como música, deportes, cultura o gastronomía.';
    }

    if (preg_match('/(lugar|tour|dónde|lugares|visitar|mirador|parque|playa|centro)/i', $mensaje)) {
        return 'Salto tiene muchos lugares para visitar: el centro, costanera, espacios culturales y puntos de paseo. Si querés, te puedo sugerir una ruta según si te gusta naturaleza, historia o comer bien.';
    }

    if (preg_match('/(comer|gastronom|restaurant|restaurante|pizza|asado|café|bar)/i', $mensaje)) {
        return 'Para gastronomía, podés buscar propuestas del centro y de la costanera, además de cafés y espacios para comer al aire libre. Si querés, te ayudo a armar una recomendación según tu gusto.';
    }

    if (preg_match('/(transporte|cómo llegar|ir|movilidad|uber|taxi|bus)/i', $mensaje)) {
        return 'En general, lo más práctico es consultar la ubicación del evento y elegir según tu punto de partida. Si me decís desde dónde salís, te puedo orientar mejor la mejor opción.';
    }

    if (preg_match('/(hola|buenas|buenos|ayuda|saludo)/i', $mensaje)) {
        return '¡Hola! Soy tu asistente turístico de SIGTUR. Puedo ayudarte con eventos, recomendaciones, lugares y gastronomía de Salto.';
    }

    return 'Puedo ayudarte a encontrar eventos, lugares para visitar, opciones gastronómicas y recomendaciones para disfrutar Salto. Probá preguntarme por una categoría o un lugar concreto.';
}

function sigtur_ai_env_value(string $name, array $aliases = []): string
{
    static $loaded = false;
    if (!$loaded) {
        $envPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
        if (is_readable($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (preg_match('/^\s*([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)\s*$/', $line, $matches) !== 1) {
                    continue;
                }

                $value = trim($matches[2]);
                if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                    $value = substr($value, 1, -1);
                }
                putenv($matches[1] . '=' . $value);
                $_ENV[$matches[1]] = $value;
            }
        }
        $loaded = true;
    }

    $candidates = array_merge([$name], $aliases);
    foreach ($candidates as $candidate) {
        $value = getenv($candidate);
        if ($value === false || trim((string) $value) === '') {
            $value = $_ENV[$candidate] ?? null;
        }
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }
    }

    return '';
}

function sigtur_ai_respuesta_api(string $mensaje, string $contexto, ?string $previousResponseId = null): ?array
{
    global $sigturAiError;

    $endpoint = sigtur_ai_env_value('COPILOT_AI_ENDPOINT', ['AZURE_OPENAI_ENDPOINT', 'OPENAI_API_BASE']);
    $apiKey = sigtur_ai_env_value('COPILOT_AI_API_KEY', ['AZURE_OPENAI_API_KEY', 'OPENAI_API_KEY']);
    $deployment = sigtur_ai_env_value('COPILOT_AI_DEPLOYMENT', ['AZURE_OPENAI_DEPLOYMENT', 'AZURE_OPENAI_MODEL', 'OPENAI_MODEL']);
    $bearerToken = sigtur_ai_env_value('AZURE_OPENAI_BEARER_TOKEN', ['COPILOT_AI_BEARER_TOKEN']);

    if ($endpoint === '' || ($apiKey === '' && $bearerToken === '')) {
        $sigturAiError = 'La IA todavía no está configurada. Completá el archivo .env con el endpoint, la API key y el deployment de Azure OpenAI.';
        return null;
    }

    $url = rtrim($endpoint, '/');
    $isAzureOpenAiHost = stripos($url, 'openai.azure.com') !== false || stripos($url, 'services.ai.azure.com') !== false;
    if ($isAzureOpenAiHost && stripos($url, '/openai/') === false) {
        $url .= '/openai/v1';
    }
    if (stripos($url, '/responses') === false) {
        $url .= '/responses';
    }

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    if ($bearerToken !== '') {
        $headers[] = 'Authorization: Bearer ' . $bearerToken;
    } else {
        $headers[] = 'api-key: ' . $apiKey;
    }

    $payload = [
        'model' => $deployment !== '' ? $deployment : 'gpt-4o-mini',
        'instructions' => 'Sos el asistente virtual de Sigtur Salto. Respondé en español, de forma clara, breve y útil para turistas. Solo respondé sobre turismo en Salto y sobre la información pública disponible en Sigtur. No inventes eventos, fechas, horarios, precios, direcciones, organizadores ni actividades. Si el contexto no tiene la información suficiente, decilo claramente y recomendá consultar la sección correspondiente del sitio. No reveles datos privados, contraseñas, mensajes, emails, teléfonos ni información interna de usuarios u organizadores.',
        'input' => "CONTEXTO PÚBLICO ACTUAL DE SIGTUR:\n" . $contexto . "\n\nPREGUNTA DEL TURISTA:\n" . $mensaje,
        'max_output_tokens' => 300,
    ];
    if ($previousResponseId !== null && preg_match('/^resp_[A-Za-z0-9_-]+$/', $previousResponseId) === 1) {
        $payload['previous_response_id'] = $previousResponseId;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($raw === false || $httpCode >= 400) {
        error_log(sprintf('SIGTUR AI request failed: HTTP %d%s', $httpCode, $curlError !== '' ? ' - ' . $curlError : ''));
        $sigturAiError = 'Azure OpenAI no pudo responder. Revisá el endpoint, el deployment, la API key y la conexión del servidor.';
        return null;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        $sigturAiError = 'Azure OpenAI devolvió una respuesta inválida.';
        return null;
    }

    $content = $decoded['output_text'] ?? null;
    if (!is_string($content) || trim($content) === '') {
        $parts = [];
        foreach (($decoded['output'] ?? []) as $outputItem) {
            foreach (($outputItem['content'] ?? []) as $contentItem) {
                if (isset($contentItem['text']) && is_string($contentItem['text'])) {
                    $parts[] = $contentItem['text'];
                }
            }
        }
        $content = implode('', $parts);
    }
    if ((!is_string($content) || trim($content) === '') && isset($decoded['choices'][0]['message']['content'])) {
        $content = $decoded['choices'][0]['message']['content'];
    }

    if (!is_string($content) || trim($content) === '') {
        return null;
    }

    return [
        'id' => isset($decoded['id']) && is_string($decoded['id']) ? $decoded['id'] : null,
        'text' => trim($content),
    ];
}

$usuarioActual = sigtur_usuario_actual($pdo);
if (!$usuarioActual) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (sigtur_normalizar_rol($usuarioActual['rol'] ?? null) !== 'turista') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Esta función está disponible solo para turistas.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? '');
if ($accion === 'historial' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $historial = $pdo->prepare('SELECT id, mensaje, respuesta, fecha_creacion FROM copilot_ai_historial WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC, id DESC LIMIT 50');
    $historial->execute([':usuario_id' => (int) $usuarioActual['id']]);
    echo json_encode(['success' => true, 'items' => $historial->fetchAll()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($accion === 'borrar_historial') {
    $csrfToken = $_POST['csrf_token'] ?? null;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !sigtur_validar_csrf(is_string($csrfToken) ? $csrfToken : null)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'La sesión expiró. Recargá la página e intentá otra vez.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $borrarHistorial = $pdo->prepare('DELETE FROM copilot_ai_historial WHERE usuario_id = :usuario_id');
    $borrarHistorial->execute([':usuario_id' => (int) $usuarioActual['id']]);
    echo json_encode(['success' => true, 'message' => 'Chat eliminado correctamente.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$mensaje = trim((string) ($_POST['message'] ?? ''));
$longitudMensaje = function_exists('mb_strlen') ? mb_strlen($mensaje) : strlen($mensaje);
if ($mensaje === '' || $longitudMensaje > 800) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => $mensaje === '' ? 'Escribí una pregunta antes de enviar.' : 'La pregunta no puede superar los 800 caracteres.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sigturAiError = null;
$contextoSigtur = sigtur_ai_contexto_sigtur($pdo);
$previousResponseId = trim((string) ($_POST['previous_response_id'] ?? ''));
$respuestaApi = sigtur_ai_respuesta_api($mensaje, $contextoSigtur, $previousResponseId !== '' ? $previousResponseId : null);
if ($respuestaApi === null || trim((string) ($respuestaApi['text'] ?? '')) === '') {
    if ($sigturAiError !== null) {
        http_response_code(503);
        echo json_encode(['success' => false, 'message' => $sigturAiError], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $respuesta = sigtur_ai_respuesta_local($mensaje);
    $responseId = null;
} else {
    $respuesta = $respuestaApi['text'];
    $responseId = $respuestaApi['id'] ?? null;
}

sigtur_ai_guardar_historial($pdo, (int) $usuarioActual['id'], $mensaje, $respuesta);

echo json_encode([
    'success' => true,
    'answer' => $respuesta,
    'response_id' => $responseId,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
