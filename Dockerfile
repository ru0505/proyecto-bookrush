# Imagen base de PHP con Apache
FROM php:8.2-apache

# Copiar el contenido del proyecto al directorio del servidor Apache
COPY . /var/www/html/

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Exponer el puerto 80
EXPOSE 80

# Iniciar el servidor Apache
CMD ["apache2-foreground"]
