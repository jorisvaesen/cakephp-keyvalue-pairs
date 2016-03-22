# CakePHP 3 Key Value Pairs

Map key-value pairs between datasource and application.

## Requirements

* PHP 5.4
* CakePHP 3.0+

## Installation

Using composer:

```
    composer require jorisvaesen/cakephp-keyvalue-pairs
```

Enable plugin by adding this line to your bootstrap.php:

```php
    Plugin::load('Jorisvaesen/KeyValuePairs');
```

## Usage

### Attach behavior

```php
    $this->addBehavior('Jorisvaesen/KeyValuePairs.KeyValuePairs', [
        // Here you can override the default options
    ]);
```

### Options

* fields
  * key
      Default: `(string) key`
  * value
      Default: `(string) value`
* scope
    Default: `(boolean) false`
* preventDeletion
    Default: `(boolean) false`
* allowedKeys
    Default: `(boolean) false`
* cache `
    Default: `(boolean) false`
* cacheKey
    Default: `(string) default`
