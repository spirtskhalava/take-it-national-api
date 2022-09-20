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

final class GetFunnelElementChildrenListCommand
{
    private $request;
    private $args;

    public function __construct(Request $request, array $args)
    {
        $this->request = $request;
        $this->args = $args;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function hasIncludeParam(): bool
    {
        return null !== $this->request->getParam('include');
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return (int)$this->args['id'];
    }
}
