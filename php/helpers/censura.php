<?php
/**
 * Reemplaza vocabulario prohibido antes de persistir contenido generado por
 * usuarios. \b exige límites de palabra, por lo que "puto" queda intacto
 * porque no forma parte de esta lista y no se censura por coincidencia parcial.
 */
function censurarTexto(string $texto): string
{
    if ($texto === '') {
        return $texto;
    }

    $palabrasProhibidas = [
        'carajo', 'concha', 'puta', 'pija', 'verga', 'boludo',
        'pelotudo', 'maricon', 'hijo de puta', 'bastardo', 'tarado',
        'mogolico', 'mongolico', 'estupido', 'down', 'retrasado',
        'idiota', 'conchudo', 'chupaverga', 'pajero', 
    ];

    foreach ($palabrasProhibidas as $palabra) {
        /* La expresión Unicode con límites evita reemplazos dentro de otras
           palabras y conserva explícitamente términos no listados como "puto". */
        $patron = '/\b' . preg_quote($palabra, '/') . '\b/iu';
        $texto = preg_replace_callback($patron, static function (array $coincidencia) use ($palabra): string {
            return str_repeat('*', mb_strlen($coincidencia[0], 'UTF-8'));
        }, $texto) ?? $texto;
    }

    return $texto;
}

