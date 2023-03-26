# Homework assignment
## Requirements
1. [x] PHP 8.1 minimum.
2. [x] Nginx.
3. [x] Mysql 8
4. [x] Composer
5. [x] Docker

### Setup project in localhost via docker
- Project url in github `https://github.com/pk70/moinul-task-laravel.git`
- Download file from git or `git clone https://github.com/pk70/moinul-task-laravel.git`
- Go to project folder and open terminal
- Run command `composer update`
- Make sure .env file is in root folder
- Change `DB_HOST` as your network ip in .env file
- Open shell terminal and `Run docker-compose up`
- Open terminal inside project folder and Run `docker-compose exec app php artisan migrate`
- Run command `docker-compose exec app php artisan key:generate` for generating encrypted key


### How to operate the project/software
- For customer api http://localhost/api/customers
- For starting queue job open a terminal and RUN `docker-compose exec app php artisan queue:work`
- For starting specific job open another terminal and RUN `docker-compose exec app php artisan job:dispatch PropertiesProcess authtoken`(here auth token is your token which is string)

### Technology used
- Laravel framework version 10

