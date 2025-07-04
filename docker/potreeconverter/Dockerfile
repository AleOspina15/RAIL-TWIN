FROM alpine:3.17

ARG VERSION=latest
LABEL Name="potree-converter ${VERSION}"
LABEL maintainer="infeeeee"
WORKDIR /potreebuild
RUN apk add --no-cache git cmake make gcc g++ libtbb-dev
RUN git clone --depth 1 --branch $VERSION https://github.com/potree/PotreeConverter /PotreeConverter || \
    git clone --depth 1 https://github.com/potree/PotreeConverter /PotreeConverter && \
    mkdir -p /data/input /data/output && \
    cmake /PotreeConverter && make && \
    apk del git cmake make gcc g++ && \
    rm -rf /PotreeConverter
COPY ./entrypoint.sh ./
ENV POTREE_ENCODING=UNCOMPRESSED \
    POTREE_METHOD=poisson \
    POTREE_EXTRA_OPTIONS="" \
    OVERWRITE=TRUE \
    REMOVE_INDEX=TRUE
RUN chmod +x ./entrypoint.sh
ENTRYPOINT exec ./entrypoint.sh
