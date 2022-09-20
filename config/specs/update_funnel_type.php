<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Domain\Type\FunnelElementTypeStatusInterface;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;
use Zend\Validator\Callback;

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
        'name' => 'title',
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
        'name' => 'description',
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
        'name' => 'url_pattern',
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
                        FunnelElementTypeStatusInterface::ACTIVE,
                        FunnelElementTypeStatusInterface::NOT_ACTIVE,
                        FunnelElementTypeStatusInterface::DELETED
                    ],
                ],
            ],
            [
                'name' => 'Callback',
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => 'Cannot change status of this funnel element type.',
                    ],
                    'callback' => function (string $value = null) use ($container, $request) {
                        $funnelElementTypeRepository = $container['funnel.element.type.repository'];
                        $id = $request->getAttribute('route')->getArgument('id');

                        $type = $funnelElementTypeRepository->findOneById((int)$id, FunnelElementTypeStatusInterface::DELETED);

                        if (!empty($type['parent_type_id'])) {
                            $parent = $funnelElementTypeRepository->findOneById((int)$type['parent_type_id']);
                            return (bool)$parent['has_child'] === false;
                        }

                        $rootType = $funnelElementTypeRepository->findOneActiveRootByFunnelId((int)$type['funnel_id']);

                        return empty($rootType) || $rootType['id'] === $type['id'];
                    },
                ],
            ],
        ],
    ],
];
