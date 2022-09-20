<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Domain\User\UserStatusInterface;
use Zend\Filter\Boolean;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

return [
    [
        'name' => 'username',
        'required' => false,
        'continue_if_empty' => true,
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
                'name' => 'StringLength',
                'options' => [
                    'messages' => [
                        StringLength::TOO_SHORT => 'Username must be at least 6 characters long'
                    ],
                    'min' => 6,
                ],
            ],
        ],
    ],
    [
        'name' => 'email',
        'required' => false,
        'continue_if_empty' => true,
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
            ['name' => 'EmailAddress'],
        ],
    ],
    [
        'name' => 'role',
        'required' => false,
        'continue_if_empty' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'NotEmpty',
            ],
            [
                'name' => 'InArray',
                'options' => [
                    'messages' => [
                        InArray::NOT_IN_ARRAY => 'Unknown role',
                    ],
                    'strict' => InArray::COMPARE_STRICT,
                    'haystack' => ['admin', 'agency'],
                ],
            ],
        ],
    ],
    [
        'name' => 'password',
        'required' => false,
        'continue_if_empty' => true,
        'filters' => [
            ['name' => 'StringTrim'],
        ],
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'messages' => [
                        StringLength::TOO_SHORT => 'Password must be at least 6 characters long'
                    ],
                    'min' => 6,
                ],
            ],
        ]
    ],
    [
        'name' => 'block',
        'required' => false,
        'filters' => [
            [
                'name' => 'Boolean',
                'type' => Boolean::TYPE_ALL,
            ],
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
                    'haystack' => [
                        UserStatusInterface::NOT_ACTIVE,
                        UserStatusInterface::ACTIVE,
                        UserStatusInterface::DELETED
                    ],
                ],
            ],
        ],
    ],
];
