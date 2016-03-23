# CakePHP 3 Key Value Pairs ![alt tag](https://travis-ci.org/jorisvaesen/cakephp-keyvalue-pairs.svg?branch=master)

Map key-value pairs between datasource and application.

## Requirements

* PHP 5.4+
* CakePHP 3.x

## Installation

Using composer:

```
composer require jorisvaesen/cakephp-keyvalue-pairs
```

Enable plugin by adding this line to your bootstrap.php:

```php
Plugin::load('JorisVaesen/KeyValuePairs');
```

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