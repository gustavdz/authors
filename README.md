# Athors & Books

## env values required
Los siguientes valores son necesarios tener en el archivo .env
#### Database
- DB_CONNECTION=mysql
- DB_HOST=<db_host>
- DB_PORT=3306
- DB_DATABASE=<db_name>
- DB_USERNAME=<db_user>
- DB_PASSWORD=<db_password>

#### APP

- APP_URL=http://localhost:8000
- APP_NAME="Authors & Books"
- APP_KEY=<<key_to_be_generated_later>>

#### Email

<p>para envio de verificación de email a usuarios nuevos que se registren via web (no API)</p>

- MAIL_DRIVER=smtp
- MAIL_HOST=<email_host>
- MAIL_PORT=<email_port>
- MAIL_USERNAME=<email_address>
- MAIL_PASSWORD=<email_password>
- MAIL_ENCRYPTION=<encryption_type>
- MAIL_FROM_ADDRESS=<email_address>
- MAIL_FROM_NAME="${APP_NAME}"

## Steps to install

Los pasos para instalar la aplicacion son los siguientes:

- Instalar dependencias: **composer install**
- Generar el key global: **php artisan key:generate**
- Crear la base de datos: **php artisan db:create**
- Crear las tablas y poblarlas: **php artisan migrate:fresh --seed**
- Instalar los keys de passport: **php artisan passport:install**

## Considering if db is not created

Si al crear la base de datos da error el comando correr los siguientes comandos antes
- php artisan config:cache
- php artisan config:clear
- php artisan cache:clear

## Postman Collection to test
El archivo de Postam Collection con los distintos endpoints es el siguiente:
- authors_books.postman_collection.json
