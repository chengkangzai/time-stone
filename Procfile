web: vendor/bin/heroku-php-nginx -C nginx_app.conf /public
queue: php artisan queue:work --sleep=3 --tries=3
schedule: php artisan schedule:work
