<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

return [
    'client' => [
        'fields' => [
            'role' => 'client',
            'username' => '',
            'email' => '',
            'password' => '',
        ],
    ],
    'websites' => [
        [
            'fields' => [
                'name' => '',
                'url' => '',
                'client_id' => '',
                'client_secret' => '',
            ],
            'funnel' => [
                'fields' => [
                    'name' => 'sunsetmoving',
                ],
                'types' => [
                    [
                        'fields' => [
                            'name' => 'specialty',
                            'url_pattern' => '[specialty]',
                            'title' => 'Specialty [specialty]',
                            'description' => '[specialty]',
                        ],
                    ],
                    [
                        'fields' => [
                            'name' => 'location',
                            'url_pattern' => '[specialty]-in-[location]',
                            'title' => '[specialty] in [location]',
                            'description' => '[specialty] in [location]', // missing description
                        ],
                    ],
                ],
                'elements' => [
                    'specialty' => [
                        'root' => true,
                        'items' => [
                            'moving and storage',
                            'residential moving',
                            'commercial moving',
                            'residential movers',
                            'commercial movers',
                            'storage',
                            'moving services',
                            'house moving',
                            'office moving',
                            'furniture moving',
                            'stress free moving',
                            'cross town moving',
                            'cross country moving',
                            'professional movers',
                            'professional moving',
                            'moving experts',
                            'best moving company',
                            'best residential movers',
                            'best residential moving company',
                            'best commercial movers',
                            'best commercial moving company',
                            'trusted moving company',
                            'trusted movers',
                            'paino movers',
                            'paino moving',
                            'long distance movers',
                            'long distance moving',
                            'safe and secure storage',
                            'safe and secure storage facilities',
                        ],
                        'child' => 'location',
                    ],
                    'location' => [
                        'root' => false,
                        'items' => [
                            'oakland park',
                            'fort launderdale',
                            'ft lauderdale',
                            'south florida',
                            'broward county',
                            'broward',
                            'florida',
                            'lauderdhill',
                            'lauderdale lakes',
                            'north andrews gardens',
                            'coral ridge isles',
                            'west oakland park',
                            'east oakland park',
                            'wilton manors',
                            'coral ridge',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
