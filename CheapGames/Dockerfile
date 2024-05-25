FROM php:8.2-fpm

# Instala as dependências
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zlib1g-dev \
    mariadb-client \
    git \
    unzip \
 && docker-php-ext-install pdo pdo_mysql sockets zip

# Instala o composer
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copia os arquivos do projeto
COPY . .
COPY .env.example .env

# Define as variáveis de ambiente no Dockerfile
ARG DB_CONNECTION=mysql
ARG DB_HOST=cheapgames.czeqk0iimp5p.us-east-1.rds.amazonaws.com
ARG DB_PORT=3306
ARG DB_DATABASE=cheap_games
ARG DB_USERNAME=admin
ARG DB_PASSWORD=Cheapgamespiv
ARG APP_ENV=production

# Substitui as variáveis no arquivo .env pelas variáveis de ambiente
RUN sed -i "s#DB_HOST=.*#DB_HOST=${DB_HOST}#" .env \
    && sed -i "s#DB_PORT=.*#DB_PORT=${DB_PORT}#" .env \
    && sed -i "s#DB_DATABASE=.*#DB_DATABASE=${DB_DATABASE}#" .env \
    && sed -i "s#DB_USERNAME=.*#DB_USERNAME=${DB_USERNAME}#" .env \
    && sed -i "s#DB_PASSWORD=.*#DB_PASSWORD=${DB_PASSWORD}#" .env \
    && sed -i "s#APP_ENV=.*#APP_ENV=${APP_ENV}#" .env

RUN composer install
RUN php artisan route:cache && php artisan view:cache
RUN php artisan key:generate

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
