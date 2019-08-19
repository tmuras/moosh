FROM jekyll/jakyll

ENV JEKYLL_VERSION=3.8

RUN mkdir -p /srv/jekyll

WORKDIR /srv/jekyll

COPY . .

RUN jekyll build --config _config.yml --destination ./dist/staging
