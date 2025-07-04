#!/bin/bash

/usr/sbin/cron -f &
docker-php-entrypoint php-fpm
