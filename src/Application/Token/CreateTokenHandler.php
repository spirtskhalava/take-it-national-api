<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Token;

use DateTimeImmutable;
use Exception;
use Firebase\JWT\JWT;
use Tuupola\Base62;

final class CreateTokenHandler
{
    /**
     * @param CreateTokenCommand $command
     * @throws CreateTokenException
     * @return array
     */
    public function handle(CreateTokenCommand $command): array
    {
        $data = [];

        try {
            $config = $command->getConfiguration();

            $now = new DateTimeImmutable('now');
            $future = new DateTimeImmutable($config['lifespan']);
            $sub = $config['php.auth.user'] ?? null;
            $jti = (new Base62())->encode(random_bytes(16));

            $payload = array_filter([
                'iat' => $now->getTimestamp(),
                'exp' => $future->getTimestamp(),
                'jti' => $jti,
                'sub' => $sub,
                'scope' => $config['scopes'],
                'user_id' => $config['user_id'] ?? null,
                'role' => $config['role'] ?? null,
                'website_id' => $config['website_id'] ?? null,
                'avatar' => $config['avatar'] ?? null,
            ]);

            $secret = getenv('JWT_SECRET');
            $token = JWT::encode($payload, $secret);

            $data['token'] = $token;
            $data['expires'] = $future->getTimestamp();
            $data['role'] = $payload['role'];
            $data['user_id'] = $payload['user_id'];
            $data['avatar'] = $payload['avatar'];
        } catch (Exception $e) {
            throw new CreateTokenException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $data;
    }
}
