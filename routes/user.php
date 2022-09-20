<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use App\Application\User\UserClientListGetAction;
use App\Application\User\UserCreatePostAction;
use App\Application\User\UserDeletePostAction;
use App\Application\User\UserGetAction;
use App\Application\User\UserListGetAction;
use App\Application\User\UserProfileGetAction;
use App\Application\User\UserProfileUpdatePutAction;
use App\Application\User\UserUpdatePutAction;
use App\Application\User\UploadAvatarPostAction;
use App\Application\User\UserForgotPasswordPostAction;

$app->group('/users', function () {
    $this->get('', UserListGetAction::class)
        ->setArguments([
            'scopes' => ['app.all'],
        ]);

    $this->get('/{id}', UserGetAction::class)
        ->setArguments([
            'scopes' => ['app.all'],
        ]);

    $this->post('', UserCreatePostAction::class)
        ->setArguments([
            'scopes' => ['app.all'],
            'input_filter' => 'create_user',
        ]);

    $this->delete('/{id}', UserDeletePostAction::class)
        ->setArguments([
            'scopes' => ['app.all'],
        ]);

    $this->map(['PUT', 'PATCH'], '/{id}', UserUpdatePutAction::class)
        ->setArguments([
            'scopes' => ['app.all'],
            'input_filter' => 'update_user',
        ]);

    $this->get('/{id}/clients', UserClientListGetAction::class)
        ->setArguments([
            'scopes' => ['app.all'],
        ]);

    $this->group('/{id}/profile', function () {
        $this->get('', UserProfileGetAction::class)
            ->setArguments([
                'scopes' => ['app.all', 'profile.all'],
            ]);
        $this->map(['PUT', 'PATCH'], '', UserProfileUpdatePutAction::class)
            ->setArguments([
                'scopes' => ['app.all', 'profile.all'],
            ]);

        $this->post('/avatar', UploadAvatarPostAction::class)
            ->setArguments([
                'scopes' => ['app.all', 'profile.all'],
                'input_filter' => 'upload_avatar',
            ]);
    });
});

$app->post('/forgot-password', UserForgotPasswordPostAction::class)
    ->setArguments([
        'scopes' => ['app.all'],
    ]);

//$app->get('/forgot-password/{code}', UserPasswordAction::class)
//    ->setArguments([
//        'scopes' => ['app.all'],
//    ]);

//$app->get('/reset-password', UserPasswordAction::class)
//    ->setArguments([
//        'scopes' => ['app.all'],
//    ]);
