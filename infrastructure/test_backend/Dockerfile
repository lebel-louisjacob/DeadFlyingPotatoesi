# docker build . -t laravel_test
# docker run -e BRANCH=... laravel_test

FROM ubuntu:16.04
WORKDIR /opt/

# Installation des logiciels nécessaires
RUN apt-get update
RUN apt-get install -y software-properties-common python3-software-properties git wget locales 

# Configurer le serveur MySQL local
RUN export DEBIAN_FRONTEND=noninteractive && apt-get install -q -y mysql-server

RUN chown -R mysql: /var/lib/mysql && \
    service mysql start && \
    mysql -u root -e "CREATE DATABASE revolvair" && \
    mysql -u root -e "GRANT ALL PRIVILEGES ON revolvair.* TO 'test'@'localhost' IDENTIFIED BY 'test'"

# Ajouter le repository de PHP
RUN locale-gen en_US.UTF-8
RUN LANG=en_US.UTF-8 add-apt-repository -y ppa:ondrej/php
RUN apt-get update

# Installer php 7.2 et ses extensions nécessaires
RUN apt-get install -y php7.2 php7.2-common php7.2-cli php7.2-fpm 
RUN apt-get install -y php7.2-xml php7.2-mbstring php7.2-zip php7.2-mysql phpunit
RUN wget https://getcomposer.org/download/1.6.2/composer.phar
RUN wget https://phar.phpunit.de/phpunit-6.phar

# Cloner le repo
RUN git clone https://gitlab.com/jbrassard/revolvairapi.git
WORKDIR /opt/revolvairapi
RUN php ../composer.phar install
RUN php ../composer.phar update --no-scripts

# Configurer le script bash qui roule les tests
RUN echo '#!/bin/bash' > /opt/runtests.sh
RUN echo 'chown -R mysql: /var/lib/mysql && service mysql start' >> /opt/runtests.sh

RUN echo 'git reset --hard' >> /opt/runtests.sh
RUN echo 'git pull' >> /opt/runtests.sh
RUN echo 'git checkout $BRANCH' >> /opt/runtests.sh
RUN echo 'cat .env.testing > .env' >> /opt/runtests.sh
RUN echo 'php ../composer.phar install' >> /opt/runtests.sh
RUN echo 'php ../composer.phar update --no-scripts' >> /opt/runtests.sh
RUN echo 'php artisan migrate' >> /opt/runtests.sh
RUN echo 'php artisan passport:install' >> /opt/runtests.sh
RUN echo 'php artisan passport:client --secret' >> /opt/runtests.sh
RUN echo 'php ../phpunit-6.phar' >> /opt/runtests.sh

RUN chmod +x /opt/runtests.sh

WORKDIR /opt/revolvairapi

CMD /opt/runtests.sh