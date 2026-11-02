# syntax=docker/dockerfile:1.7
ARG PHP_VERSION=8.3
ARG NODE_MAJOR=20
ARG SAXON_VERSION=12.5.0

# ============================================================ builder =========
FROM php:${PHP_VERSION}-apache-bookworm AS builder
ARG NODE_MAJOR
ARG SAXON_VERSION
ENV DEBIAN_FRONTEND=noninteractive COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_INTERACTION=1

RUN set -eux; apt-get update; apt-get install -y --no-install-recommends \
      ca-certificates curl git unzip g++ make pkg-config \
      libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
      libxml2-dev libxslt1-dev libonig-dev libgraphicsmagick1-dev zlib1g-dev; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-configure gd --with-jpeg --with-freetype; \
    docker-php-ext-install -j"$(nproc)" gd opcache pdo_mysql zip intl soap xsl; \
    pecl install gmagick-2.0.6RC1; \
    docker-php-ext-enable gmagick; \
    rm -rf /tmp/pear

# SaxonC 12 ist ein GraalVM-Native-Image -> kein JDK/JRE noetig
RUN set -eux; cd /opt; \
    curl -fsSL -o s.zip "https://downloads.saxonica.com/SaxonC/HE/12/libsaxon-HEC-linux-x86_64-v${SAXON_VERSION}.zip"; \
    unzip -q s.zip; src="/opt/libsaxon-HEC-linux-amd64-v${SAXON_VERSION}"; \
    cp "${src}/libs/nix/libsaxon-hec-${SAXON_VERSION}.so" /usr/lib/; ldconfig; \
    cd "${src}/Saxon.C.API"; phpize; ./configure; make -j"$(nproc)"; make install; \
    docker-php-ext-enable saxon; cd /; rm -rf /opt/s.zip "${src}"

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN set -eux; curl -fsSL "https://deb.nodesource.com/setup_${NODE_MAJOR}.x" | bash -; \
    apt-get install -y --no-install-recommends nodejs; rm -rf /var/lib/apt/lists/*

WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-progress

COPY . .
RUN set -eux; \
    cp docker/standalone/app.php config/app.php; \
    rm -rf docker .git .gitlab .idea tests plugins/*/tests config/app.deploy.php; \
    composer dump-autoload --no-dev --optimize --classmap-authoritative; \
    if [ -f package.json ]; then npm ci --no-audit --no-fund; npm run build --if-present; rm -rf node_modules; fi; \
    mkdir -p tmp/cache logs /var/www/data; \
    chmod +x bin/cake

# ============================================================ runtime =========
FROM php:${PHP_VERSION}-apache-bookworm AS runtime
ENV DEBIAN_FRONTEND=noninteractive TZ=Europe/Berlin

RUN set -eux; apt-get update; apt-get install -y --no-install-recommends \
      ca-certificates curl tzdata \
      libicu72 libzip4 libpng16-16 libjpeg62-turbo libfreetype6 \
      libxslt1.1 libxml2 libonig5 libgraphicsmagick-q16-3 zlib1g \
      graphicsmagick librsvg2-bin libimage-exiftool-perl \
      default-mysql-client zip unzip; \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime; echo $TZ > /etc/timezone; \
    rm -rf /var/lib/apt/lists/*

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/     /usr/local/etc/php/conf.d/
COPY --from=builder /usr/lib/libsaxon-hec-*.so     /usr/lib/
RUN ldconfig

COPY docker/standalone/php.ini     /usr/local/etc/php/conf.d/zz-epigraf.ini
COPY docker/standalone/apache.conf /etc/apache2/sites-available/epigraf.conf
RUN set -eux; \
    a2enmod rewrite headers expires deflate; \
    a2dissite 000-default; \
    a2ensite epigraf; \
    # ServerTokens ist nur im globalen Kontext erlaubt -> Debians security.conf
    sed -i 's/^ServerTokens .*/ServerTokens Prod/' /etc/apache2/conf-available/security.conf; \
    # ServerName global setzen, unterdrueckt die AH00558-Warnung beim Start
    echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf; \
    a2enconf servername

COPY --from=builder --chown=www-data:www-data /build /var/www/html

# Remove CRLF if present in entrypoint.sh
COPY docker/standalone/entrypoint.sh /usr/local/bin/entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

# Local defaults (instead of .env)
ENV MYSQL_HOST=sql \
    MYSQL_PORT=3306 \
    MYSQL_DATABASE=epigraf \
    MYSQL_USER=epigraf \
    MYSQL_PASS=epigraf \
    MYSQL_ROOT_PASSWORD=root \
    SECURITY_SALT=local_development_salt_not_for_production_0123456789 \
    APP_FULL_BASE_URL=http://localhost:8080 \
    DEBUG=false \
    INIT_DATABASE=true \
    INIT_DEMO=true \
    DEMO_DATABASE=epi_movies \
    DEMO_PRESET=movies \
    ADMIN_USER=admin \
    ADMIN_PASS=admin

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=90s --retries=3 \
  CMD curl -fsS -o /dev/null http://127.0.0.1/ || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["apache2-foreground"]