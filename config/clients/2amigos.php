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
                'name' => '2amigos consulting group',
                'url' => 'https:/2amigos.us/',
                'client_id' => '',
                'client_secret' => '',
            ],
            // requires website_id
            'funnel' => [
                'fields' => [
                    'name' => '2amigos',
                ],
                // requires funnel_id
                'types' => [
                    [
                        'fields' => [
                            'name' => 'framework', // <-- this becomes type name lowercase!
                            'url_pattern' => '[framework]-developers',
                            'title' => '[framework] application development',
                            'description' => 'Use [framework] to create your next idea', // missing description
                        ]
                        // funnel_element_type_attribute
                        // 'attributes' => [
                        //      ['fields' => ['name' => 'example type attribute']]
                        // ]
                    ],
                ],
                // funnel_element_type_id
                'elements' => [
                    'framework' => [
                        'root' => true,
                        'items' => [
                            'Angular',
                            'ReactJs',
                            'NodeJs',
                            'Javascript',
                            'VueJs',
                            'Yii2',
                            'Symfony',
                            'Laravel',
                            'Slim',
                            // if we require attributes
                            // [
                            //    'name' => 'DNA testing',
                            //     'attributes' => [
                            //          ['type' => 'name-of-element-type', 'name' =>'']
                            //      ]
                            // ]
                        ],
                    ],
                ],
            ],
        ],
    ],
];
