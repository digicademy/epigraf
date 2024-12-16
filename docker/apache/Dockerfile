# httpd base image
FROM httpd:2.4
# Copy the Apache virtual host configuration file to the container
COPY apache.vhost.conf /usr/local/apache2/conf/extra/apache.vhost.conf

# Enable Apache modules to ensure proper functionality
RUN sed -i \
   # Uncomment the configuration for mod_deflate to enable compression
    -e '/#LoadModule deflate_module/s/^#//g' \
    # Uncomment the configuration for mod_proxy to enable proxying capabilities
    -e '/#LoadModule proxy_module/s/^#//g' \
    # Uncomment the configuration for mod_proxy_fcgi to enable FastCGI proxy module
    -e '/#LoadModule proxy_fcgi_module/s/^#//g' \
    # Mod rewrite \
    -e '/#LoadModule rewrite_module/s/^#//g' \
    /usr/local/apache2/conf/httpd.conf

# Include the virtual host configuration file in the main Apache configuration
RUN echo "Include /usr/local/apache2/conf/extra/apache.vhost.conf" >> /usr/local/apache2/conf/httpd.conf

# Cleanup after the installation
# Delete apt-get lists as the take up space
RUN apt-get clean && \
  rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Set www permissions
RUN usermod -u 1000 www-data
