<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

return [
    'config' => [
        [
            'name'     => "United States",
            'type'     => 'country',
            'meta'     => ['description' => 'Country United states Lorem ipsum dolor', 'author' => 'tin'],
            "title"    => "Title United States",
            "tagline"  => "Example country desc",
            'children' => [
                [
                    'name'     => "North-Carolina",
                    'type'     => "state",
                    'meta'     => ['description' => 'North Carolina lorem ipsum dolor'],
                    "title"    => "Title North Carolina",
                    "tagline"  => "Example country desc",
                    'image'    => "http://www.someimage.com/img.jpg",
                    'children' => [
                        [
                            'type' => "city",
                            'name' => "Winston-Salem",
                            'meta' => ['description' => 'North Carolina city winston salem lorem ipsum dolor'],
                            'image' => 'hhttp://vagrant.test/images/ws.jpg'
                        ], [
                            'type'  => "city",
                            'meta' => ['description' => 'North Carolina city Greensboro lorem ipsum dolor'],
                            'name'  => "Greensboro",
                            'image' => 'http://vagrant.test/images/greensboro.jpg'
                        ]
                    ],
                ],
                [
                    "name"     => "Virginia",
                    "type"     => "state",
                    "title"    => "title Virginia",
                    "tagline"  => "example state",
                    'meta'     => ['description' => 'Virginia  lorem ipsum dolor'],
                    "image"    => "http://www.someimage.com/img.jpg",
                    'children' => [
                        [
                            'type'  => "city",
                            'name'  => "Richmond",
                            'meta' => ['description' => 'Virginia city Richmond lorem ipsum dolor'],
                            'image' => 'http://vagrant.tes/richmond.jpg'
                        ], [
                            'type'  => "city",
                            'name'  => "Charlottesville",
                            'meta' => ['description' => 'Virginia city Charlottesville lorem ipsum dolor'],
                            'image' => 'http://vagrant.test/images/charlottesville.jpg'
                        ], [
                            'type'  => "city",
                            'name'  => "Roanoke",
                            'meta' => ['description' => 'Virginia city Roanoke lorem ipsum dolor'],
                            'image' => 'http://vagrant.test/images/roanoke.jpg'
                        ], [
                            'type'  => 'city',
                            'name'  => 'Harrisonburg',
                            'meta' => ['description' => 'Virginia city Harrisonburg lorem ipsum dolor'],
                            'image' => 'http://vagrant.test/images/harrisonburg.jpg'
                        ]
                    ]
                ],
            ]
        ]
    ],
    "types"  => [
        'country' => [
            'urlPattern' => '/',
            'attributes' => ['name', 'image']
        ],
        'state'   => [
            'urlPattern' => 'state/[state]',
            'attributes' => ['name', 'image']
        ],
        'city'    => [
            'urlPattern' => '[state]/city/[city]/',
            'attributes' => ['name', 'image']
        ]
    ]
];
