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
        $rutaSubida = substr(str_replace('\\', '/', $valor), strlen('../uploads/'));
        $segmentosRuta = array_map('rawurlencode', array_map('rawurldecode', explode('/', $rutaSubida)));
        return '../uploads/' . implode('/', $segmentosRuta);
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

function sigtur_atribucion_imagen_lugar(string $slug): ?array
{
    $atribuciones = [
        'acuamania' => [
            'texto' => 'Flavia232 · CC BY-SA 4.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:Parque_acu%C3%A1tico_Acuaman%C3%ADa.jpg',
        ],
        'biblioteca-municipal-salto' => [
            'texto' => 'Nemo bis · CC BY-SA 3.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:2017_UEF_Joensuu_library_05.jpg',
        ],
        'costanera-sur' => [
            'texto' => 'ManuelaGB · CC BY-SA 3.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:Atr%C3%A1s,_el_sol._R%C3%ADo_Uruguay._Salto,_Uruguay.JPG',
        ],
        'museo-del-hombre-y-la-tecnologia' => [
            'texto' => 'Lucia Giambiaggi · CC BY-SA 3.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:Museo_del_Hombre_y_la_Tecnolog%C3%ADa_(Salto,_Uruguay).JPG',
        ],
        'muelle-negro' => [
            'texto' => 'ManuelaGB · CC BY-SA 3.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:R%C3%ADo_Uruguay_visto_desde_Salto,_Uruguay.JPG',
        ],
        'parque-benito-solari' => [
            'texto' => 'Richard aristegui · CC BY-SA 3.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:PARQUE_DISE%C3%91ADO_AL_ESTILO_FRANCES_DE_LA_EPOCA_(1900).JPG',
        ],
        'plaza-artigas' => [
            'texto' => 'Cornelius · CC BY-SA 2.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:Plaza_Artigas,_Salto.jpg',
        ],
        'plaza-treinta-y-tres-orientales' => [
            'texto' => 'Lourdes Berretta · CC BY-SA 4.0',
            'fuente' => 'https://commons.wikimedia.org/wiki/File:Plaza_Treinta_y_Tres_Orientales.JPG',
        ],
    ];

    return $atribuciones[$slug] ?? null;
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
