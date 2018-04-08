
FROM juozasl/docker:phpapp

# ++ install packages

RUN apt update && apt install -y \
    git \
    zip

# ++ copy api folder
ADD api /var/www/geoip
ADD files/env /var/www/geoip/.env

# ++ install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ++ composer update
RUN cd /var/www/geoip/ && composer update

#  ++ install cron
RUN apt update && apt install -y cron run-one
ADD files/updatecron /etc/cron.d/app
RUN chmod 0644 /etc/cron.d/app
ADD files/cron.conf /etc/supervisor/conf.d/cron.conf

# ++ add nginx default config
ADD files/nginx.default /etc/nginx/sites-available/default




WORKDIR /var/www/geoip

# ++ entry script
ADD run.sh /run.sh
ENTRYPOINT ["/run.sh"]