# Image sur dockerhub: demers/jenkins_docker
# Construire l'image avec:    docker build . -t laravel_jenkins
# Démarrer le conteneur avec: docker run -itd --name laravel_jenkins -p 8080:8080 -v /var/run/docker.sock:/var/run/docker.sock -v ./jenkins_home:/var/jenkins_home laravel_jenkins

# Inspiré de https://tripdubroot.com/jenkins-docker-in-docker-dind-2040cc90eeab
# Version Jenkins LTS

FROM jenkins/jenkins:lts

# Installation via le compte root
USER root

# Mise à jour du système
RUN apt-get update

# Préalable pour installer Docker
RUN apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg2 \
    software-properties-common

# Docker repos
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add -

RUN add-apt-repository \
    "deb [arch=amd64] https://download.docker.com/linux/debian \
     $(lsb_release -cs) \
     stable"

RUN apt-get update

# Installation de Docker
RUN apt-get -y install docker-ce

# Installation docker-compose 1.17
RUN curl -L https://github.com/docker/compose/releases/download/1.17.0/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose \
    && chmod +x /usr/local/bin/docker-compose

# Donner les droits d'utiliser Docker à Jenkins
RUN usermod -aG docker jenkins