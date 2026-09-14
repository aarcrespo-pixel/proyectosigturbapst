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
