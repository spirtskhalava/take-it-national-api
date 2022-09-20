<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\User;

use League\Fractal\TransformerAbstract;

final class ProfileTransformer extends TransformerAbstract
{
    /**
     * @param array $profile
     * @return array
     */
    public function transform(array $profile)
    {
        return [
            'billing_address' => $profile['billing_address'],
            'secondary_address' => $profile['secondary_address'],
            'city' => $profile['city'],
            'state' => $profile['state'],
            'zip' => $profile['zip'],
            'phone' => $profile['phone'],
            'website' => $profile['website'],
            'notes' => $profile['notes'],
            'first_name' => $profile['first_name'],
            'last_name' => $profile['last_name'],
            'company_name' => $profile['company_name'],
            'avatar' => $profile['avatar']
        ];
    }
}
