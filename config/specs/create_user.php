<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Domain\User\UserStatusInterface;
use Zend\Validator\InArray;
use Zend\Validator\StringLength;


return [
    [
        'name' => 'username',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'messages' => [
                        StringLength::TOO_SHORT => 'Username must be at least 6 characters long'
                    ],
                    'min' => 6,
                ],
            ],
        ]
    ],
    [
        'name' => 'email',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'EmailAddress',
            ],
        ],
    ],
    [
        'name' => 'role',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'InArray',
                'options' => [
                    'messages' => [
                        InArray::NOT_IN_ARRAY => 'Unknown role',
                    ],
                    'strict' => InArray::COMPARE_STRICT,
                    'haystack' => ['admin', 'agency', 'marketing'],
                ],
            ],
        ],
    ],
    [
        'name' => 'password',
        'required' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
    ],
    [
        'name' => 'status',
        'required' => false,
        'continue_if_empty' => true,
        'filters' => [
            [
                'name' => 'Int',
            ],
        ],
        'validators' => [
            [
                'name' => 'InArray',
                'options' => [
                    'messages' => [
                        InArray::NOT_IN_ARRAY => 'Unknown status',
                    ],
                    'strict' => InArray::COMPARE_STRICT,
                    'haystack' => [UserStatusInterface::NOT_ACTIVE, UserStatusInterface::ACTIVE],
                ],
            ],
        ],
    ],
];
