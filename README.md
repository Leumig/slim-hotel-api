# 🏨 El Hotel - API con Sli

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

1. **Clonar el repositorio**:

    Clonar el repositorio con el siguiente comando, o descargar el ZIP.
   
    ```bash
    git clone https://github.com/miguecode/el-hotel-api.git

2. **Instalar dependencias**:

    Hay que tener Composter instalado, y después ejecutar este comando:

    ```bash
    composer install
    ```

3. **Configurar la base de datos [ADVERTENCIA]**

    - Hay que tener MySQL instalado.
    - Hay que tener una base de datos con la estructura específica que necesita el proyecto.

    ⚠️ **La advertencia cae en este último punto. El script para crearla no lo tengo por ahora...**

    - Editar el archivo .env con las credenciales de tu base de datos

      ```bash
      MYSQL_HOST=localhost
      MYSQL_PORT=3306
      MYSQL_USER=tu_usuario
      MYSQL_PASS=tu_contraseña
      MYSQL_DB=el_hotel
      ```

4. **Levantar el servidor**:

    Para correr el servidor PHP Slim en el puerto hay que ejecutar el comando:

      ```bash
      php -S localhost:777 -t app
      ```

5. **Importar las consultas de Postman**:

    El repositorio contiene un archivo .json llamado "El Hotel.postman_collection.json" (dentro de la carpeta 'postman'). Este archivo es el que hay que importar en Postman.


## 📌 Aclaraciones

- Fue creado en 2023, mientras cursaba la carrera de Tecnicatura Universitaria en Programación, en la Universidad Tecnológica Nacional.
- El proyecto está bajo la licencia MIT.

## 🗃️ Otros proyectos similares
- [La Comanda - API con Slim](https://github.com/miguecode/slim-comanda-api)
