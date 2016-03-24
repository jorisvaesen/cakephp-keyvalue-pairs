# CakePHP 3 Key Value Pairs [![Build Status](https://travis-ci.org/jorisvaesen/cakephp-keyvalue-pairs.svg?branch=master)](https://travis-ci.org/jorisvaesen/cakephp-keyvalue-pairs) [![Coverage Status](https://coveralls.io/repos/github/jorisvaesen/cakephp-keyvalue-pairs/badge.svg?branch=master)](https://coveralls.io/github/jorisvaesen/cakephp-keyvalue-pairs?branch=master)

Map key-value pairs between datasource and application.

## Requirements

* PHP 5.4+
* CakePHP 3.x

## Installation

### Using composer

Run the following command

```
composer require jorisvaesen/cakephp-keyvalue-pairs:"~3.0"
```

or copy the json snippet for the latest version into your project’s composer.json.

```
{
    "require": {
        "jorisvaesen/cakephp-keyvalue-pairs": "~3.0"
    }
}
```

You need to enable the plugin in your `config/bootstrap.php` file:

```php
Plugin::load('JorisVaesen/KeyValuePairs');
```

If you are already using `Plugin::loadAll();`, then this is not necessary.

## Usage

### Attach behavior

```php
$this->addBehavior('JorisVaesen/KeyValuePairs.KeyValuePairs', [
    // Here you can override the default options
]);
```

### Options

| Key  | Default | Description |
| ------------- | ------------- | ------------- |
| fields.key  | `'key'` | Name of the key field |
| fields.value  | `'value'`  | Name of the value field |
| scope  | `false`  | If you want to set extra conditions |
| preventDeletion  | `false`  | Prevent pairs from being deleted. `true` to disallow deletion, `array` to specify keys that should not be removed |
| allowedKeys  | `false`  | `array` of allowed keys or `false` to allow any |
| cache  | `false`  | Enable or disable caching  |
| cacheKey  | `'default'`  | A custom cache key that should be used |

### Available functions

* `findPair($key)` get the value of `$key`.
* `findPairs($keys, $requireAll = true)` returns an associative array with the keys and its values. When `$requireAll` is set true, the function returns false when not all keys could be found. 