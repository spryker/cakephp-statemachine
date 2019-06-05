#  CakePHP StateMachine Plugin

[![Build Status](https://travis-ci.com/spryker/cakephp-statemachine.svg?branch=master)](https://travis-ci.com/spryker/cakephp-statemachine)
[![PHPStan level](https://img.shields.io/badge/style-level%207-brightgreen.svg?style=flat-square&label=phpstan)](https://github.com/phpstan/phpstan)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)

StateMachine engine for CakePHP applications.

**This branch is for CakePHP 3.x**

## Features

- Easy to use and modify
- Live preview as rendered image
- Simple admin interface included.

Note: This plugin is a sandbox/showcase for state machines.
Use with Caution.

## License

License is not open source, but open code.

The plugin is offered are provided free of charge by Spryker Systems GmbH and can be used in any CakePHP project. 
They are experimental and under the Spryker Sandbox License (see LICENSE file). 
However, Spryker does not warrant or assume any liability for errors that occur during use.  
Spryker does not guarantee their full functionality neither does Spryker assume liability for any disadvantages related to the use of the experimental plugin. 
Spryker does not guarantee any updates, upgrades or similar to the experimental plugin.  
By installing the experimental plugin, the project agrees to these terms of use. Please check LICENSE every 90 days.

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