function censurarComentario(string $texto): string
{
    if ($texto === '') {
        return $texto;
    }

    $palabrasProhibidas = [
        'ass', 'arse', 'arsehole', 'bastard', 'bitch', 'bollocks', 'bullshit',
        'crap', 'cunt', 'damn', 'dick', 'dickhead', 'dildo', 'douche', 'fag',
        'faggot', 'fuck', 'fucker', 'fucking', 'motherfucker', 'nigger', 'nigga',
        'piss', 'prick', 'pussy', 'shit', 'shitty', 'slut', 'twat', 'whore',
        'asshole', 'son of a bitch', 'son of a motherfucker', 'idiot', 'stupid',
        'moron', 'loser', 'worthless', 'pathetic', 'disgusting', 'ugly',
        'freak', 'scumbag', 'piece of trash', 'piece of shit', 'dumbass',
        'arschloch', 'fotze', 'ficken', 'scheisse', 'scheiße', 'schlampe',
        'hurensohn', 'wichser', 'arschgeige', 'dummkopf', 'merde', 'putain',
        'connard', 'connasse', 'salope', 'enculé', 'encule', 'bordel', 'cul',
        'idiot', 'imbecile', 'imbécile', 'minable', 'ordure',
        'cazzo', 'coglione', 'stronzo', 'figa', 'troia', 'vaffanculo',
        'merda', 'bastardo', 'puttana', 'porca', 'deficiente', 'stupido',
        'caralho', 'porra', 'puta', 'puto', 'merda', 'buceta', 'cuzao',
        'babaca', 'foda', 'fodido', 'arrombado', 'desgracado', 'desgraçado',
        'idiota', 'imbecil', 'imbécil', 'inutil', 'inútil', 'basura',
        'asqueroso', 'asquerosa', 'repugnante', 'despreciable', 'patetico',
        'patético', 'miserable', 'fracasado', 'fracasada', 'perdedor',
        'perdedora', 'inservible', 'estorbo', 'payaso', 'payasa', 'ridiculo',
        'ridícula', 'fea', 'feo', 'horrible', 'maldito', 'maldita',
        'enfermo', 'enferma', 'degenerado', 'degenerada', 'ignorante',
        'imbecil', 'imbécil', 'cretino', 'cretina', 'nefasto', 'nefasto',
        'porqueria', 'porquería', 'mierda', 'mierdoso', 'mierdosa',
        'concha', 'pija', 'verga', 'boludo', 'pelotudo', 'maricon', 'maricón',
        'hijo de puta', 'hija de puta', 'bastardo', 'tarado', 'mogolico',
        'mogólico', 'mongolico', 'mongólico', 'estupido', 'estúpido', 'idiota',
        'conchudo', 'chupaverga', 'pajero', 'pajera', 'culero', 'culera',
        'inutil', 'inútil', 'asqueroso', 'asquerosa', 'repugnante',
        'despreciable', 'patetico', 'patético', 'miserable', 'fracasado',
        'fracasada', 'perdedor', 'perdedora', 'inservible', 'estorbo',
        'ridiculo', 'ridícula', 'feo', 'fea', 'horrible', 'maldito', 'maldita',
        'enfermo', 'enferma', 'degenerado', 'degenerada', 'ignorante',
        'cretino', 'cretina', 'nefasto', 'nefasto', 'porqueria', 'porquería',
        'chingada', 'chingado', 'chingar', 'chinga tu madre', 'pinche',
        'mamaguevo', 'coño', 'cojudo', 'cabrón', 'cabron', 'mierda',
        'mierdas', 'mierdero', 'malparido', 'gonorrea', 'careverga',
        'puta madre', 'putamadre', 'la concha de tu madre',
        'idiota', 'imbecil', 'imbécil', 'estupido', 'estúpido', 'inutil',
        'inútil', 'basura', 'asqueroso', 'asquerosa', 'repugnante',
        'despreciable', 'patetico', 'patético', 'miserable', 'fracasado',
        'fracasada', 'perdedor', 'perdedora', 'inservible', 'estorbo',
        'payaso', 'payasa', 'ridiculo', 'ridícula', 'feo', 'fea', 'horrible',
        'maldito', 'maldita', 'enfermo', 'enferma', 'degenerado', 'degenerada',
        'ignorante', 'cretino', 'cretina', 'nefasto', 'porqueria', 'porquería',
        'сука', 'блядь', 'блять', 'хуй', 'пизда', 'ебать', 'мудак',
        'говно', 'дерьмо', 'урод', 'тупой', 'ничтожество', 'ублюдок',
        'يخرب', 'كس', 'قحبة', 'كلب', 'غبي', 'حقير',
        '操你妈', '他妈的', '傻逼', '草泥马', '王八蛋', '混蛋',
        'バカ', 'くそ', 'ちくしょう', 'くたばれ',
        '씨발', '병신', '개새끼', '바보', '쓰레기',
    ];

    $normalizarCaracter = static function (string $caracter): string {
        $caracter = mb_strtolower($caracter, 'UTF-8');
        $equivalencias = [
            '0' => 'o', '1' => 'i', '!' => 'i', '3' => 'e', '4' => 'a',
            '@' => 'a', '5' => 's', '$' => 's', '7' => 't', '8' => 'b',
        ];
        if (isset($equivalencias[$caracter])) {
            return $equivalencias[$caracter];
        }

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $caracter);
        if (is_string($ascii) && preg_match('/^[a-z0-9]+$/i', $ascii)) {
            return strtolower($ascii);
        }

        return preg_match('/[\p{L}\p{N}]/u', $caracter) ? $caracter : '';
    };

    $caracteresTexto = preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY);
    $caracteresNormalizados = [];
    $origenPorByte = [];
    foreach ($caracteresTexto as $indice => $caracter) {
        $normalizado = $normalizarCaracter($caracter);
        foreach (preg_split('//u', $normalizado, -1, PREG_SPLIT_NO_EMPTY) as $letra) {
            $caracteresNormalizados[] = $letra;
            foreach (str_split($letra) as $_byte) {
                $origenPorByte[] = $indice;
            }
        }
    }

    if (!$caracteresNormalizados) {
        return $texto;
    }

    $textoNormalizado = implode('', $caracteresNormalizados);
    $rangosCensura = [];
    $terminosNormalizados = [];
    foreach ($palabrasProhibidas as $palabra) {
        $terminoNormalizado = '';
        foreach (preg_split('//u', $palabra, -1, PREG_SPLIT_NO_EMPTY) as $caracter) {
            $terminoNormalizado .= $normalizarCaracter($caracter);
        }
        if ($terminoNormalizado !== '') {
            $terminosNormalizados[$terminoNormalizado] = true;
        }
    }

    foreach (array_keys($terminosNormalizados) as $termino) {
        $coincidencias = [];
        preg_match_all('/' . preg_quote($termino, '/') . '/u', $textoNormalizado, $coincidencias, PREG_OFFSET_CAPTURE);
        foreach ($coincidencias[0] as [$coincidencia, $inicioByte]) {
            $finByte = $inicioByte + strlen($coincidencia) - 1;
            $inicioOriginal = $origenPorByte[$inicioByte] ?? null;
            $finOriginal = $origenPorByte[$finByte] ?? null;
            if ($inicioOriginal === null || $finOriginal === null) {
                continue;
            }

            $anteriorEsAlfanumerico = $inicioOriginal > 0
                && preg_match('/[\p{L}\p{N}]/u', $caracteresTexto[$inicioOriginal - 1]);
            $siguienteEsAlfanumerico = $finOriginal + 1 < count($caracteresTexto)
                && preg_match('/[\p{L}\p{N}]/u', $caracteresTexto[$finOriginal + 1]);
            if ($anteriorEsAlfanumerico || $siguienteEsAlfanumerico) {
                continue;
            }

            $rangosCensura[] = [$inicioOriginal, $finOriginal];
        }
    }

    if (!$rangosCensura) {
        return $texto;
    }

    usort($rangosCensura, static fn (array $a, array $b): int => $a[0] <=> $b[0]);
    $rangosUnidos = [];
    foreach ($rangosCensura as [$inicio, $fin]) {
        $ultimo = count($rangosUnidos) - 1;
        if ($ultimo >= 0 && $inicio <= $rangosUnidos[$ultimo][1] + 1) {
            $rangosUnidos[$ultimo][1] = max($rangosUnidos[$ultimo][1], $fin);
        } else {
            $rangosUnidos[] = [$inicio, $fin];
        }
    }

    $posicionesCensuradas = [];
    foreach ($rangosUnidos as [$inicio, $fin]) {
        for ($indice = $inicio; $indice <= $fin; $indice++) {
            if (preg_match('/[\p{L}\p{N}]/u', $caracteresTexto[$indice])) {
                $posicionesCensuradas[$indice] = true;
            }
        }
    }

    foreach (array_keys($posicionesCensuradas) as $indice) {
        $caracteresTexto[$indice] = '*';
    }

    return implode('', $caracteresTexto);
}
