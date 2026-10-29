<?php

function sigtur_nombre_imagen_subida(int $usuarioId, string $nombreOriginal, string $extension): string
{
    $nombreBase = pathinfo(basename($nombreOriginal), PATHINFO_FILENAME);
    $nombreBase = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $nombreBase) ?? '', '-'));
    $nombreBase = substr($nombreBase !== '' ? $nombreBase : 'imagen', 0, 48);
    return 'usuario-' . max(0, $usuarioId) . '-' . $nombreBase . '-' . bin2hex(random_bytes(16)) . '.' . $extension;
}

function sigtur_url_imagen_subida(string $valor, string $carpeta, string $fallback = ''): string
{
    $valor = trim($valor);
    if ($valor === '') {
        return $fallback;
    }
    if (in_array($valor, ['default.jpg', 'default-avatar.png', 'default-banner.png', 'user-default.png'], true)) {
        return $fallback;
    }
    if (preg_match('/^(?:https?:)?\\/\\//i', $valor) === 1 || str_starts_with($valor, '/')) {
        return $valor;
    }
    if (basename(str_replace('\\', '/', $valor)) === $valor) {
        return '../uploads/' . trim($carpeta, '/') . '/' . rawurlencode($valor);
    }
    if (str_starts_with(str_replace('\\', '/', $valor), '../uploads/')) {
        return '../uploads/' . rawurlencode(implode('/', array_map('rawurldecode', explode('/', substr(str_replace('\\', '/', $valor), strlen('../uploads/'))))));
    }
    return $valor;
}

function sigtur_url_imagen_evento(string $slug, string $valor, string $fallback = '../img/porco.avif'): string
{
    $url = sigtur_url_imagen_subida($valor, 'eventos', $fallback);
    if (!str_contains(strtolower($url), 'encrypted-tbn')) {
        return $url;
    }

    $imagenesCatalogo = [
        'bambola' => '../img/bambi.jpg',
        'carrera-de-bicicleta' => '../img/moto_cross.jpg',
        'circuito-en-bicicleta' => '../img/evento2.jpg',
        'copa-de-natacion' => '../img/TER%202.jpg',
        'futbol-x5' => '../img/lsf.jpg',
        'la-ferne' => '../img/porco.avif',
        'polo' => '../img/porco.avif',
        'rally' => '../img/moto_cross.jpg',
        'streetball-salto' => '../img/lsb.jpg',
        'torneo-de-beach-volley' => '../img/TER%202.jpg',
    ];

    return $imagenesCatalogo[$slug] ?? $url;
}

function sigtur_nombre_imagen_desde_ruta(string $valor, string $prefijoUploads): string
{
    $valor = str_replace('\\', '/', $valor);
    $prefijoUploads = trim(str_replace('\\', '/', $prefijoUploads), '/') . '/';
    $posicion = strpos($valor, $prefijoUploads);
    if ($posicion === false) {
        return $valor;
    }
    $rutaDespuesDePrefijo = substr($valor, $posicion + strlen($prefijoUploads));
    return basename($rutaDespuesDePrefijo) === $rutaDespuesDePrefijo ? $rutaDespuesDePrefijo : $valor;
}
