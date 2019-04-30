#  CakePHP StateMachine Plugin

StateMachine engine for CakePHP applications.

**This branch is for CakePHP 3.x**

## Features

- Easy to use and modify
- Live preview as rendered image
- Simple admin interface included.

Note: This plugin is a sandbox/showcase for state machines.
Use with Caution.

## License

License is not open source, but open code. Please see LICENSE file for details. 
Spryker grants to Licensee, during the **45-calendar-day** period (the **"Evaluation Period"**) following the download of the Software,
the nontransferable, nonexclusive limited, free of charge license to permit Licensee’s employees to internally use the Software
to test and evaluate the Software.

## Install

### Requirements

StateMachine plugin requires GraphViz. 
Please check https://graphviz.gitlab.io/download/ in order to install it for your system.

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
bin/cake migrations migrate -p StateMachine
```
Or just copy the migration file into your app `src/config/Migrations/`, modify if needed, and then run it as part of your app migrations.

Fully tested so far are PostgreSQL and MySQL, but by using the ORM all major databases should be supported.

## Usage

Navigate to `http://example.local/admin/state-machine` to view your currently setup state machines.

See [Documentation](/docs) for more details.
