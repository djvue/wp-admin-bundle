# Symfony Wordpress Admin Bundle

## Gettings started

1. Add in `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://wpackagist.org",
            "only": [
                "wpackagist-plugin/*",
                "wpackagist-theme/*"
            ]
        },
        {
            "type": "composer",
            "url": "https://pivvenit.github.io/acf-composer-bridge/composer/v3/wordpress-plugin/"
        }
    ],
    "require": {
        "...": "...",
        "johnpbloch/wordpress": "^5.6",
        "advanced-custom-fields/advanced-custom-fields-pro": "5.9.5",
        "wpackagist-plugin/duplicate-post": "*",
        "wpackagist-plugin/post-types-order": "*",
        "wpackagist-plugin/regenerate-thumbnails": "*",
        "wpackagist-plugin/svg-support": "*",
        "wpackagist-plugin/taxonomy-terms-order": "*",
        "wpackagist-plugin/wordpress-seo": "*"
    },
    "scripts": {
        "auto-scripts": {
            "...": "...",
            "wp:clear-installation": "symfony-cmd"
        },
        "post-install-cmd": [
            "...",
            "@auto-scripts"
        ],
        "post-update-cmd": [
            "...",
            "@auto-scripts"
        ]
    },
    "extra": {
        "...": "...",
        "wordpress-install-dir": "public/wp",
        "installer-paths": {
            "public/content/plugins/{$name}/": [
                "vendor:wpackagist-plugin",
                "type:wordpress-plugin"
            ]
        }
    }
}
```

2. Add in symfony routes config:

```yaml
wp-admin:
    resource: "@WpAdminBundle/Resources/config/routes.yaml"
```

3. Setup nginx config:

```text
server {
    listen 80;
    server_name _;

    set $php_upstream unix:/var/run/php/php-fpm.sock;
    set $root /var/www/public;

    # uncomment if use local files uploads (not S3 for example)
    #location /content/uploads {
    #    expires max;
    #    root $root;
    #    try_files $uri 404;
    #}

    # symfony bundles static
    location /bundles {
        root $root;
        try_files $uri 404;
    }

    # symfony routes
    location ~ ^/(api/.+|wp/wp-admin/?|robots\.txt|sitemap(-\d+)?\.xml|wp-json/.+)$ {
        fastcgi_pass $php_upstream;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_SELF /var/www/public/index.php;
        fastcgi_param SCRIPT_NAME /var/www/public/index.php;
        fastcgi_param SCRIPT_FILENAME /var/www/public/index.php;
    }

    # admin static
    location ~ ^/(content/plugins|wp/wp-admin/css|wp/wp-admin/js|wp/wp-admin/images|wp/wp-includes/css|wp/wp-includes/js|wp/wp-includes/images|wp/wp-includes/fonts) {
        expires 14d;
        root $root;
        try_files $uri 404;
    }

    location ~ ^/(wp/wp-login\.php|wp/wp-admin/.+\.php)$ {
        root $root;
        fastcgi_pass $php_upstream;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        #fastcgi_param SCRIPT_FILENAME /var/www/public/index.php;
    }

    location / {
        # ...
    }
}
```

## Local developing

1. Example of local docker-compose.yaml file

```yaml
version: "3.8"

volumes:
  php_socket:
  mysql_data:

services:
  php-fpm:
    image: ggpa/php:8.1.2-wp-debug
    restart: unless-stopped
    environment:
      PHP_IDE_CONFIG: $PHP_IDE_CONFIG
      XDEBUG_MODE: debug
      #XDEBUG_MODE: debug,profile
      #XDEBUG_CONFIG: output_dir=/var/www/var/log
      ACF_PRO_KEY: $ACF_PRO_KEY
    volumes:
      - ./:/var/www
      - php_socket:/var/run/php
      #- ./var/uploads:/var/www/public/content/uploads
      - ./deploy/dev/php-fpm.conf:/usr/local/etc/php-fpm.d/zz-docker.conf
    depends_on:
      - mysql
    extra_hosts:
      - "dockerhost:172.17.0.1"

  nginx:
    image: nginx:alpine
    restart: unless-stopped
    ports:
      - "YOUR_APP_PORT:80"
    volumes:
      - php_socket:/var/run/php
      - ./:/var/www:ro
      #- ./var/uploads:/var/www/public/content/uploads
      - ./deploy/dev/nginx.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - php-fpm
    extra_hosts:
      - "dockerhost:172.17.0.1"

  mysql:
    image: mysql:8.0
    restart: unless-stopped
    command: --default-authentication-plugin=mysql_native_password --secure-file-priv=NULL --default-time-zone='+03:00' --general_log=0 --performance_schema=0
    environment:
      MYSQL_DATABASE: app
      MYSQL_ALLOW_EMPTY_PASSWORD: 1
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "YOUR_MYSQL_PORT:3306"

```

2. And local php-fpm.conf
```text
[global]
daemonize = no
process_control_timeout = 20

[www]
listen = /var/run/php/php-fpm.sock
listen.mode = 0666
ping.path = /ping

access.log = /dev/null
```

3. .env
```dotenv
COMPOSE_PROJECT_NAME=YOUR_APP_NAME
PHP_IDE_CONFIG="serverName=YOUR_APP_SERVER"

HOST=http://localhost:YOUR_APP_PORT

DB_HOST=mysql
DB_PORT=3306
DB_NAME=app
DB_USER=root
DB_PASSWORD=
```
