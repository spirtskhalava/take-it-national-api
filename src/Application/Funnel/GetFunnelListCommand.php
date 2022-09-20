<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Domain\Token\Token;
use Slim\Http\Request;

class GetFunnelListCommand
{
    private $request;
    private $websiteId;
    private $token;

    public function __construct(Request $request, ?int $websiteId = null)
    {
        $this->request = $request;
        $this->websiteId = $websiteId;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getWebsiteId(): ?int
    {
        return $this->websiteId;
    }

    public function hasIncludeParam(): bool
    {
        return null !== $this->request->getParam('include');
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return Token|null
     */
    public function getToken():?Token
    {
        return $this->token ?? null;
    }
}
