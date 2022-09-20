<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Domain\Common;

interface StatusInterface
{
    public const NOT_ACTIVE = 0;
    public const ACTIVE = 1;
    public const DELETED = 2;
}
