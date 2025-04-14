FROM php:8.3-fpm
LABEL org.opencontainers.image.authors="jakob.juenger@uni-muenster.de"

WORKDIR /var/www/html

# Update apt-get
RUN apt-get update

# Timezone
ENV TZ Europe/Berlin

# Install required components
RUN apt-cache search libjpeg
RUN apt-get install -y --no-install-recommends \
    libmcrypt-dev \
    g++ \
    libicu-dev \
    libmcrypt4 \
    zlib1g-dev \
    git \
    libxml2-dev \
    openssh-client \
    default-mysql-client \
    libjpeg62-turbo \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libjpeg-dev \
    libpq-dev \
    libmcrypt-dev \
    libpng16-16 \
    icu-devtools \
    libxslt1-dev \
    libonig-dev \
    graphicsmagick \
    zip \
    unzip \
    librsvg2-bin \
    nano \
    exiftool

# Install redis
RUN apt-get update && apt-get install -y --no-install-recommends \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype;
RUN	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		pdo_mysql \
		zip \
		intl \
        soap \
        xsl

# Install Gmagick
RUN apt-get update \
    && apt-get install -y --force-yes --no-install-recommends \
    libgraphicsmagick1-dev \
    libpng16-16 \
    libjpeg-dev \
    subversion

RUN pecl install gmagick-2.0.6RC1 \
    && docker-php-ext-enable gmagick \
    && rm -rf /tmp/pear

# Install ImageMagick
#RUN apt-get update && apt-get install -y \
#    imagemagick libmagickwand-dev --no-install-recommends \
#    && pecl install imagick \
#    && docker-php-ext-enable imagick

# Install composer globally
RUN curl -sSL https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Install node.js / npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash \
  && apt-get install -y nodejs

# Install Saxon/c dependencies
RUN mkdir -p /usr/share/man/man1
RUN apt-get update && apt-get install -y --no-install-recommends \
    openjdk-17-jre-headless libxml-commons-resolver1.1-java

# Install Saxon/c
WORKDIR /opt
RUN mkdir -p /opt/saxon
RUN curl https://downloads.saxonica.com/SaxonC/HE/12/libsaxon-HEC-linux-x86_64-v12.5.0.zip --output saxon.zip
#COPY docker/saxon.zip /opt
RUN unzip saxon.zip -d saxon

WORKDIR /opt/saxon
RUN cp libsaxon-HEC-linux-amd64-v12.5.0/libs/nix/libsaxon-hec-12.5.0.so /usr/lib/.

# Install Saxon/c PHP extension
ARG jvm='/usr/lib/jvm/java-11-openjdk-amd64'
WORKDIR /opt/saxon/libsaxon-HEC-linux-amd64-v12.5.0/Saxon.C.API
RUN phpize
RUN ./configure CPPFLAGS="-I${jvm}/include -I${jvm}/include/linux"
RUN make
RUN make install

# Enable Saxon/c PHP extension
# RUN echo 'extension=saxon.so' > "$PHP_INI_DIR/conf.d/saxon.ini"
RUN docker-php-ext-enable saxon

# Install SSL: the ssl-cert package will create a "snakeoil" keypair
WORKDIR /var/www/html
RUN apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y ssl-cert

# Install SSL: enable ssl module and enable the default-ssl site
# RUN a2enmod ssl \
# && a2ensite default-ssl


# Cleanup after the installation
#RUN apt-get purge --auto-remove -y \
#    libmcrypt-dev \
#    g++ \
#    libicu-dev \
#    zlib1g-dev \
#    libfreetype6-dev \
#    libjpeg62-turbo-dev \
#    libpng-dev \
#    libzip-dev \
#    libjpeg-dev \
#    libpq-dev \
#    libmcrypt-dev

# Delete apt-get lists as the take up space
RUN rm -rf /var/lib/apt/lists/*

# Enable apache rewrite
# RUN a2enmod rewrite

# Set www permissions
RUN usermod -u 1000 www-data

# Copy custom php.ini
COPY docker/deploy/php.ini /usr/local/etc/php/

# Copy epigraf src
COPY . /var/www/html/

RUN rm -rf /var/www/html/.git/*
RUN rm -rf /var/www/html/.gitlab/*
RUN rm -rf /var/www/html/.idea/*
RUN rm -rf /var/www/html/docker/*

RUN rm -rf /var/www/html/tests/*
RUN rm -rf /var/www/html/plugins/Epi/tests/*
RUN rm -rf /var/www/html/plugins/Files/tests/*
RUN rm -rf /var/www/html/plugins/Rest/tests/*
RUN rm -rf /var/www/html/plugins/Widgets/tests/*

RUN rm -rf /var/www/html/vendor/*
RUN rm -rf /var/www/html/node_modules/*
RUN rm -rf /var/www/html/tmp/*
RUN rm -rf /var/www/html/logs/*
RUN rm -rf /var/www/html/docs/*

RUN rm -f config/app.php

# Init data folder
#COPY data/ /var/www/data/
RUN mkdir -p /var/www/data/

# Install epigraf with the deploy configuration
COPY config/app.deploy.php /var/www/html/config/app.default.php
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev

RUN bin/cake cache clear_all

RUN chown -R www-data /var/www/html
RUN chown -R www-data /var/www/data

# Link logs folder into data folder
# RUN ln -s /var/www/html/logs /var/www/data/logs
