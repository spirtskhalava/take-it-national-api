<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use Slim\Http\Request;

final class GetWebsiteCommand
{
    private $id;
    private $request;

    public function __construct(int $id, Request $request)
    {
        $this->id = $id;
        $this->request = $request;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function hasIncludeParam(): bool
    {
        return null !== $this->request->getParam('include');
    }
}
