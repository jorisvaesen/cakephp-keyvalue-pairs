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

| Key  | Default | Description |
| ------------- | ------------- | ------------- |
| fields.key  | `'key'` |   |
| fields.value  | `'value'`  |   |
| scope  | `false`  |   |
| preventDeletion  | `false`  |   |
| allowedKeys  | `false`  |   |
| cache  | `false`  |   |
| cacheKey  | `'default'`  |   |