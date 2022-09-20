<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Tactitian;

use App\Infrastructure\Exception\UnprocessableEntityException;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDataAwareCommand
{
    /**
     * @var array
     */
    private $data;
    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * CreateWebsiteCommand constructor.
     * @param array $data
     * @throws UnprocessableEntityException
     */
    protected function __construct(array $data)
    {
        try {
            $this->data = $this->getResolver()->resolve($data);
        } catch (Exception $exception) {
            throw new UnprocessableEntityException($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return OptionsResolver
     */
    protected function getResolver(): OptionsResolver
    {
        if (null === $this->resolver) {
            $this->resolver = $this->buildResolver();
        }

        return $this->resolver;
    }

    /**
     * Configures the available options for the command
     * @return OptionsResolver
     */
    abstract protected function buildResolver(): OptionsResolver;
}
