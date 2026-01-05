FROM php:8.3-apache
LABEL org.opencontainers.image.authors="jakob.juenger@adwmainz.de"

WORKDIR /var/www/html

# Update apt-get
RUN apt-get update

# Timezone
ENV TZ=Europe/Berlin

# Gecko driver
ENV GECKODRIVER_VERSION=0.30.0

# Install required components
RUN apt-cache search libjpeg

RUN apt-get update \
    && apt-get install -y --force-yes --no-install-recommends \
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

# Install xdiff
RUN curl -O http://www.xmailserver.org/libxdiff-0.23.tar.gz \
    && tar -xzf libxdiff-0.23.tar.gz \
    && cd libxdiff-0.23/ \
    && ./configure \
    && make \
    && make install

RUN pecl install xdiff-2.1.1 && docker-php-ext-enable xdiff

# Install Gmagick
#RUN apt-get update \
#    && apt-get install -y --force-yes --no-install-recommends \
#    libgraphicsmagick1-dev \
#    libpng16-16 \
#    libjpeg-dev \
#    subversion

#RUN pecl install gmagick-2.0.6RC1 \
#    && docker-php-ext-enable gmagick \
#    && rm -rf /tmp/pear

# Install ImageMagick
RUN apt-get update && apt-get install -y --no-install-recommends \
    imagemagick libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install composer globally
RUN curl -sSL https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Install node.js / npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash \
  && apt-get install -y nodejs

# Install xdebug for code coverage
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Install Saxon/c dependencies
ARG jdk='openjdk-21-jre-headless'
RUN mkdir -p /usr/share/man/man1
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    ${jdk} unzip libxml-commons-resolver1.1-java

# Install Saxon/c
WORKDIR /opt
RUN mkdir -p /opt/saxon
RUN curl https://downloads.saxonica.com/SaxonC/HE/12/libsaxon-HEC-linux-x86_64-v12.5.0.zip --output saxon.zip
#COPY ./docker/saxon.zip /opt
RUN unzip saxon.zip -d saxon

WORKDIR /opt/saxon
RUN cp libsaxon-HEC-linux-amd64-v12.5.0/libs/nix/libsaxon-hec-12.5.0.so /usr/lib/.

#RUN chmod +x saxon/${saxon}
#RUN saxon/${saxon} -batch -dest /opt/saxon

RUN ln -s /opt/saxon/libsaxonhec.so /usr/lib/
RUN ln -s /opt/saxon/rt /usr/lib/

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

# Install Chrome and Firefox browser
# TODO fix the FF version to 102.11.0esr-1~deb11u1. Not working anymore.
WORKDIR /var/tmp
RUN apt-get update && apt-get install -y \
    wget \
    firefox-esr \
    xvfb

# Install Selenium drivers
RUN curl -sSL https://github.com/mozilla/geckodriver/releases/download/v$GECKODRIVER_VERSION/geckodriver-v$GECKODRIVER_VERSION-linux64.tar.gz -o download_geckodriver.tar.gz && \
    tar -x geckodriver -zf download_geckodriver.tar.gz -O > /usr/bin/geckodriver && \
    chmod +x /usr/bin/geckodriver && \
    rm download_geckodriver.tar.gz

ENV SELENIUM_DOWNLOAD_URL=https://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar
RUN curl -sSL $SELENIUM_DOWNLOAD_URL -o /tmp/selenium-server-standalone.jar \
    && mkdir -p /opt/selenium/ \
    && mv /tmp/selenium-server-standalone.jar /opt/selenium/selenium-server-standalone.jar

# Install phpDoc
# RUN wget https://phpdoc.org/phpDocumentor.phar
WORKDIR /var/tmp
RUN curl -LO https://phpdoc.org/phpDocumentor.phar
RUN chmod +x phpDocumentor.phar
RUN mv phpDocumentor.phar /usr/local/bin/phpDocumentor

# Install jsDoc
WORKDIR /var/www/html
RUN npm install -g jsdoc

# Install SSL: the ssl-cert package will create a "snakeoil" keypair
WORKDIR /var/www/html
RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get install -y ssl-cert

# Install SSL: enable ssl module and enable the default-ssl site
RUN a2enmod ssl \
 && a2ensite default-ssl

# Enable apache rewrite
RUN a2enmod rewrite

# Cleanup after the installation
# Delete apt-get lists as the take up space
RUN apt-get clean && \
  rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Align host and container user ID
#RUN usermod -u 1000 www-data

RUN mkdir /tmp/cache && mkdir /tmp/cache/models && mkdir /tmp/cache/persistent && chown -R www-data:www-data /tmp/cache
RUN mkdir /tmp/sessions && chown -R www-data:www-data /tmp/cache

# Copy custom php.ini
COPY ./php.ini /usr/local/etc/php/
