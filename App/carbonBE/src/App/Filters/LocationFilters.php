<?php

namespace App\Filters;

class LocationFilters
{
    final public const LIST = [
        [
            'name' => 'page',
            'in' => 'query',
            'required' => false,
            'type' => 'integer',
            'format' => 'integer',
            'example' => '1',
            'default' => 1,
        ],
        [
            'name' => 'items_per_page',
            'in' => 'query',
            'required' => false,
            'type' => 'integer',
            'format' => 'integer',
            'example' => '10',
            'default' => 10,
        ],
        [
            'name' => 'user_id',
            'in' => 'query',
            'required' => false,
            'type' => 'integer',
            'format' => 'integer',
            'example' => '1',
            'default' => null,
        ],
        [
            'name' => 'name',
            'in' => 'query',
            'required' => false,
            'type' => 'string',
            'format' => 'string',
            'example' => 'cool',
            'default' => null,
        ],
        [
            'name' => 'level1',
            'in' => 'query',
            'required' => false,
            'type' => 'string',
            'format' => 'string',
            'example' => 'other',
            'default' => null,
        ],
        [
            'name' => 'level2',
            'in' => 'query',
            'required' => false,
            'type' => 'string',
            'format' => 'string',
            'example' => 'other',
            'default' => null,
        ],
    ];
}
