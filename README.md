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
| cacheConfig  | `'default'`  | A custom cache config that should be used |

### Available functions

* `findPair($key)` get the value of `$key`.
* `findPairs($keys, $requireAll = true)` returns an associative array with the keys and its values. When `$requireAll` is set true, the function returns false when not all keys could be found.

### Tips

* **Cache invalidation happens on afterSave and afterDelete callbacks, when you use `updateAll`, these callbacks don't get called. In this case you should invalidate the cache yourself.**
* Caching is advisable and its duration can be set to `+999 days` since the cached result gets invalidated automatically when a pair gets saved or removed.
* Caching automatically saves all the pairs in the database and extracts the specific values from it. If you want a cache file for each record, disable caching in this plugin and do the caching yourself or suggest the functionality by doing a pull request.
* This plugin is rather new so it can contain bugs. If you find any or want to suggest enhancements, please use the issue tracker [here](https://github.com/jorisvaesen/cakephp-keyvalue-pairs/issues).

## Example

Lets say you have an application where the user can create invoices which should get a prefix and postfix when created. Every invoice gets these static values but the user should be able to change them over time for new invoices (like when they use the current year in it).

First we create a database table to store the key value pairs and insert the defaults.

```
CREATE TABLE `configs` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` VARCHAR(255) NOT NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `modified` datetime NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `key_index` (`key`),
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `configs` (`key`, `value`, `is_deleted`) VALUES ('invoice_prefix', 'INV-', 0);
INSERT INTO `configs` (`key`, `value`, `is_deleted`) VALUES ('invoice_postfix', '-2016', 0);
```

We create a cache config that should be used by the plugin.

```php
Cache::config('pairs', [
    'className' => 'File',
    'duration' => '+999 days',  // cache gets invalidated automatically when a pair is saved or removed
    'path' => CACHE,
    'prefix' => 'pairs_'
]);
```

Next we attach the behavior to our table in `Model/Table/ConfigsTable.php`.

```php
public function initialize(array $config) 
{
    ...
    
    $this->addBehavior('JorisVaesen/KeyValuePairs.KeyValuePairs', [
        'cache' => true,            //  Enable caching
        'cacheConfig' => 'pairs',   //  Tell the plugin to use the pairs cache config
        'scope' => [                // Just as example to show how to use extra conditions when fetching pairs
            'is_deleted' => false
        ],
        'preventDeletion' => true,  // Prevents us from deleting any record in this table (and thereby possibly break the app)
        'allowedKeys' => [          // Prevents us from saving any other keys than the ones specified here
            'invoice_prefix',
            'invoice_postfix'
        ]
    ]);
}
```

Now when a new invoice is created we can fetch the prefix and the postfix for it.

```php
public function add() 
{
    ...
    
    $pairs = TableRegistry::get('Configs')->findPairs(['invoice_prefix', 'invoice_postfix], true);
    
    $invoice->number = $pairs['invoice_prefix'] . $invoice_number . $pairs['invoice_postfix'];
    
    $this->Invoices->save($invoice);
    
    ...
}
```

## License

The MIT License (MIT)

Copyright (c) 2016 Joris Vaesen

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.