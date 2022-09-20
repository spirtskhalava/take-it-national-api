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
            // requires website_id
            'funnel' => [
                'fields' => [
                    'name' => 'walkinlabmd',
                ],
                // requires funnel_id
                'types' => [
                    [
                        'fields' => [
                            'name' => 'specialty', // <-- this becomes type name!
                            'url_pattern' => '[specialty]',
                            'title' => 'Specialty [specialty]',
                            'description' => '[specialty]', // missing description
                        ]
                        // 'attributes' => [] // funnel_element_type_attribute
                    ],
                    [
                        'fields' => [
                            'name' => 'city', // <-- this becomes type name lowercase!
                            'url_pattern' => '[specialty]-in-[city]',
                            'title' => '[specialty] in [city]',
                            'description' => '[specialty] in [city]', // missing description
                        ]
                        // funnel_element_type_attribute
                        // 'attributes' => [
                        //      ['fields' => ['name' => 'example type attribute']]
                        // ]
                    ],
                ],
                // funnel_element_type_id
                'elements' => [
                    'specialty' => [
                        'root' => true,
                        'items' => [
                            'DNA testing',
                            'Paternity testing',
                            'Daddy testing',
                            'STD testing',
                            'Employment drug testing',
                            'Employment screening',
                            'Employment testing',
                            'MMA testing',
                            'Mixed martial arts testing',
                            'Lab testing',
                            'Affordable lab testing',
                            'Vitamin testing',
                            'Hormone testing',
                            'Temp agency testing',
                            // if we require attributes
                            // [
                            //    'name' => 'DNA testing',
                            //     'attributes' => [
                            //          ['type' => 'name-of-element-type', 'name' =>'']
                            //      ]
                            // ]
                        ],
                        'child' => 'city'
                    ],
                    // funnel_element_type
                    'city' => [ // <-- this is type name! lowercase!
                        'root' => false,
                        'items' => [
                            'Sugar Hill',
                            'Pleasant Grove',
                            'Jack Cullen Drive',
                            'Texarkana, Texas',
                            'Texarkana, Arkansas',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
