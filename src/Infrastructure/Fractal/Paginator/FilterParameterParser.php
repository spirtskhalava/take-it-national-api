<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\Fractal\Paginator;

use Slim\Http\Request;

final class FilterParameterParser
{
    /**
     * @param Request $request
     * @return array
     */
    public function parse(Request $request): array
    {
        $data = [];

        $filter = $request->getParam('filter');
        if (null !== $filter) {
            $params = array_pad(explode(':', $filter, 2), 2, null);

            foreach ($params as $param) {
                if (!empty($param)) {
                    $paramHolder = [];
                    preg_match_all('/([\w]+)(\(([^\)]+)\))?/', $param, $paramHolder);
                    $pipe = array_slice(explode('|', $paramHolder[3][0], 2), 0, 2);

                    $data[$paramHolder[1][0]] = $pipe;
                }
            }
        }

        return $data;
    }
}
