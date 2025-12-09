# Simple PHP + Apache image to serve the schedule aggregation demo
FROM php:8.2-apache

# Copy application files into the Apache document root
COPY index.php /var/www/html/index.php

# Configure Apache to serve the app from the document root
WORKDIR /var/www/html
