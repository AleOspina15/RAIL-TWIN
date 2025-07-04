FROM php:8.1-fpm
RUN apt-get update
RUN apt-get install -y gnupg curl wget ca-certificates unzip lsb-release
RUN wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ `lsb_release -cs`-pgdg main" | tee  /etc/apt/sources.list.d/pgdg.list
RUN apt-get install -y libicu-dev libpq-dev libzip-dev
RUN apt-get install -y nodejs
RUN apt-get install -y postgresql-client postgis
RUN apt-get install -y zip cron
RUN apt-get install -y default-jre default-jdk gdal-bin libgdal-dev
RUN apt-get install -y python3 python3-pip
RUN apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libfreetype6-dev zlib1g-dev libgd-dev libjpeg-dev
RUN pecl install redis
RUN docker-php-ext-enable redis
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install intl pdo pdo_pgsql pgsql zip bcmath pcntl exif soap
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN apt-get install -y npm
RUN apt-get install -y p7zip-full
RUN curl -fsSL https://get.docker.com | sh
RUN echo 'max_execution_time = 0' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
RUN echo 'default_socket_timeout = 18000' >> /usr/local/etc/php/conf.d/docker-php-defaultsockettimeout.ini;
RUN echo 'max_input_vars = 50000' >> /usr/local/etc/php/conf.d/docker-php-maxinputvars.ini;
RUN echo 'memory_limit = 8192M' >> /usr/local/etc/php/conf.d/docker-php-memorylimit.ini;
RUN echo 'upload_max_filesize = 4096M' >> /usr/local/etc/php/conf.d/docker-php-uploadmaxfilesize.ini;
RUN echo 'post_max_size = 4096M' >> /usr/local/etc/php/conf.d/docker-php-postmaxsize.ini;
RUN apt-get update
RUN apt-get -y autoremove
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN chown -R www-data:www-data /var/www
RUN touch /var/www/schedule.log
RUN chmod 0777 /var/www/schedule.log
ADD scheduler /etc/cron.d/scheduler
RUN crontab /etc/cron.d/scheduler
COPY ./entrypoint.sh /
RUN chmod +x /entrypoint.sh
ENTRYPOINT /entrypoint.sh
WORKDIR /var/www/app
USER root