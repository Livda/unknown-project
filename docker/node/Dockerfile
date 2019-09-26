FROM node:10-alpine

ARG UID=1000
ARG GID=1000

RUN apk add --update --no-cache shadow
RUN groupadd --gid $GID --non-unique symfony
RUN useradd --system --uid $UID --gid $GID --non-unique --no-create-home symfony
RUN apk del shadow
RUN rm -rf /var/cache/apk/*
RUN rm -rf /tmp/*

WORKDIR "/app"
EXPOSE 3000
