<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Domain\Funnel\FunnelStatusInterface;
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
        'name' => 'status',
        'required' => false,
        'continue_if_empty' => true,
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
            [
                'name' => 'InArray',
                'options' => [
                    'messages' => [
                        InArray::NOT_IN_ARRAY => 'Unknown status',
                    ],
                    'strict' => InArray::COMPARE_STRICT,
                    'haystack' => [
                        FunnelStatusInterface::ACTIVE,
                        FunnelStatusInterface::NOT_ACTIVE,
                        FunnelStatusInterface::DELETED
                    ],
                ],
            ],
        ],
    ],
    [
        'name' => 'notes',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'website',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'address',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'secondary_address',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'phone',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'city',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'state',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'zip',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'logo',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'instagram',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'facebook',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'linked_in',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'twitter',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ],
    [
        'name' => 'industry',
        'required' => false,
        'filters' => [
            ['name' => 'StringTrim'],
        ]
    ]
];
