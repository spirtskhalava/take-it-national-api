<?php
/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Domain\Token\Token;

final class FunnelRefreshConfigurationCommand
{
    private $token;

    /**
     * @return Token|null
     */
    public function getToken(): ?Token
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
