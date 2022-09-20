<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use Exception;

final class WebsiteNotFoundException extends Exception
{
    protected $message = 'Website not found';
}
