# API PoC

Requires PHP 7.4

Install dependencies
```
composer install
```

Create database (sqlite) and update schema
```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

Load fixtures
```
php bin/console hautelook:fixtures:load
```

(Install `symfony` binary for Symfony server https://symfony.com/doc/current/setup/symfony_server.html#installation)

Start server
```
symfony server:start
```

## list() action features

Use API token `sk_user_1` and try `/customers` endpoint (base URL is `http://127.0.0.1:8000` when running `symfony server:start`).

Use `pageSize`, `page`, `orderBy`, `filters`, `search` and `fields` parameters.

```
http://127.0.0.1:8000/customers
http://127.0.0.1:8000/customers?pageSize=1&page=2
http://127.0.0.1:8000/customers?orderBy=id DESC
http://127.0.0.1:8000/customers?filters=name:like:Example
http://127.0.0.1:8000/customers?search=Bar
http://127.0.0.1:8000/customers?fields=id,name
```

## Endpoint ideas

```
GET /tasks                  # Returns company's all tasks
GET /teams/{id}/tasks       # Returns all tasks belonging to team {id}
GET /teams/all/tasks        # Returns all tasks belonging to any user's teams

GET /teams/all/tasks?filters=group:eq:123,456
```
