FROM alpine:3.10

RUN apk add --upgrade --no-cache \
    apache2 \
    bash \
    curl \
    composer \
    php7 \
    php7-apache2 \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-opcache \
    php7-pdo \
    php7-pdo_mysql \
    php7-tokenizer \
    php7-session \
    php7-simplexml \
    php7-sodium \
    php7-xml \
    vim

# Define timezone
RUN apk add --upgrade --no-cache tzdata && \
    cp /usr/share/zoneinfo/Europe/Paris /etc/localtime && \
    echo "Europe/Paris" > /etc/timezone && \
    echo "date.timezone=Europe/Paris" >> /etc/php7/php.ini && \
    apk del tzdata

# Clean apk tmp files
RUN rm -rf /var/cache/apk/* /tmp/*

COPY bin/* /usr/local/bin/
RUN chmod +x /usr/local/bin/*

WORKDIR /var/www/html
EXPOSE 80
CMD ["apache2-foreground"]
