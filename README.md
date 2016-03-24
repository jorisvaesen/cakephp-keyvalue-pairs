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

or copy the json snippet for the latest version into your project’s `composer.json`:

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

## License

The MIT License (MIT)

Copyright (c) 2016 Joris Vaesen

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.