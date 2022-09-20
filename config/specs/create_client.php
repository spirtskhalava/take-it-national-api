<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Zend\Validator\StringLength;

return [
    [
        'name' => 'user_id',
        'required' => false,
        'filters' => [
            ['name' => 'Int'],
        ]
    ],
    [
        'name' => 'name',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'messages' => [
                        StringLength::TOO_SHORT => 'Name must be at least 6 characters long'
                    ],
                    'min' => 6,
                ],
            ],
        ]
    ],
    [
        'name' => 'notes',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
];
