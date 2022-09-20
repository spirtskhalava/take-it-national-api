<?php

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Infrastructure\FileSystem;

interface ServiceInterface
{
    /**
     * @param array $params the extra parameters value required for the service to run.
     *
     * @return mixed
     */
    public function run(array $params = []);
}
