<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\RegisterAgency\RegisterAgencyPostAction;
use App\Application\RegisterAgency\UploadAgencyLogoPostAction;

$app->post('/register-agency', RegisterAgencyPostAction::class);

$app->post('/upload-logo', UploadAgencyLogoPostAction::class);
