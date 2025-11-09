FROM node:20-alpine AS assets-build
WORKDIR /var/www/html
COPY . /var/www/html/

RUN apk add --no-cache php php-cli php-dom php-intl php-session php-fileinfo php-tokenizer php-xml php-mbstring php-xmlreader php-gd php-simplexml php-xmlwriter composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

RUN npm ci
RUN npm run build

FROM nginx:1.19-alpine AS nginx
COPY /docker/vhost.conf /etc/nginx/conf.d/default.conf
COPY --from=assets-build /var/www/html/public /var/www/html/