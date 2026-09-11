<?php
$host = '127.0.0.1';
$dbname = 'bapst';
$dbUser = 'root';
$dbPassword = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nombre_completo` varchar(100) NOT NULL,
            `cedula` varchar(20) NOT NULL,
            `email` varchar(100) NOT NULL,
            `telefono` varchar(20) DEFAULT NULL,
            `password` varchar(255) NOT NULL,
            `avatar` varchar(255) NOT NULL DEFAULT 'default-avatar.png',
            `nickname` varchar(50) DEFAULT NULL,
            `biografia` text DEFAULT NULL,
            `ubicacion` varchar(100) DEFAULT NULL,
            `sitio_web` varchar(255) DEFAULT NULL,
            `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_email` (`email`),
            UNIQUE KEY `uniq_cedula` (`cedula`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");

    $columnas = $pdo->query("SHOW COLUMNS FROM `usuarios`")->fetchAll(PDO::FETCH_COLUMN, 0);
    $camposNuevos = [
        'avatar' => "ALTER TABLE `usuarios` ADD COLUMN `avatar` varchar(255) NOT NULL DEFAULT 'default-avatar.png'",
        'nickname' => "ALTER TABLE `usuarios` ADD COLUMN `nickname` varchar(50) DEFAULT NULL",
        'biografia' => "ALTER TABLE `usuarios` ADD COLUMN `biografia` text DEFAULT NULL",
        'ubicacion' => "ALTER TABLE `usuarios` ADD COLUMN `ubicacion` varchar(100) DEFAULT NULL",
        'sitio_web' => "ALTER TABLE `usuarios` ADD COLUMN `sitio_web` varchar(255) DEFAULT NULL",
    ];

    foreach ($camposNuevos as $nombreCampo => $sqlAlter) {
        if (!in_array($nombreCampo, $columnas, true)) {
            $pdo->exec($sqlAlter);
        }
    }
} catch (PDOException $e) {
    http_response_code(503);
    exit('No se pudo conectar a la base de datos.');
}
?>