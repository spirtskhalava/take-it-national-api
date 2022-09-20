<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

return [
    [
        'name' => 'client_id',
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
            ]
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
            ]
        ],
    ],
    [
        'name' => 'url',
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
            [
                'name' => 'Uri',
                'options' => [
                    'allowRelative' => false,
                ],
            ],
        ],
    ],
    [
        'name' => 'api_key',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'messages' => [
                        StringLength::TOO_SHORT => 'Client id must be at least 6 characters long',
                    ],
                    'min' => 6,
                ],
            ],
        ],
    ],
    [
        'name' => 'api_secret',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'messages' => [
                        StringLength::TOO_SHORT => 'Client secret must be at least 6 characters long',
                    ],
                    'min' => 6,
                ],
            ],
        ],
    ],
];
