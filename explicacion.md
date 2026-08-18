# Diseño y desarrollo de servicios web - caso. GA7-220501096-AA5-EV01.
## Jonathan Steven Arias Beltran
## 1019086621
## ANÁLISIS Y DESARROLLO DE SOFTWARE
## JORGE DAVID ORTIZ ROJAS

# PET-TIME API

Servicio web desarrollado para el proyecto **PET-TIME**, orientado inicialmente al registro y autenticación de usuarios.

Este desarrollo corresponde a la evidencia **GA7-220501096-AA5-EV01 – Diseño y desarrollo de servicios web - caso**.

---

## Objetivo

Construir una API en PHP que permita registrar usuarios e iniciar sesión mediante correo electrónico y contraseña, utilizando una base de datos MySQL/MariaDB.

La API se desarrollará de forma modular para posteriormente incorporar otros servicios del proyecto PET-TIME.

---

## Tecnologías

El proyecto utiliza herramientas de uso libre:

- PHP 8.2 para el desarrollo de los servicios.
- Apache como servidor web.
- MySQL/MariaDB para almacenar la información.
- phpMyAdmin para administrar la base de datos.
- Visual Studio Code como editor.
- Git para el control de versiones.
- GitHub como repositorio remoto.
- Postman para realizar pruebas de la API.

---

## Estructura inicial

El proyecto se encuentra ubicado en:

C:\xampp\htdocs\PET-TIME-API

## Servicio de registro

Se desarrolló el primer servicio funcional de la API para permitir el registro de usuarios.

El servicio utiliza el método `POST` y recibe los datos en formato JSON. Antes de almacenar la información se realizan validaciones para comprobar que los campos obligatorios estén completos, que el correo tenga un formato válido y que no exista previamente.

La contraseña no se almacena directamente. Se utiliza `password_hash()` para generar un hash antes de guardar la información en la base de datos.

También se implementó el manejo de respuestas HTTP. Por ejemplo, se utiliza `201 Created` cuando el usuario se registra correctamente y `409 Conflict` cuando el correo ya se encuentra registrado.

El servicio fue probado mediante Postman y se verificó que la información fuera almacenada correctamente en la tabla `usuarios` de la base de datos `pet_time_api`.