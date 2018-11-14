FROM nginx:1.15.3
ADD vhost.conf /etc/nginx/conf.d/default.conf
# Installation des dépendances – Let’s Encrypt
RUN apt-get update -q -y \
    && apt-get install -q -y --no-install-recommends \
        ca-certificates \
        python-certbot-nginx
