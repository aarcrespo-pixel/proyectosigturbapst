<?php

function sigtur_envio_ya_procesado(string $ambito, mixed $clave): bool
{
    if (!is_string($clave) || preg_match('/^[a-f0-9]{32}$/', $clave) !== 1) {
        return false;
    }

    $ahora = time();
    $envios = $_SESSION['sigtur_envios_procesados'] ?? [];
    foreach ($envios as $id => $marcaTiempo) {
        if (!is_int($marcaTiempo) || $marcaTiempo < $ahora - 86400) {
            unset($envios[$id]);
        }
    }
    $_SESSION['sigtur_envios_procesados'] = $envios;

    return isset($envios[$ambito . ':' . $clave]);
}

function sigtur_marcar_envio_procesado(string $ambito, mixed $clave): void
{
    if (!is_string($clave) || preg_match('/^[a-f0-9]{32}$/', $clave) !== 1) {
        return;
    }

    $_SESSION['sigtur_envios_procesados'][$ambito . ':' . $clave] = time();
}
