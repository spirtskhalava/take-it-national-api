<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Domain\Website\WebsiteStatusInterface;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

return [
    [
        'name' => 'name',
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
                        StringLength::TOO_SHORT => 'Username must be at least 6 characters long',
                    ],
                    'min' => 6,
                ],
            ],
        ],
    ],
    [
        'name' => 'url',
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
                'name' => 'Uri',
                'options' => [
                    'allowRelative' => false,
                ],
            ],
        ],
    ],
    [
        'name' => 'api_key',
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
                        StringLength::TOO_SHORT => 'Client id must be at least 6 characters long',
                    ],
                    'min' => 6,
                ],
            ],
        ],
    ],
    [
        'name' => 'api_secret',
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
                        StringLength::TOO_SHORT => 'Client secret must be at least 6 characters long',
                    ],
                    'min' => 6,
                ],
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
                        WebsiteStatusInterface::NOT_ACTIVE,
                        WebsiteStatusInterface::ACTIVE,
                        WebsiteStatusInterface::DELETED
                    ],
                ],
            ],
        ],
    ],
];
