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
    ];
    /**
     * records property
     *
     * @var array
     */
    public $records = [
        ['key' => 'invoice_prefix', 'value' => 'INV-2016'],
        ['key' => 'invoice_next_number', 'value' => '1234'],
        ['key' => 'project_prefix', 'value' => 'PROJ-2016'],
        ['key' => 'project_next_number', 'value' => '123']
    ];
}