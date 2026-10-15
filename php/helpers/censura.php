<?php
function sigtur_palabras_prohibidas(): array
{
    return [
        'carajo', 'concha', 'puta', 'pija', 'verga', 'boludo',
        'pelotudo', 'maricon', 'hijo de puta', 'bastardo', 'tarado',
        'mogolico', 'mongolico', 'estupido', 'down', 'retrasado',
        'idiota', 'conchudo', 'chupaverga', 'pajero',
    ];
}

function censurarTexto(string $texto): string
{
    if ($texto === '') {
        return $texto;
    }

    foreach (sigtur_palabras_prohibidas() as $palabra) {
        $patron = '/\b' . preg_quote($palabra, '/') . '\b/iu';
        $texto = preg_replace_callback($patron, static function (array $coincidencia): string {
            return str_repeat('*', mb_strlen($coincidencia[0], 'UTF-8'));
        }, $texto) ?? $texto;
    }

    return $texto;
}

function sigtur_comentario_con_palabras_prohibidas(string $texto): bool
{
    if (preg_match('//u', $texto) !== 1) {
        return true;
    }

    $normalizar = static function (string $valor): string {
        $valor = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $valor) ?? $valor), 'UTF-8');
        if (function_exists('iconv')) {
            $sinAcentos = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
            if ($sinAcentos !== false) {
                $valor = $sinAcentos;
            }
        }
        return strtr($valor, [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
        ]);
    };

    $normalizado = $normalizar($texto);
    if (preg_match('//u', $normalizado) !== 1) {
        return true;
    }

    foreach (sigtur_palabras_prohibidas() as $palabra) {
        $canonica = preg_replace('/[^a-z0-9]+/', '', $normalizar($palabra)) ?? '';
        if ($canonica === '') {
            continue;
        }
        $caracteres = str_split($canonica);
        $patron = '/(?<![\p{L}\p{N}])'
            . implode('[\p{P}\p{Z}\p{S}]*', array_map(static fn(string $letra): string => preg_quote($letra, '/'), $caracteres))
            . '(?![\p{L}\p{N}])/u';
        if (preg_match($patron, $normalizado) === 1) {
            return true;
        }
    }

    return false;
}
