# Linux, Nginx, PHP-FPM Deployment

Target stack:

- Ubuntu 24.04 LTS or similar Linux host
- Nginx
- PHP 8.4 FPM
- Composer 2
- Node 24
- Queue worker managed by systemd or Supervisor
- Scheduler via cron or systemd timer

Required PHP extensions:

```text
ctype curl dom exif fileinfo gd iconv mbstring openssl pdo pdo_mysql pdo_sqlite tokenizer xml zip
```

Typical release steps:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Point Nginx at `public/`. A sample server block is available at `deploy/nginx/erp.conf`.

Background workers:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
php artisan pulse:check
```

Scheduler:

```cron
* * * * * cd /var/www/erp/current && php artisan schedule:run >> /dev/null 2>&1
```
