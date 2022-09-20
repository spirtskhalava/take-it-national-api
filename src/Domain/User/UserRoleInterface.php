<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Domain\User;

use App\Domain\Common\StatusInterface;

interface UserRoleInterface extends StatusInterface
{
    public const ADMIN = 'admin';
    public const AGENCY = 'agency';
    public const MARKETING = 'marketing';
}
