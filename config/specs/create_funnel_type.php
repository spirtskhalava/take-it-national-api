<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Zend\Validator\NotEmpty;
use Zend\Validator\Callback;

$body = $request->getParsedBody();

return [
    [
        'name' => 'funnel_id',
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
            [
                'name' => 'Callback',
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => 'This Funnel_id does not exist.',
                    ],
                    'callback' => function ($value) use ($container) {
                        $funnelRepository = $container['funnel.repository'];
                        $funnel = $funnelRepository->findOneById((int)$value);

                        return !empty($funnel);
                    },
                ],
            ]
        ],
    ],
    [
        'name' => 'parent_type_id',
        'required' => true,
        'continue_if_empty' => true,
        'filters' => [
            ['name' => 'Int'],
        ],
        'validators' => [
            [
                'name' => 'Callback',
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => 'Parent_type_id is not valid.',
                    ],
                    'callback' => function (string $value = null) use ($container, $body) {
                        $funnelElementTypeRepository = $container['funnel.element.type.repository'];

                        if (empty($value)) {
                            if (!empty($funnelElementTypeRepository->findOneActiveRootByFunnelId((int)$body['funnel_id']))) {
                                return false;
                            }

                            return true;
                        }

                        $parent = $funnelElementTypeRepository->findOneById((int)$value);

                        $ancestry = !empty($parent['ancestry'])
                            ? explode('/', $parent['ancestry'])
                            : [];

                        if (in_array($value, $ancestry) || (bool)$parent['has_child']) {
                            return false;
                        }

                        return true;
                    },
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
        'name' => 'title',
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
    [
        'name' => 'url_pattern',
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
