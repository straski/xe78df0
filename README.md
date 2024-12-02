# xe78df0 API

## Getting Started

### With Docker

> This Docker setup is tested on MacOS, Linux (Ubuntu) and Windows with WSL2.


This is the recommended way.

1. [Install Docker Compose](https://docs.docker.com/compose/install/) (version 2.10+).
2. Clone this repository and `cd` into it.
3. Run `make start` to build images, start the containers and workers.
4. Run `make down` to stop the containers.

If you run into trouble using the `make` command, use the following `docker compose` commands directly:

1. Run `docker compose build --no-cache` to build images.
2. Run `docker compose up --pull always -d --wait` to set up and start the containers.
3. Run `docker compose exec php bin/console messenger:consume` to start the workers.
3. Run `docker compose down --remove-orphans` to stop the containers.

The API is accessible on https://localhost/api/files (self-signed cert).

### Without Docker

#### Requirements:
* [PHP](https://www.php.net/downloads.php) 8.3+,
* [Composer](https://getcomposer.org/download/),
* [PostgreSQL 16](https://www.postgresql.org/download/),
* A web server such as [Caddy](https://caddyserver.com/), Apache or [nginx](https://nginx.org/).

#### Steps:
1. [Configure](https://symfony.com/doc/7.1/setup/web_server_configuration.html) your web server.
2. Clone this repository and `cd` into it.
3. [Update](https://symfony.com/doc/7.1/doctrine.html#configuring-the-database) the the `DATABASE_URL` variable in the `.env` file with your database connection parameters.
5. Run `composer install` to install vendor dependencies.
6. Run `bin/console doc:mig:mig` to migrate the database schema.
7. Run `bin/console messenger:consume` to start the workers. 

Depending on your server's configuration the API should be accessible at `YOUR_SERVER_URL/api/files`. 

## Usage

Use the Postman collection as API documentation.

### Postman

Import the collection from `docs/postman` into your Postman app.
