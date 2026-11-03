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

## Recuperación de contraseña por Gmail

La recuperación envía un código aleatorio de un solo uso, válido por 15 minutos. Para habilitar el envío en XAMPP:

1. En la cuenta Gmail remitente, habilita la verificación en dos pasos y crea una **contraseña de aplicación**. No uses la contraseña normal de Gmail.
2. Copia `.env.example` como `.env` y configura `SIGTUR_MAIL_FROM` con la dirección Gmail autenticada.
3. En `C:\xampp\sendmail\sendmail.ini`, configura `smtp_server=smtp.gmail.com`, `smtp_port=587`, `smtp_ssl=auto`, `auth_username`, `auth_password` (la contraseña de aplicación) y `force_sender` con esa misma dirección.
4. En `C:\xampp\php\php.ini`, configura `sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"` y reinicia Apache desde XAMPP.

No subas `.env` ni compartas la contraseña de aplicación. El formulario limita las solicitudes, no revela si un correo está registrado, limita los intentos del código y almacena solo su hash. En producción, usa HTTPS y guarda las credenciales en el gestor de secretos del servidor.

