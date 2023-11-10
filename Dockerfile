FROM php:8.2-cli
COPY . /usr/src/fancontroller
WORKDIR /usr/src/fancontroller
CMD [ "php", "./bin/console", "fan:controller", "auto" ]
