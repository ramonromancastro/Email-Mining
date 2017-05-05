# Email-Mining

## Descripción

Plataforma Web (PHP/MySQL) para la generación de informes mediante minería de correo electrónico

## Extesiones de PHP requeridas

Core,date,gd,imap,ldap,pcre,session,standard,xml

## Problemas conocidos

Si utlizamos XAMPP/WAMP bajo Windows para implementar el sitio Web, la autentificación utilizando LDAP devolverá en la mayoría de los casos el mensaje:

ldap_bind(): Unable to bind to server: Can't contact LDAP server

Para evitar este problema, hay que seguir las instrucciones que se detallan en [link](https://qadrio.wordpress.com/2012/03/14/ldap-ssl-connectionbind-with-self-signed-certificate-using-xamppwamp-on-windows/).