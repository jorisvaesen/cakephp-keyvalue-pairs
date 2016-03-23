<?php

namespace JorisVaesen\KeyValuePairs\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ConfigsFixture extends TestFixture
{
    /**
     * fields property
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer'],
        'key' => ['type' => 'string'],
        'value' => ['type' => 'string'],
        'deleted' => ['type' => 'integer']
    ];
    /**
     * records property
     *
     * @var array
     */
    public $records = [
        ['id' => 1, 'key' => 'invoice_prefix', 'value' => 'INV-2016', 'deleted' => 0],
        ['id' => 2, 'key' => 'invoice_next_number', 'value' => '1234', 'deleted' => 0],
        ['id' => 3, 'key' => 'project_prefix', 'value' => 'PROJ-2016', 'deleted' => 1],
        ['id' => 4, 'key' => 'project_next_number', 'value' => '123', 'deleted' => 1]
    ];
}
