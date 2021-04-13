# API PoC

Install dependencies
```
composer install
```

Create database (sqlite)
```
php bin/console doctrine:database:create
```

Load fixtures
```
php bin/console doctrine:fixtures:load
```

(Install `symfony` binary https://symfony.com/doc/current/setup/symfony_server.html#installation)

Start server
```
symfony server:start
```

## List action features

Try `/customers` endpoint with GET parameters.

Use `pageSize`, `page`, `orderBy`, `filters`, `search` and `fields`.

```
http://127.0.0.1:8000/customers
http://127.0.0.1:8000/customers?pageSize=1&page=2
http://127.0.0.1:8000/customers?orderBy=id%2BDESC
http://127.0.0.1:8000/customers?filters=name%3Alike%3AExample%7CFoo
http://127.0.0.1:8000/customers?search=Bar
http://127.0.0.1:8000/customers?fields=id,name
```
