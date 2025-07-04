# RAIL-TWIN

## Instalación
`````
git clone https://github.com/AleOspina15/RAIL-TWIN.git

cd rail-twin
`````
### Configuración de variables de entorno
````
cp .env.example .env
nano .env

`````

Modificar las variables de entorno con la IP del servidor:
````
APP_URL=http://<IP_SERVIDOR>
APP_HOST=<IP_SERVIDOR>
GS_URL=http://<IP_SERVIDOR>:8080/geoserver
````
Modificar las variables de acceso a la base de datos:
````
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=aicedronesdi
DB_USERNAME=postgres
DB_PASSWORD="railtwin#pass"
````
### Despliegue
````
docker compose up -d
````
### Crear alias para el contenedor ````aicedrone-sdi-php-1````
````
alias dra="docker exec -it rail-twin-php-1"
````
### Instalar paquetes de Laravel
````
dra composer install
````
### Asignar permisos para la caché del framework
````
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
sudo chown -R www-data:www-data docker/php/aicedronesdi_filemanager
sudo chown -R www-data:www-data docker/geoserver/data
sudo chmod -R 777 docker/geoserver/data
sudo chmod 666 /var/run/docker.sock
sudo chmod -R 777 public/descargas
sudo chmod -R 777 public/potree
````
### Importar estructura de base de datos
````
dra php artisan restore:db
````
### Generación de clave Laravel y limpieza de cache
````
dra php artisan key:generate
dra php artisan optimize:clear
````
### Instalación de dependencias Nodejs
````
dra npm install
dra npm run dev
````
### Credenciales de acceso
Email: admin@admin.com
Password: admin@admin.com
