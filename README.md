The Webkey Privacy
-------------------
The Webkey Privacy is an application for managing OpenPGP keys within organizations

## Features
- Centrally creating & certifing new personal keys
- Revoking personal key
- Publish certificates as a REST API

### System Requirements
* Web server with URL rewriting
* PHP 8.1.x or later with extensions: Ctype, cURL, DOM, Fileinfo, Filter, Hash,
Mbstring, MySQL native driver, OpenSSL, PCRE, PDO, PDO-MySQL, Session, Tokenizer, XML
* Database server: MariaDB 10.10+ or MySQL 5.7+ or PostgreSQL 11.0+ or SQL Server 2017+

## Deployment
When you're ready to deploy your Webkey Privacy application to production,
there are some important things you can do to make sure your
application is running as efficiently as possible.
In this document, we'll cover some great starting points
for making sure your Laravel application is deployed properly.

### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /srv/example.com/public;
 
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
 
    index index.php;
 
    charset utf-8;
 
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
 
    error_page 404 /index.php;
 
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
 
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Licensing
[GNU Affero General Public License v3.0](LICENSE)

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
