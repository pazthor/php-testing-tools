# Ewallet Console Application

The following commands are meant to be run from the `dev` container in this
folder.

```bash
$ cd applications/console
```

## Setup

```bash
$ composer install
```

## Tests

Setup the testing database

```bash
$ ENV=testing bin/doctrine orm:schema-tool:update --force
```

Run the tests

```bash
$ bin/phpunit --testdox
```