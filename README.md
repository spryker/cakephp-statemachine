#  CakePHP StateMachine Plugin

StateMachine engine for CakePHP applications.

**This branch is for CakePHP 3.x**

## Features

- Easy to use and modify
- Live preview as rendered image
- Simple admin interface included.


## Install

### Composer (preferred)
```
composer require spryker/cakephp-statemachine
```

## Setup
Enable the plugin in your `config/bootstrap.php` or call
```
bin/cake plugin load StateMachine
```

Run migrations:
```
bin/cake Migrations migrate -p StateMachine
```
Or just copy the migration file into your app `src/config/Migrations/`, modify if needed, and then run it as part of your app migrations.

Fully tested so far are PostgreSQL and MySQL, but by using the ORM all major databases should be supported.

## Usage

Navigate to `http://example.local/admin/state-machine` to view your currently setup state machines.

See [Documentation](/docs) for more details.
