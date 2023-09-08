
FROM php:8.2-cli

# Create app directory
WORKDIR /app

# Copy files
COPY . /app

# Install system dependencies
RUN apt-get update && apt-get install -y git

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install

# Start the app
CMD [ "php", "index.php" ]
