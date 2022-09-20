<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Domain\Token;

use App\Domain\User\UserRoleInterface;

class Token
{
    /**
     * @var array
     */
    private $decoded = [];

    /**
     * @param $decoded
     */
    public function populate(array $decoded): void
    {
        $this->decoded = $decoded;
    }

    /**
     * @param array $scope
     * @return bool
     */
    public function hasScope(array $scope): bool
    {
        return (bool)count(array_intersect($scope, $this->decoded['scope']));
    }

    /**
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return !empty($this->decoded['website_id']) ? (int)$this->decoded['website_id'] : null;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return !empty($this->decoded['user_id']) ? (int)$this->decoded['user_id'] : null;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->decoded['role'] ?? null;
    }

    /**
     * @return bool
     */
    public function getIsAdmin(): bool
    {
        return $this->getRole() === UserRoleInterface::ADMIN;
    }
}
