FROM alpine:3.14.0

RUN apk add php7 curl php7-json php7-phar php7-mbstring php7-openssl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

CMD ["tail", "-f", "/dev/null"]
