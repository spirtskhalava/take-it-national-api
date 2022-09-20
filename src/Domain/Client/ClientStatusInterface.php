<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Domain\Client;

use App\Domain\Common\StatusInterface;

interface ClientStatusInterface extends StatusInterface
{
    public const BLOCKED = 3;
}
