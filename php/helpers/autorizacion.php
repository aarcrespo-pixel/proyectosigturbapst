<?php
function sigtur_normalizar_rol(?string $rol): string
{
    $rol = strtolower(trim((string) $rol));
    if (in_array($rol, ['administrador', 'admin', 'moderador'], true)) {
        return 'administrador';
    }
    if ($rol === 'organizador') {
        return 'organizador';
    }
    return 'turista';
}

function sigtur_usuario_actual(PDO $pdo): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
    if ($usuarioId <= 0) {
        return null;
    }

    $consulta = $pdo->prepare('SELECT id, nombre_completo, email, rol FROM usuarios WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $usuarioId]);
    $usuario = $consulta->fetch();
    if (!$usuario) {
        $_SESSION = [];
        session_destroy();
        return null;
    }

    $usuario['rol'] = sigtur_normalizar_rol($usuario['rol'] ?? null);
    $_SESSION['rol'] = $usuario['rol'];
    return $usuario;
}

function sigtur_requerir_rol(PDO $pdo, array $roles, string $loginUrl = 'login.php', string $deniedUrl = 'eventos.php'): array
{
    $usuario = sigtur_usuario_actual($pdo);
    if (!$usuario) {
        header('Location: ' . $loginUrl);
        exit;
    }

    $rolesPermitidos = array_map('sigtur_normalizar_rol', $roles);
    if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
        http_response_code(403);
        ?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Acceso denegado | SIGTUR</title><link rel="stylesheet" href="../css/estilos.css"></head><body><main style="max-width:640px;margin:12vh auto;padding:2rem"><h1>Acceso denegado</h1><p>Tu cuenta no tiene permisos para acceder a esta sección.</p><a href="<?= htmlspecialchars($deniedUrl, ENT_QUOTES, 'UTF-8') ?>">Volver a eventos</a></main></body></html><?php
        exit;
    }

    return $usuario;
}

function sigtur_csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function sigtur_validar_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function sigtur_liberar_catalogo_legacy(PDO $pdo, int $usuarioId): void
{
    $slugsCatalogo = [
        'bambola', 'campeonato-de-pesca', 'carrera-de-bicicleta', 'carrera-nocturna',
        'circuito-en-bicicleta', 'copa-de-natacion', 'costanera', 'exposalto',
        'feria-de-emprendedores', 'festival-de-la-naranja', 'futbol-x5', 'la-ferne',
        'lafosa-bike', 'muestra-de-danza', 'polo', 'porco-negro', 'rally',
        'streetball-salto', 'surf-y-kayak', 'torneo-de-ajedrez', 'torneo-de-beach-volley',
    ];
    $marcadores = [];
    $parametros = [':usuario' => $usuarioId];
    foreach ($slugsCatalogo as $indice => $slug) {
        $marcador = ':slug' . $indice;
        $marcadores[] = $marcador;
        $parametros[$marcador] = $slug;
    }
    $liberar = $pdo->prepare('UPDATE eventos SET organizador_id = 0 WHERE organizador_id = :usuario AND slug IN (' . implode(', ', $marcadores) . ')');
    $liberar->execute($parametros);
}