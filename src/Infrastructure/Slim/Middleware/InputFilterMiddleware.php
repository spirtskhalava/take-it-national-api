<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Slim\Middleware;

use App\Application\Response\BadRequestResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Container;
use Tuupola\Middleware\DoublePassTrait;
use Tuupola\Middleware\HttpBasicAuthentication\RequestMethodRule;
use Tuupola\Middleware\HttpBasicAuthentication\RequestPathRule;
use Zend\InputFilter\InputFilter;

final class InputFilterMiddleware implements MiddlewareInterface
{
    use DoublePassTrait;

    /**
     * @var \SplStack
     */
    private $rules;
    /**
     * @var string the path where file specs are
     */
    private $specs_path;
    /**
     * @var array
     */
    private $specs;
    /**
     * @var Container
     */
    private $container;

    /**
     * InputFilterMiddleware constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->rules = new \SplStack;

        if (!isset($options['rules'])) {
            $this->rules->push(new RequestMethodRule([
                'ignore' => ['OPTIONS'],
            ]));
        }

        if (null !== $options['path'] ?? null) {
            $this->rules->push(new RequestPathRule([
                'path' => $options['path'],
                'ignore' => $options['ignore'] ?? [],
            ]));
        }
        $this->specs_path = rtrim($options['specs_path'] ?? '', '/');
        $this->specs = $options['specs'] ?? [];

        if (null !== $options['container'] ?? null) {
            $this->container = $options['container'];
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (false === $this->shouldProcess($request)) {
            return $handler->handle($request);
        }

        $filter = $this->getInputFilterSpecName($request);

        if (null === $filter || (!isset($this->specs[$filter]) && !is_file("{$this->specs_path}/{$filter}.php"))) {
            return $handler->handle($request);
        }

        $inputFilter = new InputFilter();

        $container = $this->container;
        if (isset($this->specs[$filter])) {
            foreach ($this->specs[$filter] as $input) {
                $inputFilter->add($input);
            }
        } else {
            $specs = (array)(require "{$this->specs_path}/{$filter}.php");
            foreach ($specs as $input) {
                $inputFilter->add($input);
            }
        }

        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        if (!empty($files['file'])) {
            $data['file'] = $files['file'];
        }

        $inputFilter->setData($data);

        if (!$inputFilter->isValid()) {
            $errors = [];
            foreach ($inputFilter->getInvalidInput() as $field => $error) {
                foreach ($error->getMessages() as $message) {
                    $errors[] = str_replace('Value', ucfirst($field), $message);
                }
            }

            return new BadRequestResponse($errors);
        }

        return $handler->handle($request->withParsedBody($inputFilter->getValues()));
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getInputFilterSpecName(ServerRequestInterface $request): ?string
    {
        $route = $request->getAttribute('route');

        if (!empty($route)) {
            $filter = $route->getArgument('input_filter');
            if (\is_scalar($filter) || \is_string($filter)) {
                return $filter;
            }
        }

        return null;
    }

    /**
     * Test if current request should be authenticated.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function shouldProcess(ServerRequestInterface $request): bool
    {
        /* If any of the rules in stack return false will not authenticate */
        foreach ($this->rules as $callable) {
            if (false === $callable($request)) {
                return false;
            }
        }

        return true;
    }
}
