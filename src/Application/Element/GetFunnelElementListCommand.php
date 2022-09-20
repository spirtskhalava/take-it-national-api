<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Domain\Token\Token;
use Slim\Http\Request;

final class GetFunnelElementListCommand
{
    private $request;
    private $funnelId;
    private $token;

    public function __construct(Request $request, ?int $funnelId = null)
    {
        $this->request = $request;
        $this->funnelId = $funnelId;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getFunnelId(): ?int
    {
        return $this->funnelId;
    }

    public function hasIncludeParam(): bool
    {
        return null !== $this->request->getParam('include');
    }

    /**
     * @return Token|null
     */
    public function getToken():?Token
    {
        return $this->token ?? null;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }
}
