<?php

namespace App\Filters;

class BaseFilters
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
    ];
}
