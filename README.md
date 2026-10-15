# SIGTUR - Sistema Informático de Gestión del Turismo en Salto

## **Institución:** Escuela Superior Catalina H. de Castaños

## Proyecto
Proyecto desarrollado por estudiantes de 3º MC con el objetivo de contribuir a la modernización y fortalecimiento del turismo en el departamento de Salto mediante una plataforma digital que centralice información, eventos y servicios turísticos.

## Descripcion general del sistema
Pagina web destinada al turismo en Salto con: un formulario de inicio, secciones de eventos, lugares y turismo. Con acceso a configuraciones, soporte el cliente,  registro e inicio de sesión,traducción al Inglés y Portugués y modo claro y oscuro. Responsive para tabletas, celulares y todo tipo de pantallas

## Integrantes
- Aaron Crespo
- Santiago Diez
- Pio Monetta
- Federico Sarmiento
- Benjamin Reina

## Roles y primer administrador

Las cuentas nuevas reciben el rol `turista`. La primera cuenta administradora se asigna una sola vez desde una consola local, usando el correo de una cuenta ya registrada:

```powershell
C:\xampp\php\php.exe php\herramientas\promover-primer-administrador.php correo@dominio.com
```

El comando solo funciona por CLI y se cancela si ya existe un administrador. Luego, esa persona puede promover turistas a organizadores desde `Panel de administración`.

## Mensajes y archivos adjuntos

Los mensajes directos persisten en la tabla `mensajes`; al cargar la aplicación,
`php/conexion.php` agrega de forma idempotente la columna `video_url` a las bases
existentes. Se aceptan imágenes de hasta 10 MB, videos MP4/WEBM/MOV de hasta
25 MB y audios de hasta 10 MB. En XAMPP, para habilitar el tamaño máximo de video,
configura `upload_max_filesize = 25M`, `post_max_size = 40M` y
`max_file_uploads = 2` en `C:\xampp\php\php.ini` y reinicia Apache.

## Asistente Gemini

El asistente para turistas usa la API oficial de Google Gemini desde PHP; la clave
nunca se envía al navegador. Para XAMPP, inicia Apache y MySQL y configura la
clave de una de estas formas:

- El archivo privado `php/config/gemini.local.php` ya está creado para esta
  instalación local. Reemplazá su valor `api_key` desde el servidor y no quites
  la exclusión `php/config/gemini.local.php` de `.gitignore`.
- Alternativamente, define `GEMINI_API_KEY` en el entorno de Apache o en el
  archivo `.env` del directorio raíz. `.env` está ignorado por Git y protegido
  contra acceso web por `.htaccess`.

Abre `http://localhost/proyectosigturbapst-copilot-sigtur-changes-2026-10-13/`,
inicia sesión con una cuenta turista y abre el botón **Asistente Gemini**.
Preguntá, por ejemplo, “¿Qué eventos futuros hay en SIGTUR?”; el chat solo muestra
respuesta exitosa cuando Gemini responde y devuelve `provider: Google Gemini`.
Los errores de configuración, conexión y cuota se indican en el propio chat.
La extensión PHP `curl` debe estar habilitada en `C:\xampp\php\php.ini`.

Para cambiar o revocar la clave luego de la presentación, reemplazala o elimínala
en `php/config/gemini.local.php` (o en el entorno/`.env` si usaste esa opción).
Después, revocá la clave anterior en Google AI Studio. La clave proporcionada
debe considerarse expuesta por haber sido compartida en una conversación; se
recomienda rotarla antes de una presentación pública.

El esquema se inicializa o actualiza una vez y la versión aplicada se guarda en
`sigtur_schema_version`, evitando repetir migraciones y carga de datos en cada
petición. Al incorporar una migración nueva, actualiza la versión esperada en
`php/conexion.php`; el bloqueo de MySQL evita que dos peticiones migren a la vez.
