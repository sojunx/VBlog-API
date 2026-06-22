# VBlog-API

## How to use

- Clone this repo
- Create a .env file

```dotenv
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=password
DB_DATABASE=local_db
API_DEBUG=true
```

- Install PHP 8.0 or higher, Mariadb, and Composer
- Download GNU to use Makefile
- Run `composer install`
- Run `make migrate`
- Run `make seed`
- Run `make run`
- Run `make rollback` to roll back last migration
- Start using the API with http://localhost:8000/api/v1