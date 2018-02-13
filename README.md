# rrcPHPBase (PROYECTO DE FASE DE DESARROLLO. NO ES ESTABLE AUN)

## Descripción

Plantilla base para la construcción de sitios PHP.
Se incluye una aplicación de ejemplo de minería de correos electrónicos.

## Funcionalidad actual

Actualmente, rrcPHPBase se encuentra en desarrollo, por lo que la funcionalidad va creciendo con cada actualización.

Por el momento dispone de las siguientes características:

+ Página de inicio de sesión integrada (index.php con la opción de autentificación activada en el archivo de configuración).
+ Autentificación mediante Active Directory o LDAP.
+ Personalización de la página de inicio de sesión (título, subtítulo e imagen de fondo).
+ Página de autocomprobación de requisitos (index.php?requirements).
+ Acceso mediante una única página (index.php?p=&lt;pagina_seleccionada&gt;).

## Funcionalidad prevista

+ Autentificación mediante MySQL.

## Aplicación personalizada

### Archivo de configuración de rrcPHPBase [config.php]

Este archivo contiene la configuración básica para el correcto funcionamiento de la aplicación personalizada desarrollada con rrcPHPBase.

Se facilita una plantilla con todos los posibles valores de configuración [config.template.php].

### Archivos que deben existir en la carpeta [site/]

+ site/config.php       - Archivo de configuración
+ site/header.php       - Contenido del sitio web que se cargará antes de la página seleccionada
+ site/footer.php       - Contenido del sitio web que se cargará después de la página seleccionada
+ site/home.php         - Página de inicio
+ site/404.php          - Página que se mostrará cuando se solicite una página que no exista
+ site/images/login.jpg - Fondo de pantalla de la página de inicio de sesión (es opcional)

### Formato del archivo de configuración [site/config.php]

$config['app'][&lt;identificador&gt;] = &lt;valor&gt;;

## Extensiones de PHP requeridas

Core,date,gd,imap,ldap,pcre,session,standard,xml

## Problemas conocidos

Si utlizamos XAMPP/WAMP bajo Windows para implementar el sitio Web, la autentificación utilizando LDAP devolverá en la mayoría de los casos el mensaje:

ldap_bind(): Unable to bind to server: Can't contact LDAP server

Para evitar este problema, hay que seguir las instrucciones que se detallan en [link](https://qadrio.wordpress.com/2012/03/14/ldap-ssl-connectionbind-with-self-signed-certificate-using-xamppwamp-on-windows/).