<?php

declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Generic;

class ReadCollectionQuery
{
    public $searchString = null;
    public $sortString = 'id';
    public $page = 1;
    public $per_page = 10;

    public function __construct($data)
    {
        if (!empty($data['page'])) {
            $this->page = (int)$data['page'];
        }

        if (!empty($data['per-page'])) {
            $this->per_page = (int)$data['per-page'];
        }

        if (!empty($data['sort'])) {
            $this->sortString = (string)$data['sort'];
        }

        if (!empty($data['query'])) {
            $this->searchString = (string)$data['query'];
        }
    }
}
