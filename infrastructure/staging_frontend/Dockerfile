# Créer l'image avec:         docker build . -t laravel_staging_frontend
# Démarrer le conteneur avec:
# docker kill laravel_staging_frontend
# docker run --rm --name laravel_staging_frontend -p 8200:4200 laravel_staging_frontend

FROM ubuntu:16.04
WORKDIR /opt/src/
RUN export DEBIAN_FRONTEND=noninteractive

RUN apt update

RUN apt -y install nano git curl python make

RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.6/install.sh | bash

RUN export NVM_DIR="$HOME/.nvm"; \
    [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"; \
    nvm install --lts=carbon; \
    npm install -g npm; \
    git clone https://gitlab.com/jbrassard/revolvairwebclient.git; \
    cd revolvairwebclient; \
    npm install;

CMD export NVM_DIR="$HOME/.nvm"; \
    [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"; \
    cd /opt/src/revolvairwebclient; \
    git pull; \
    npm install; \
    npm start