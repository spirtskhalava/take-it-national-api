<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Domain\Element\FunnelElementStatusInterface;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;

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
        ],
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
                        FunnelElementStatusInterface::ACTIVE,
                        FunnelElementStatusInterface::NOT_ACTIVE,
                        FunnelElementStatusInterface::DELETED
                    ],
                ],
            ],
        ],
    ],
];
