# CakePHP 3 Key Value Pairs

Map key-value pairs between datasource and application.

## Requirements

* PHP 5.4
* CakePHP 3.0+

## Installation

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
  * value
* scope
* preventDeletion
* allowedKeys
* cache
* cacheKey