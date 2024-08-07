# Quiz Central

#### Utilizing the power of the Laravel 11 prompt package to create a command line based quiz application with a clean and beautiful user-friendly form based interface similar to the web.

## Dependencies

1. PHP 8.2
2. Composer
3. Command Line (Mac, Linux or Windows WSL2)

## Starting the application
1. Open your terminal (Mac, Linux or Windows WSL2)
2. Change directory to the project folder

```php
Run composer install
```

```php
Run touch database/database.sqlite

If you are on a windows pc you should create the database.sqlite file in the database directory similar to the above command.
```

```php
Create a copy of the .env.example and rename it to .env

Run php artisan key:generate

Update your database .env variables to the following:

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

```php
Run php artisan migrate:fresh --seed
Run php artisan app:quiz-start
```
