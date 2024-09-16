# 🏨 El Hotel - API con Slim

Este proyecto es una API desarrollada con Slim, que es un micro-framework de PHP. Trabaja desde el lado del servidor comunicándose con una base de datos SQL.

## 📘 Descripción

El Hotel es una API cuya idea es trabajar la comunicación entre el cliente y el servidor, centrándose en la manipulación de las respuestas (del servidor). Todos los datos persisten en una base de datos SQL, precisamente con el motor MySQL. Además, cuenta con un sistema de logeo mediante JSON Web Token. La temática es la de un hotel, con la administración de distintos usuarios (clientes, gerentes, recepcionistas), y con reservas de habitaciones.

## 👨‍🚀 Consultas en Postman

Así se ve la colección de consultas realizadas en Postman (archivo importable en este repositorio)

<img src="https://github.com/user-attachments/assets/ce95b2e5-79f1-4bcc-81b6-6ef89accc7ec" width="320"/>

## 🛠️ Consultas posibles

- **GET**: Permite traer todos (o individualmente) los clientes, gerentes, recepcionistas, reservas y ajustes (de precios) del sistema.
- **POST**: Permite hacer altas de clientes, gerentes, recepcionistas, reservas y ajustes (de precios).
- **PUT**: Permite hacer modificaciones de clientes y reservas.
- **DELETE**: Permite hacer bajas de clientes y reservas.
- **Consultas de Reservas**: Con el verbo **GET**, las reservas tienen distintos tipos de consultas personalizadas para filtrarlas por distintos criterios.

## 🚀 Cómo Ejecutar el Proyecto



## 📌 Aclaraciones

- Fue creado en 2023, mientras cursaba la carrera de Tecnicatura Universitaria en Programación, en la Universidad Tecnológica Nacional.
- El proyecto está bajo la licencia MIT.

## 🗃️ Otros proyectos similares
- [La Comanda - API con Slim](https://github.com/Leumig/slim-comanda-api)
