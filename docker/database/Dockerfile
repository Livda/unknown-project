FROM mariadb:10.4

ARG UID=1000
ARG GID=1000

RUN addgroup --gid $GID symfony \
    && adduser --system --uid $UID --gid $GID --no-create-home --disabled-password --disabled-login symfony

EXPOSE 3306