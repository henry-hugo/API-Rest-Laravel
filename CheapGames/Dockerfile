# Use a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Atualize a lista de pacotes e instale as dependências necessárias
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install zip

# Instale as extensões do PHP necessárias para o Laravel
RUN docker-php-ext-install pdo_mysql

# Ative o mod_rewrite do Apache para o Laravel
RUN a2enmod rewrite

# Copie o projeto Laravel para o contêiner
COPY . /var/www/html/

# Dê permissão ao diretório storage
RUN chown -R www-data:www-data /var/www/html/storage

# Instale o Composer no contêiner
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Remova o diretório do fornecedor existente
RUN rm -rf vendor/

# Atualize o arquivo composer.lock
RUN composer update --lock

# Execute o Composer Install para instalar as dependências do Laravel
RUN composer install

# Crie ou edite o arquivo .env
RUN echo "DB_CONNECTION=mysql\n\
DB_HOST=cheapgames.czeqk0iimp5p.us-east-1.rds.amazonaws.com\n\
DB_PORT=3306\n\
DB_DATABASE=cheap_games\n\
DB_USERNAME=admin\n\
DB_PASSWORD=edit" > /var/www/html/.env

RUN php artisan key:generate

# Exponha a porta 80
EXPOSE 80

# Inicie o servidor Apache
CMD ["apache2-foreground"]
