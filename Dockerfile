FROM dunglas/frankenphp:php8.4-bookworm

RUN install-php-extensions mysqli pdo_mysql \
    && apt-get remove -y apache2 apache2-utils libapache2-mod-php* 2>/dev/null || true \
    && apt-get autoremove -y \
    && apt-get clean

WORKDIR /app
COPY . /app

ENV SERVER_NAME=":8080"

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]