# MyAdmin Google Analytics Plugin

Google Analytics integration plugin for the [MyAdmin](https://github.com/detain/myadmin) control panel framework. Provides event-driven hooks for analytics tracking, menu registration, requirement loading, and settings management within the MyAdmin plugin architecture.

[![Build Status](https://github.com/detain/myadmin-google-analytics/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-google-analytics/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-google-analytics/version)](https://packagist.org/packages/detain/myadmin-google-analytics)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-google-analytics/downloads)](https://packagist.org/packages/detain/myadmin-google-analytics)
[![License](https://poser.pugx.org/detain/myadmin-google-analytics/license)](https://packagist.org/packages/detain/myadmin-google-analytics)

## Installation

Install via Composer:

```sh
composer require detain/myadmin-google-analytics
```

## Usage

The plugin registers itself through the MyAdmin event system. It provides the following event handlers:

- **getHooks()** -- Returns an array of event hooks the plugin subscribes to.
- **getMenu()** -- Registers admin menu entries when the current user has appropriate permissions.
- **getRequirements()** -- Registers class and function requirements with the plugin loader.
- **getSettings()** -- Integrates plugin settings into the MyAdmin settings panel.

## Running Tests

```sh
composer install
vendor/bin/phpunit
```

## License

This package is licensed under the [LGPL-2.1](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html) license.
