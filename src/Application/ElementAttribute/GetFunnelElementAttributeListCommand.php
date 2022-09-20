<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\ElementAttribute;

use App\Domain\Token\Token;
use Slim\Http\Request;

final class GetFunnelElementAttributeListCommand
{
    private $request;
    private $funnelElementId;
    private $token;

    public function __construct(Request $request, ?int $funnelElementId = null)
    {
        $this->request = $request;
        $this->funnelElementId = $funnelElementId;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getFunnelElementId(): ?int
    {
        return $this->funnelElementId;
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
