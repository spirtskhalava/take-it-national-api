<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Website;

use App\Domain\Token\Token;
use Slim\Http\Request;

final class GetWebsiteListCommand
{
    private $request;
    private $clientId;
    private $token;

    public function __construct(Request $request, ?int $clientId = null)
    {
        $this->request = $request;
        $this->clientId = $clientId;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getId(): ?int
    {
        return $this->clientId;
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
