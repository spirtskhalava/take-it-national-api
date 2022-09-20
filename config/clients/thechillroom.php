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
                    'name' => 'thechillroom',
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
                            'kava',
                            'kava bar',
                            'kava shop',
                            'kava store',
                            'kava chill',
                            'kava lounge',
                            'vape',
                            'vaping',
                            'vape lounge',
                            'vape shop',
                            'vape store',
                            'vape in',
                            'vape near',
                            'vape chill',
                            'vape and kava',
                            'vape and kava bar',
                            'vape and kava lounge',
                            'vape and kava chill',
                            'vape and kava chill lounge',
                            'kava and vape',
                            'kava and vape bar',
                            'kava and vape lounge',
                            'kava and vape chill',
                            'kava and vape chill lounge',
                            'Glass',
                            'Glass Blowing',
                            'Glass Shop',
                            'Hookah',
                            'Headshop',
                        ],
                        'child' => 'location',
                    ],
                    'location' => [
                        'root' => false,
                        'items' => [
                            'Lake Worth',
                            'Lake Worth Rd',
                            'Lake Worth Road',
                            'Palm Beach',
                            'Palm Beach Florida',
                            'Boynton Beach',
                            'Wellington',
                            'Greenacres',
                            'Aberdeen',
                            'Royal Palm Estates',
                            'Lantan',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
