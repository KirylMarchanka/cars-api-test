# How to start
1. Clone this repo
2. Copy .env.example to .env
3. Run `composer install`
4. Run `php artisan key:generate`
5. Run `docker-compose up -d`
6. Run `docker-compose exec app php artisan migrate --seed`
7. Test application, use `http://127.0.0.1/api`

Test user: test.acc@example.net / password
