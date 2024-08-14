# Quiz Central

#### Utilizing the power of the Laravel 11 prompt package to create a command line based quiz application with a clean and beautiful user-friendly form based interface similar to the web.
#### The Trivia API (https://opentdb.com/api_config.php) is used as the datasource for configuring and providing quiz data. Users can modify each setting.

## About

### Running the application presents the user with some initial set up instructions:

1. Limit on the amount of questions (1-10)
2. A category (Entertainment, Science & Nature or History)
3. A difficulty level (Easy, Medium or Hard)
4. Type of answers (True/False or Multiple)
5. You can go back to a previously answered question by pressing control + u (Mac)

## Dependencies

1. PHP 8.2
2. Composer
3. Command Line (Mac, Linux or Windows WSL2)

## Starting the application
1. Open your terminal (Mac, Linux or Windows WSL2)
2. Change directory to the project folder

```
Run composer install
```

```
Run touch database/database.sqlite

If you are on a windows pc you should create the database.sqlite file in the database directory similar to the above command.
```

```
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

```
Run php artisan migrate --seed
Run php artisan app:quiz-start
```
### Have Fun!!!
