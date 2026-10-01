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


