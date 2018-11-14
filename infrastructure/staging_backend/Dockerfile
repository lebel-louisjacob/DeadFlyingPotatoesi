# Docker file staging
#
# docker build -t laravel_staging .
# docker exec laravel_staging restart
# 

FROM ubuntu:16.04
WORKDIR /opt
RUN export DEBIAN_FRONTEND=noninteractive

# Installation des logiciels nécessaires
RUN apt-get update
RUN apt-get install -y software-properties-common python3-software-properties git wget locales lsof

# Ajouter le repository de PHP
RUN locale-gen en_US.UTF-8
RUN LANG=en_US.UTF-8 add-apt-repository -y ppa:ondrej/php
RUN apt-get update

# Installer php 7.2 et ses extensions nécessaires
RUN apt-get install -y php7.2 php7.2-common php7.2-cli php7.2-fpm
RUN apt-get install -y php7.2-xml php7.2-mbstring php7.2-zip php7.2-mysql
RUN wget https://getcomposer.org/download/1.6.2/composer.phar


# Cloner le repo
RUN git clone https://gitlab.com/jbrassard/revolvairapi.git
WORKDIR /opt/revolvairapi
RUN php ../composer.phar install
RUN php ../composer.phar update --no-scripts

# Configurer les scripts bash qui démarrent/arrêtent le serveur
RUN echo '#!/bin/bash' > /opt/runserver.sh
RUN echo 'cd /opt/revolvairapi' >> /opt/runserver.sh
RUN echo 'while true' >> /opt/runserver.sh
RUN echo 'do' >> /opt/runserver.sh
RUN echo 'git reset --hard' >> /opt/runserver.sh
RUN echo 'git pull' >> /opt/runserver.sh
RUN echo 'cat .env.staging > .env' >> /opt/runserver.sh
RUN echo 'php artisan migrate:fresh --seed' >> /opt/runserver.sh
RUN echo 'php artisan passport:install' >> /opt/runserver.sh
RUN echo 'php artisan passport:client --secret' >> /opt/runserver.sh
RUN echo 'php ../composer.phar install' >> /opt/runserver.sh
RUN echo 'php ../composer.phar update --no-scripts' >> /opt/runserver.sh
RUN echo 'php artisan serve --host=0.0.0.0' >> /opt/runserver.sh
RUN echo 'done' >> /opt/runserver.sh

RUN echo '#!/bin/bash' > /bin/restart
RUN echo 'kill $(lsof -t -i:8000)' >> /bin/restart

RUN chmod +x /opt/runserver.sh /bin/restart

# Point d'entrée de l'image
CMD /opt/runserver.sh