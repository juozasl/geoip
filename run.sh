#!/bin/bash

set -e

NGINX_REALIP_PROXY=${NGINX_REALIP_PROXY:-"172.17.0.1"}

# ++ create dirs

mkdir -p /var/www/log/

# ++ permissions

chown -R www-data:www-data /var/www

# initialize required files
service php7.0-fpm start
service php7.0-fpm stop

# ++ config real ip proxy
sed -i 's/set_real_ip_from 0.0.0.0;/set_real_ip_from '$NGINX_REALIP_PROXY';/' /etc/nginx/sites-available/default

# ++ run initial database download if not exsits
if [ ! -f /var/www/geoip/storage/app/GeoLite2-City.mmdb ]; then
    cd /var/www/geoip/ && ./artisan geoip:download
fi

# super visor deamons start
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
