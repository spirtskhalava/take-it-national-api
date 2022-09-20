<?php

namespace App\Application;

class UnAuthenticatedPath
{
    /**
     * @return string[]
     */
    public static function routes(): array
    {
        return [
            '/token',
            '/wstoken',
            '/register-agency',
            '/upload-logo'
        ];
    }
}