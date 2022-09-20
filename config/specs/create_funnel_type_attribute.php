<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Zend\Validator\NotEmpty;

return [
    [
        'name' => 'funnel_element_type_id',
        'required' => true,
        'filters' => [
            ['name' => 'Int'],
        ],
        'validators' => [
            [
                'name' => 'NotEmpty',
                'options' => [
                    'type' => NotEmpty::ALL,
                ],
            ],
        ],
    ],
    [
        'name' => 'name',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'NotEmpty',
                'options' => [
                    'type' => NotEmpty::ALL,
                ],
            ],
        ],
    ],
    [
        'name' => 'description',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'NotEmpty',
                'options' => [
                    'type' => NotEmpty::ALL,
                ],
            ],
        ],
    ],
];
