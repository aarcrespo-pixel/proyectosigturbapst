<?php

function sigtur_correo_configuracion(string $nombre): string
{
    static $cargado = false;
    if (!$cargado) {
        $rutaEnv = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
        if (is_readable($rutaEnv)) {
            $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lineas as $linea) {
                if (preg_match('/^\s*(SIGTUR_MAIL_FROM|SIGTUR_MAIL_FROM_NAME)\s*=\s*(.*)\s*$/', $linea, $coincidencias) !== 1) {
                    continue;
                }
                $valor = trim($coincidencias[2]);
                if (strlen($valor) >= 2 && (
                    ($valor[0] === '"' && substr($valor, -1) === '"')
                    || ($valor[0] === "'" && substr($valor, -1) === "'")
                )) {
                    $valor = substr($valor, 1, -1);
                }
                if (getenv($coincidencias[1]) === false) {
                    putenv($coincidencias[1] . '=' . $valor);
                }
            }
        }
        $cargado = true;
    }

    $valor = getenv($nombre);
    return is_string($valor) ? trim($valor) : '';
}

function sigtur_enviar_codigo_recuperacion(string $destinatario, string $codigo): void
{
    $remitente = sigtur_correo_configuracion('SIGTUR_MAIL_FROM');
    if (!filter_var($remitente, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Falta configurar SIGTUR_MAIL_FROM con la dirección Gmail autenticada.');
    }

    $nombreRemitente = sigtur_correo_configuracion('SIGTUR_MAIL_FROM_NAME');
    if ($nombreRemitente === '') {
        $nombreRemitente = 'SIGTUR';
    }
    $nombreRemitente = str_replace(["\r", "\n"], '', $nombreRemitente);
    $nombreCodificado = '=?UTF-8?B?' . base64_encode($nombreRemitente) . '?=';
    $asunto = '=?UTF-8?B?' . base64_encode('Código para recuperar tu cuenta de SIGTUR') . '?=';
    $mensaje = "Recibimos una solicitud para cambiar la contraseña de tu cuenta SIGTUR.\n\n"
        . "Tu código de verificación es: {$codigo}\n\n"
        . "El código vence en 15 minutos y solo se puede usar una vez. Si no solicitaste este cambio, ignora este correo; tu contraseña no cambiará.\n";
    $cabeceras = [
        'From' => $nombreCodificado . ' <' . $remitente . '>',
        'Reply-To' => $remitente,
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding' => '8bit',
        'X-Content-Type-Options' => 'nosniff',
    ];

    if (!mail($destinatario, $asunto, $mensaje, $cabeceras)) {
        throw new RuntimeException('PHP mail() no pudo entregar el correo de recuperación.');
    }
}
